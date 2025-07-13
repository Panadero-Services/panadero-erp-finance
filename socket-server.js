// socket-server.js
import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';
import cors from 'cors';

const app = express();  // Create Express app
const PORT = 3000;

// Add CORS middleware to Express
app.use(cors({
    origin: "http://localhost:5173",
    credentials: true
}));

const httpServer = createServer(app);  // Create HTTP server with Express

const io = new Server(httpServer, {
    cors: {
        origin: "http://localhost:5173",
        methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        allowedHeaders: ["Content-Type", "Authorization", "X-Requested-With"],
        credentials: true,
        transports: ['websocket', 'polling']
    }
});

// Add middleware to handle CORS preflight
io.engine.on("headers", (headers, req) => {
    headers["Access-Control-Allow-Origin"] = "http://localhost:5173";
    headers["Access-Control-Allow-Credentials"] = true;
});

// Update game constants to match client
const GAME_WIDTH = 800;  // Match client canvas size
const GAME_HEIGHT = 600; // Match client canvas size
const TICK_RATE = 60;

// Add these at the top of the script
const UPDATE_INTERVAL = 100; // Send updates every 100ms
let lastState = null;
let updateInterval = null;

// Add connection tracking
const activeConnections = new Set();

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
    activeConnections.add(socket.id);
    
    gameState.players.set(socket.id, {
        id: socket.id,
        x: Math.random() * GAME_WIDTH,
        y: Math.random() * GAME_HEIGHT,
        rotation: 0,
        velocity: { x: 0, y: 0 },
        thrust: false
    });

    // Broadcast initial state to all clients
    io.emit('player_state', Object.fromEntries(gameState.players));

    // Handle state updates from clients
    socket.on('update_state', (state) => {
        if (gameState.players.has(socket.id)) {
            const player = gameState.players.get(socket.id);
            Object.assign(player, state);
            // Broadcast updated state to all clients
            io.emit('player_state', Object.fromEntries(gameState.players));
        }
    });

    socket.on('disconnect', () => {
        console.log('Player disconnected:', socket.id);
        activeConnections.delete(socket.id);
        gameState.players.delete(socket.id);
        io.emit('player_state', Object.fromEntries(gameState.players));
    });
});

// Start the server
httpServer.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
});