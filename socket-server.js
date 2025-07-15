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
const SHIP_RADIUS = 15; // Collision radius
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

class ServerShip {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.angle = -Math.PI / 2; // Start facing upward
        this.velocity = { x: 0, y: 0 };
        this.rotatingLeft = false;
        this.rotatingRight = false;
        this.engineOn = false;
        this.color = SHIP_COLORS[Math.floor(Math.random() * SHIP_COLORS.length)];
        this.radius = SHIP_RADIUS;
    }

    update(deltaTime) {
        const dt = Math.min(deltaTime, 32) / 16;

        // Rotation
        if (this.rotatingLeft) this.angle -= ROTATION_SPEED * dt;
        if (this.rotatingRight) this.angle += ROTATION_SPEED * dt;

        // Thrust - FIXED: Subtract PI/2 to correct the 180-degree offset
        if (this.engineOn) {
            const thrustAngle = this.angle - Math.PI/2; // Subtract 90 degrees to correct the offset
            this.velocity.x += Math.cos(thrustAngle) * THRUST_POWER * dt;
            this.velocity.y += Math.sin(thrustAngle) * THRUST_POWER * dt;
        }

        // Apply friction
        this.velocity.x *= FRICTION;
        this.velocity.y *= FRICTION;

        // Limit max speed
        const speed = Math.sqrt(this.velocity.x ** 2 + this.velocity.y ** 2);
        if (speed > MAX_SPEED) {
            const scale = MAX_SPEED / speed;
            this.velocity.x *= scale;
            this.velocity.y *= scale;
        }

        // Update position
        this.x += this.velocity.x;
        this.y += this.velocity.y;

        // Wrap around screen edges
        this.x = ((this.x % GAME_WIDTH) + GAME_WIDTH) % GAME_WIDTH;
        this.y = ((this.y % GAME_HEIGHT) + GAME_HEIGHT) % GAME_HEIGHT;
    }

    // Add collision detection method
    checkCollision(otherShip) {
        const dx = this.x - otherShip.x;
        const dy = this.y - otherShip.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        return distance < (this.radius + otherShip.radius);
    }

    // Add collision response method
    handleCollision(otherShip) {
        // Calculate collision normal
        const dx = otherShip.x - this.x;
        const dy = otherShip.y - this.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        const nx = dx / distance;
        const ny = dy / distance;

        // Separate ships
        const overlap = (this.radius + otherShip.radius) - distance;
        this.x -= (overlap/2) * nx;
        this.y -= (overlap/2) * ny;
        otherShip.x += (overlap/2) * nx;
        otherShip.y += (overlap/2) * ny;

        // Elastic collision response
        const relativeVelocityX = this.velocity.x - otherShip.velocity.x;
        const relativeVelocityY = this.velocity.y - otherShip.velocity.y;
        const impulse = -(2 * (relativeVelocityX * nx + relativeVelocityY * ny)) / 2;

        this.velocity.x -= impulse * nx;
        this.velocity.y -= impulse * ny;
        otherShip.velocity.x += impulse * nx;
        otherShip.velocity.y += impulse * ny;
    }

    getState() {
        return {
            position: { x: this.x, y: this.y },
            angle: this.angle,
            velocity: this.velocity,
            controls: {
                rotatingLeft: this.rotatingLeft,
                rotatingRight: this.rotatingRight,
                engineOn: this.engineOn
            },
            color: this.color  // Add color to state
        };
    }
}

// Update the game state object
const gameState = {
    players: new Map(),
    lastUpdate: Date.now()
};

// Add game loop
function gameLoop() {
    const currentTime = Date.now();
    const deltaTime = currentTime - gameState.lastUpdate;
    gameState.lastUpdate = currentTime;

    // Update all players
    for (const [id, player] of gameState.players) {
        player.ship.update(deltaTime);
    }

    // Check collisions between all pairs of ships
    const players = Array.from(gameState.players.entries());
    for (let i = 0; i < players.length; i++) {
        for (let j = i + 1; j < players.length; j++) {
            const shipA = players[i][1].ship;
            const shipB = players[j][1].ship;
            
            if (shipA.checkCollision(shipB)) {
                shipA.handleCollision(shipB);
            }
        }
    }

    // Broadcast state to all players
    const gameStateUpdate = {
        players: Object.fromEntries(
            Array.from(gameState.players.entries()).map(([id, player]) => [
                id,
                player.ship.getState()
            ])
        ),
        status: 'running'
    };
    
    io.emit('game_state', gameStateUpdate);
}

// Start game loop
setInterval(gameLoop, 1000 / TICK_RATE);

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
    
    // Initialize player with ship and color
    gameState.players.set(socket.id, {
        ship: new ServerShip(
            Math.random() * GAME_WIDTH,
            Math.random() * GAME_HEIGHT
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
        
        // Update ship controls based on input
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
        }
    });

    socket.on('disconnect', (reason) => {
        console.log('Player disconnected:', socket.id, 'Reason:', reason);
        gameState.players.delete(socket.id);
    });
});

// Start the server
httpServer.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
    console.log('Allowed origins:', ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"]);
});