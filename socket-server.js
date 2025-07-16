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
// Update the SHIP_COLORS array
const SHIP_COLORS = [
    '#00FFFF', // Cyan
    '#FF69B4', // Pink
    '#FFFF00'  // Yellow
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

// Add ship patterns at the top with other constants
const SHIP_PATTERNS = {
    FIGHTER: 'fighter',
    UFO: 'ufo'
};

// Define ServerShip class BEFORE it's used
class ServerShip {
    constructor(x, y, playerId) {
        this.id = playerId;
        this.homeX = x;
        this.homeY = y;
        this.x = x;
        this.y = y;
        this.angle = -Math.PI / 2;
        this.velocity = { x: 0, y: 0 };
        this.rotatingLeft = false;
        this.rotatingRight = false;
        this.engineOn = false;
        this.pattern = Math.random() < 0.5 ? SHIP_PATTERNS.FIGHTER : SHIP_PATTERNS.UFO;
        this.color = SHIP_COLORS[Math.floor(Math.random() * SHIP_COLORS.length)];
        this.radius = SHIP_RADIUS;
        this.isColliding = false;
        
        // Add debug log
        console.log(`New ship created for player ${playerId} with pattern: ${this.pattern}`);
    }

    update(deltaTime) {
        const dt = Math.min(deltaTime, 32) / 16;

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

        const nextX = this.x + this.velocity.x;
        const nextY = this.y + this.velocity.y;
        
        // First check ship-to-ship collisions
        let shipCollision = false;
        for (const [otherId, otherPlayer] of gameState.players) {
            if (otherId === this.id) continue; // Skip self
            
            const otherShip = otherPlayer.ship;
            if (!otherShip) continue;
            
            // If same color (same team), allow passage
            if (otherShip.color === this.color) continue;
            
            // Check collision with other ship
            const dx = nextX - otherShip.x;
            const dy = nextY - otherShip.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < SHIP_RADIUS * 2) {  // If ships are colliding
                // Bounce off each other
                this.velocity.x = -this.velocity.x;
                this.velocity.y = -this.velocity.y;
                shipCollision = true;
                break;
            }
        }
        
        if (shipCollision) {
            // Move slightly away from collision point
            const BOUNCE_OFFSET = 5;
            this.x += Math.cos(this.angle) * BOUNCE_OFFSET;
            this.y += Math.sin(this.angle) * BOUNCE_OFFSET;
            return;
        }

        // Then check safe zone collisions (existing code)
        for (const [homeId, homePos] of gameState.homePositions) {
            if (homeId === this.id) continue;
            
            const homeOwnerShip = gameState.players.get(homeId)?.ship;
            if (!homeOwnerShip) continue;
            
            if (homeOwnerShip.color === this.color) continue;
            
            const dx = Math.abs(nextX - homePos.x);
            const dy = Math.abs(nextY - homePos.y);
            
            if (dx <= 150 && dy <= 150) {
                this.velocity.x = -this.velocity.x;
                this.velocity.y = -this.velocity.y;
                
                const angle = Math.atan2(this.y - homePos.y, this.x - homePos.x);
                const SAFE_OFFSET = 5;
                this.x += Math.cos(angle) * SAFE_OFFSET;
                this.y += Math.sin(angle) * SAFE_OFFSET;
                return;
            }
        }

        this.x = nextX;
        this.y = nextY;
    }

    getState() {
        return {
            position: { x: this.x, y: this.y },
            home: { x: this.homeX, y: this.homeY },
            angle: this.angle,
            velocity: this.velocity,
            controls: {
                rotatingLeft: this.rotatingLeft,
                rotatingRight: this.rotatingRight,
                engineOn: this.engineOn
            },
            color: this.color,
            pattern: this.pattern,
            isColliding: this.isColliding
        };
    }

    warpHome() {
        this.x = this.homeX;
        this.y = this.homeY;
        this.velocity = { x: 0, y: 0 };
        this.angle = -Math.PI / 2;
    }

    checkCollision(otherShip) {
        const dx = this.x - otherShip.x;
        const dy = this.y - otherShip.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        return distance < (this.radius + otherShip.radius) * 2;
    }
}

// Then game state
const gameState = {
    players: new Map(),
    collectibles: new Map(),
    homePositions: new Map(),
    lastUpdate: Date.now()
};

// Function to create a home position
function createHomePosition(id) {
    const y = 100;  // Fixed Y position
    
    // Find all existing X positions
    const existingXPositions = Array.from(gameState.homePositions.values()).map(pos => pos.x);
    
    // Start at X = 1000 (doubled from original 500) and find first available position with 1000 unit gap
    let x = 1000;
    while (existingXPositions.some(existingX => Math.abs(existingX - x) < 1000)) {
        x += 1000;
    }
    
    return { x, y };
}

// Add function to check if a position is in any safe zone
function isInSafeZone(x, y, playerId) {
    for (const [homeId, homePos] of gameState.homePositions) {
        if (homeId === playerId) continue; // Skip player's own safe zone
        
        // Check if position is within another player's safe zone (300 units)
        const dx = Math.abs(x - homePos.x);
        const dy = Math.abs(y - homePos.y);
        if (dx <= 150 && dy <= 150) { // 300/2 = 150 units from center
            return true;
        }
    }
    return false;
}

// Update the bounce handling function
function handleSafeZoneBounce(ship, homePos) {
    // Calculate distance from ship to safe zone center
    const dx = ship.x - homePos.x;
    const dy = ship.y - homePos.y;
    
    // Calculate angle of impact
    const angle = Math.atan2(dy, dx);
    
    // Simply reverse direction while maintaining speed
    ship.velocity.x = -ship.velocity.x;
    ship.velocity.y = -ship.velocity.y;
    
    // Move ship slightly outside the safe zone to prevent getting stuck
    const SAFE_OFFSET = 5; // Small offset to ensure ship is clear of boundary
    ship.x += Math.cos(angle) * SAFE_OFFSET;
    ship.y += Math.sin(angle) * SAFE_OFFSET;
}

// Update ship movement handling to make ships bounce off safe zones
function updateShipPosition(playerId, ship) {
    const nextX = ship.x + ship.velocity.x;
    const nextY = ship.y + ship.velocity.y;
    
    // Check each safe zone for collision
    for (const [homeId, homePos] of gameState.homePositions) {
        if (homeId === playerId) continue; // Skip player's own safe zone
        
        // Get the home owner's ship color
        const homeOwnerShip = gameState.players.get(homeId)?.ship;
        if (!homeOwnerShip) continue;
        
        // If same color (same team), allow passage
        if (homeOwnerShip.color === ship.color) continue;
        
        // Check if next position would be in another player's safe zone
        const dx = Math.abs(nextX - homePos.x);
        const dy = Math.abs(nextY - homePos.y);
        
        if (dx <= 150 && dy <= 150) { // 300/2 = 150 units from center
            // Handle bounce by simply reversing direction
            handleSafeZoneBounce(ship, homePos);
            return false; // Movement was modified
        }
    }
    
    // No collision or same team, allow normal movement
    ship.x = nextX;
    ship.y = nextY;
    return true;
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

    // Update all players
    for (const [id, player] of gameState.players) {
        if (player && player.ship) {
            player.ship.update(deltaTime);
        }
    }

    // Check collectible collisions
    for (const [playerId, player] of gameState.players) {
        if (!player || !player.ship) continue;
        
        for (const [collectibleId, collectible] of gameState.collectibles) {
            if (checkCollectibleCollision(player.ship, collectible)) {
                gameState.collectibles.delete(collectibleId);
                console.log(`Player ${playerId} collected ${collectible.type}`);
            }
        }
    }

    // Send game state to players
    for (const [playerId, player] of gameState.players) {
        if (!player || !player.ship) continue;
        
        const playerShip = player.ship;
        
        // Filter visible objects
        const visibleHomes = {};
        for (const [homeId, home] of gameState.homePositions) {
            if (isInRange(playerShip.x, playerShip.y, home.x, home.y, VISIBILITY_RANGE)) {
                visibleHomes[homeId] = {
                    ...home,
                    color: gameState.players.get(homeId)?.ship?.color
                };
            }
        }

        const visibleCollectibles = {};
        for (const [collectibleId, collectible] of gameState.collectibles) {
            if (isInRange(playerShip.x, playerShip.y, collectible.x, collectible.y, VISIBILITY_RANGE)) {
                visibleCollectibles[collectibleId] = collectible;
            }
        }

        // Send filtered state
        const playerGameState = {
            players: Object.fromEntries(
                Array.from(gameState.players.entries())
                    .filter(([_, p]) => p && p.ship)
                    .map(([id, p]) => [id, p.ship.getState()])
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
    origin: ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000", "http://192.168.2.20:8000"],
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    credentials: true
}));

const httpServer = createServer(app);

// More detailed Socket.IO configuration
const io = new Server(httpServer, {
    cors: {
        origin: ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000", "http://192.168.2.20:8000"],
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
    
    // Initialize player with ID
    gameState.players.set(socket.id, {
        ship: new ServerShip(
            homePosition.x,
            homePosition.y,
            socket.id  // Pass the socket ID to the ship constructor
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