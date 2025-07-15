// socket-server.js
import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';
import cors from 'cors';

const app = express();
const PORT = 3000;

// Add these constants at the top of socket-server.js
const ROTATION_SPEED = 0.1;    // Radians per frame
const THRUST_POWER = 0.2;      // Reduced for better control
const FRICTION = 0.99;         // Velocity multiplier per frame
const MAX_SPEED = 5;           // Maximum velocity
const GAME_WIDTH = 800;
const GAME_HEIGHT = 600;
const TICK_RATE = 60; // Physics updates per second

// Add at the top with other constants
const HOME_Y_RANGE = { MIN: 5, MAX: 50 };
const HOME_X_RANGE = { MIN: 0, MAX: 1000 };

// Update existing constants
const HOME_BOX_SIZE = 600; // Much larger box size
const VISIBILITY_RANGE = 1000; // Keep this the same or adjust as needed

// Generate a new home position when needed
const generateNewHomePosition = (existingPositions) => {
    const y = Math.floor(Math.random() * (HOME_Y_RANGE.MAX - HOME_Y_RANGE.MIN)) + HOME_Y_RANGE.MIN;
    let x;
    
    do {
        x = Math.floor(Math.random() * (HOME_X_RANGE.MAX - HOME_X_RANGE.MIN)) + HOME_X_RANGE.MIN;
    } while (existingPositions.some(pos => Math.abs(pos.x - x) < 100)); // Ensure some minimum spacing

    return { x, y };
};

const SHIP_RADIUS = 10; // Changed from 15
const SHIP_COLORS = [
    '#FF0000', // Red
    '#00FF00', // Green
    '#0000FF', // Blue
    '#FFFF00', // Yellow
    '#FF00FF', // Magenta
    '#00FFFF', // Cyan
    '#FFA500', // Orange
    '#800080'  // Purple
];

const COLLECTIBLE_TYPES = [
    { type: 'star', color: '#FFD700', points: 100, radius: 15 },
    { type: 'diamond', color: '#00FFFF', points: 200, radius: 15 },
    { type: 'orb', color: '#FF00FF', points: 300, radius: 15 }
];

const COLLECTIBLE_SPAWN_TIME = {
    MIN: 30000,  // 30 seconds
    MAX: 120000  // 2 minutes
};

// Remove screen wrapping from ServerShip class
class ServerShip {
    constructor(x, y) {
        // Add home position to existing constructor
        this.homeX = x;  // Store initial spawn as home
        this.homeY = y;
        this.x = x;
        this.y = y;
        this.angle = -Math.PI / 2; // Start facing upward
        this.velocity = { x: 0, y: 0 };
        this.rotatingLeft = false;
        this.rotatingRight = false;
        this.engineOn = false;
        this.color = SHIP_COLORS[Math.floor(Math.random() * SHIP_COLORS.length)];
        this.radius = SHIP_RADIUS;
        this.isColliding = false;
    }

    update(deltaTime) {
        const dt = Math.min(deltaTime, 32) / 16;

        // Normal movement code without collision response
        if (this.rotatingLeft) this.angle -= ROTATION_SPEED * dt;
        if (this.rotatingRight) this.angle += ROTATION_SPEED * dt;

        if (this.engineOn) {
            const thrustAngle = this.angle - Math.PI/2;
            this.velocity.x += Math.cos(thrustAngle) * THRUST_POWER * dt;
            this.velocity.y += Math.sin(thrustAngle) * THRUST_POWER * dt;
        }

        this.velocity.x *= FRICTION;
        this.velocity.y *= FRICTION;

        const speed = Math.sqrt(this.velocity.x ** 2 + this.velocity.y ** 2);
        if (speed > MAX_SPEED) {
            const scale = MAX_SPEED / speed;
            this.velocity.x *= scale;
            this.velocity.y *= scale;
        }

        // Just update position without wrapping
        this.x += this.velocity.x;
        this.y += this.velocity.y;
    }

    checkCollision(otherShip) {
        const dx = this.x - otherShip.x;
        const dy = this.y - otherShip.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        this.isColliding = distance < (this.radius + otherShip.radius);
        return this.isColliding;
    }

    // Add warp method
    warpHome() {
        // Instantly move to home position
        this.x = this.homeX;
        this.y = this.homeY;
        // Reset velocity to prevent drifting after warp
        this.velocity = { x: 0, y: 0 };
        // Reset angle to face upward (optional)
        this.angle = -Math.PI / 2;
    }

    getState() {
        return {
            position: { x: this.x, y: this.y },
            home: { x: this.homeX, y: this.homeY },  // Added this line
            angle: this.angle,
            velocity: this.velocity,
            controls: {
                rotatingLeft: this.rotatingLeft,
                rotatingRight: this.rotatingRight,
                engineOn: this.engineOn
            },
            color: this.color,
            isColliding: this.isColliding
        };
    }
}

// Add to game state (with existing players and collectibles)
const gameState = {
    players: new Map(),
    collectibles: new Map(),
    homePositions: new Map(),
    lastUpdate: Date.now()
};

// Function to create a home position
function createHomePosition(id) {
    const y = Math.floor(Math.random() * (45)) + 5;  // Between 5 and 50
    const x = Math.floor(Math.random() * 1000);      // Between 0 and 1000
    
    return {
        id,
        x,
        y,
        size: HOME_BOX_SIZE, // Use the larger size
        color: gameState.players.get(id)?.ship.color // Match player's color
    };
}

// Add collectible management functions
function spawnCollectible() {
    const id = 'collectible_' + Date.now();
    const type = COLLECTIBLE_TYPES[Math.floor(Math.random() * COLLECTIBLE_TYPES.length)];
    
    const collectible = {
        id,
        x: Math.random() * GAME_WIDTH,
        y: Math.random() * GAME_HEIGHT,
        ...type,
        createdAt: Date.now()
    };

    gameState.collectibles.set(id, collectible);
    console.log('Spawned collectible:', collectible);
    
    // Schedule next spawn
    const nextSpawnDelay = Math.random() * 
        (COLLECTIBLE_SPAWN_TIME.MAX - COLLECTIBLE_SPAWN_TIME.MIN) + 
        COLLECTIBLE_SPAWN_TIME.MIN;
    
    setTimeout(spawnCollectible, nextSpawnDelay);
}

// Add collision check for collectibles
function checkCollectibleCollision(ship, collectible) {
    const dx = ship.x - collectible.x;
    const dy = ship.y - collectible.y;
    const distance = Math.sqrt(dx * dx + dy * dy);
    return distance < (ship.radius + collectible.radius);
}

function isInRange(playerX, playerY, objectX, objectY, range) {
    const dx = playerX - objectX;
    const dy = playerY - objectY;
    return Math.sqrt(dx * dx + dy * dy) <= range;
}

// Update the game loop to include collectibles
function gameLoop() {
    const currentTime = Date.now();
    const deltaTime = currentTime - gameState.lastUpdate;
    gameState.lastUpdate = currentTime;

    // Reset collision states
    for (const [id, player] of gameState.players) {
        player.ship.isColliding = false;
    }

    // Update all players
    for (const [id, player] of gameState.players) {
        player.ship.update(deltaTime);
    }

    // Only check for collisions, no physics response
    const players = Array.from(gameState.players.entries());
    for (let i = 0; i < players.length; i++) {
        for (let j = i + 1; j < players.length; j++) {
            const shipA = players[i][1].ship;
            const shipB = players[j][1].ship;
            
            if (shipA.checkCollision(shipB)) {
                shipA.isColliding = true;
                shipB.isColliding = true;
            }
        }
    }

    // Check collectible collisions
    for (const [playerId, player] of gameState.players) {
        for (const [collectibleId, collectible] of gameState.collectibles) {
            if (checkCollectibleCollision(player.ship, collectible)) {
                // Remove collected item
                gameState.collectibles.delete(collectibleId);
                console.log(`Player ${playerId} collected ${collectible.type}`);
            }
        }
    }

    // For each player, create a filtered game state with only visible objects
    for (const [playerId, player] of gameState.players) {
        const playerShip = player.ship;
        
        // Filter visible home positions
        const visibleHomes = {};
        for (const [homeId, home] of gameState.homePositions) {
            if (isInRange(playerShip.x, playerShip.y, home.x, home.y, VISIBILITY_RANGE)) { // Assuming VISIBILITY_RANGE is 1000
                visibleHomes[homeId] = {
                    ...home,
                    color: gameState.players.get(homeId)?.ship.color // Match home color to player color
                };
            }
        }

        // Filter visible collectibles
        const visibleCollectibles = {};
        for (const [collectibleId, collectible] of gameState.collectibles) {
            if (isInRange(playerShip.x, playerShip.y, collectible.x, collectible.y, VISIBILITY_RANGE)) { // Assuming VISIBILITY_RANGE is 1000
                visibleCollectibles[collectibleId] = collectible;
            }
        }

        // Send filtered state to this player
        const playerGameState = {
            players: Object.fromEntries(
                Array.from(gameState.players.entries()).map(([id, p]) => [
                    id,
                    p.ship.getState()
                ])
            ),
            homePositions: visibleHomes,
            collectibles: visibleCollectibles,
            status: 'running'
        };
        
        io.to(playerId).emit('game_state', playerGameState);
    }
}

// Start game loop
setInterval(gameLoop, 1000 / TICK_RATE);

// Start spawning collectibles when server starts
spawnCollectible();

// More permissive CORS for Express
app.use(cors({
    origin: ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"],
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    credentials: true
}));

const httpServer = createServer(app);

// More detailed Socket.IO configuration
const io = new Server(httpServer, {
    cors: {
        origin: ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"],
        methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        allowedHeaders: ["Content-Type", "Authorization", "X-Requested-With"],
        credentials: true,
        transports: ['websocket', 'polling']
    },
    pingTimeout: 60000,
    pingInterval: 25000
});

// Debug middleware
io.use((socket, next) => {
    console.log('Connection attempt from:', socket.handshake.headers.origin);
    console.log('Transport:', socket.conn.transport.name);
    next();
});

// Update the connection handler
io.on('connection', (socket) => {
    console.log('Player connected:', socket.id);

    // Create home position first
    const homePosition = createHomePosition(socket.id);
    gameState.homePositions.set(socket.id, homePosition);
    
    // Initialize player (existing code)
    gameState.players.set(socket.id, {
        ship: new ServerShip(
            homePosition.x,
            homePosition.y
        )
    });

    // Log the assigned color for debugging
    console.log('Player color assigned:', gameState.players.get(socket.id).ship.color);

    // Send initial game state including color
    socket.emit('game_state', {
        id: socket.id,
        status: 'connected',
        players: Object.fromEntries(
            Array.from(gameState.players.entries()).map(([id, player]) => [
                id,
                player.ship.getState()  // This should include the color
            ])
        )
    });

    // Handle player input
    socket.on('player_input', (input) => {
        const player = gameState.players.get(socket.id);
        if (!player) return;

        console.log('Player input:', socket.id, input);
        
        switch(input.type) {
            case 'rotate_left':
                player.ship.rotatingLeft = input.value;
                break;
            case 'rotate_right':
                player.ship.rotatingRight = input.value;
                break;
            case 'thrust':
                player.ship.engineOn = input.value;
                break;
            case 'warp_home':
                player.ship.warpHome();
                break;
        }
    });

    // When player disconnects, remove their home position
    socket.on('disconnect', (reason) => {
        console.log('Player disconnected:', socket.id, 'Reason:', reason);
        gameState.players.delete(socket.id);
        gameState.homePositions.delete(socket.id);
    });
});

// Start the server
httpServer.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
    console.log('Allowed origins:', ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"]);
});