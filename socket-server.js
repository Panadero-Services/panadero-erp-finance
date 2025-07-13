// socket-server.js
import { Server } from 'socket.io';
import { createServer } from 'http';

const PORT = 3000;
const httpServer = createServer();
const io = new Server(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        allowedHeaders: ["Content-Type", "Authorization"],
        credentials: true
    }
});

// Game constants
const GAME_WIDTH = 400;
const GAME_HEIGHT = 400;
const TICK_RATE = 60; // Server updates per second

// Game state
const gameState = {
    players: new Map(),
    bullets: new Map(),
    enemies: new Map(),
    scores: {},
    frame: 0
};

// Game loop
function gameLoop() {
    // Update positions
    gameState.players.forEach((player) => {
        if (player.thrust) {
            // Apply thrust in direction of rotation
            player.velocity.x += Math.cos(player.rotation * Math.PI / 180) * 0.1;
            player.velocity.y += Math.sin(player.rotation * Math.PI / 180) * 0.1;
        }
        
        // Update position
        player.x += player.velocity.x;
        player.y += player.velocity.y;

        // Apply drag
        player.velocity.x *= 0.99;
        player.velocity.y *= 0.99;

        // Wrap around screen
        player.x = (player.x + GAME_WIDTH) % GAME_WIDTH;
        player.y = (player.y + GAME_HEIGHT) % GAME_HEIGHT;
    });

    // Update bullets
    gameState.bullets.forEach((bullet, id) => {
        bullet.x += bullet.velocity.x;
        bullet.y += bullet.velocity.y;

        // Remove bullets that are off screen
        if (bullet.x < 0 || bullet.x > GAME_WIDTH || 
            bullet.y < 0 || bullet.y > GAME_HEIGHT) {
            gameState.bullets.delete(id);
        }
    });

    // Spawn enemies occasionally
    if (Math.random() < 0.02) {
        spawnEnemy();
    }

    // Update enemies
    gameState.enemies.forEach((enemy, id) => {
        // Move enemies
        enemy.x += enemy.velocity.x;
        enemy.y += enemy.velocity.y;

        // Wrap around screen
        enemy.x = (enemy.x + GAME_WIDTH) % GAME_WIDTH;
        enemy.y = (enemy.y + GAME_HEIGHT) % GAME_HEIGHT;
    });

    // Check collisions
    checkCollisions();

    // Broadcast game state to all players
    io.emit('game-state-update', {
        players: Array.from(gameState.players.values()),
        bullets: Array.from(gameState.bullets.values()),
        enemies: Array.from(gameState.enemies.values()),
        scores: gameState.scores
    });

    gameState.frame++;
}

function spawnEnemy() {
    const id = `enemy-${Date.now()}-${Math.random()}`;
    gameState.enemies.set(id, {
        id,
        x: Math.random() * GAME_WIDTH,
        y: Math.random() * GAME_HEIGHT,
        velocity: {
            x: (Math.random() - 0.5) * 2,
            y: (Math.random() - 0.5) * 2
        }
    });
}

function checkCollisions() {
    // Check bullet-enemy collisions
    gameState.bullets.forEach((bullet, bulletId) => {
        gameState.enemies.forEach((enemy, enemyId) => {
            const dx = bullet.x - enemy.x;
            const dy = bullet.y - enemy.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < 20) { // Hit!
                gameState.bullets.delete(bulletId);
                gameState.enemies.delete(enemyId);
                // Award points to shooter
                gameState.scores[bullet.playerId] = 
                    (gameState.scores[bullet.playerId] || 0) + 100;
            }
        });
    });

    // Check player-enemy collisions
    gameState.players.forEach((player) => {
        gameState.enemies.forEach((enemy, enemyId) => {
            const dx = player.x - enemy.x;
            const dy = player.y - enemy.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < 30) { // Collision!
                // Player loses points
                gameState.scores[player.id] = 
                    Math.max(0, (gameState.scores[player.id] || 0) - 50);
                gameState.enemies.delete(enemyId);
            }
        });
    });
}

// Start game loop
setInterval(gameLoop, 1000 / TICK_RATE);

io.on('connection', (socket) => {
    console.log('Player connected:', socket.id);

    // Initialize player
    gameState.players.set(socket.id, {
        id: socket.id,
        x: Math.random() * GAME_WIDTH,
        y: Math.random() * GAME_HEIGHT,
        rotation: 0,
        velocity: { x: 0, y: 0 },
        thrust: false
    });
    gameState.scores[socket.id] = 0;

    // Handle player input
    socket.on('player-input', (input) => {
        const player = gameState.players.get(socket.id);
        if (player) {
            player.rotation = input.rotation;
            player.thrust = input.thrust;
        }
    });

    // Handle shooting
    socket.on('player-shoot', () => {
        const player = gameState.players.get(socket.id);
        if (player) {
            const bulletId = `bullet-${Date.now()}-${Math.random()}`;
            const angle = player.rotation * Math.PI / 180;
            gameState.bullets.set(bulletId, {
                id: bulletId,
                playerId: socket.id,
                x: player.x,
                y: player.y,
                velocity: {
                    x: Math.cos(angle) * 5,
                    y: Math.sin(angle) * 5
                }
            });
        }
    });

    socket.on('disconnect', () => {
        console.log('Player disconnected:', socket.id);
        gameState.players.delete(socket.id);
        // Keep scores in case player reconnects
    });
});

httpServer.listen(PORT, () => {
    console.log(`Game server running on port ${PORT}`);
});