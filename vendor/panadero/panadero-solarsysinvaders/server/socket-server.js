// socket-server.js
import dotenv from 'dotenv';
dotenv.config(); // Load environment variables first

import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';
import cors from 'cors';
import { 
    areShipsColliding, 
    handleShipCollisionBounce,
    handleSafeZoneBounce,
    checkCollectibleCollision,
    checkPathCollision,
    isInRange,
    checkAllShipCollisions,
    checkAllCollectibleCollisions
} from '../shared/collision.js';
import { CHAT_TYPES, formatMessage, createSystemMessage } from '../shared/chat.js';
import { MOVEMENT_CONFIG, SHIP_CONFIGS } from '../shared/movementConfig.js';
import { getShipMovementConfig } from '../shared/movementConfig.js';
import { GAME_CONFIG } from '../shared/gameConfig.js';
import { 
    COLLECTIBLE_TYPES, 
    getRandomCollectibleType, 
    getRandomCollectiblePosition,
    FIELD_CONFIG,
    isValidFieldPosition,
    createOrUpdateField,
    spawnCollectibleInField,
    scheduleNextSpawn,
    checkStreak,
    calculatePoints,
    collectItem
} from '../shared/collectibles.js';
import { calculateBorderForce } from '../shared/movementConfig.js';
import { parseHelpCommand } from '../shared/helpSystem.js';
import fetch from 'node-fetch';

// Now define constants AFTER all imports and dotenv is loaded
const MASTER_SERVER_URL = process.env.MASTER_SERVER_URL ;
const GAME_SERVER_ID = process.env.GAME_SERVER_ID ;
const PORT = process.env.GAME_SERVER_PORT;

// Add debugging
console.log('Environment variables loaded:');
console.log('MASTER_SERVER_URL:', MASTER_SERVER_URL);
console.log('GAME_SERVER_ID:', GAME_SERVER_ID);
console.log('GAME_SERVER_PORT:', PORT);
console.log('Using GAME_SERVER_ID:', GAME_SERVER_ID);

const app = express();

// Add these constants at the top of socket-server.js
const TICK_RATE = 60;  // Physics updates per second
const DATA_RETENTION_TIME = 24 * 60 * 60 * 1000;  // 24 hours in milliseconds
const SCORE_RESET_INTERVAL = 60 * 1000;  // Check score reset every minute

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



const COLLECTIBLE_SPAWN_TIME = {
    MIN: 500,   // 
    MAX: 600    // 
};

// Add ship patterns at the top with other constants
const SHIP_PATTERNS = {
    FIGHTER: 'fighter',
    UFO: 'ufo'
};

// Add team tracking at the top with other game state
const teamCounts = {
    '#00FFFF': 0,  // Cyan
    '#FF69B4': 0,  // Pink
    '#FFFF00': 0   // Yellow
};

// Define the team balancing function BEFORE ServerShip class
function assignBalancedTeam() {
    // Log current state
    console.log('Assigning team. Current counts:', teamCounts);
    
    // Get teams with minimum count
    const minCount = Math.min(...Object.values(teamCounts));
    const teamsWithMinCount = Object.entries(teamCounts)
        .filter(([_, count]) => count === minCount)
        .map(([color, _]) => color);
    
    console.log('Teams with min count:', teamsWithMinCount);
    
    // Always pick from teams with lowest count
    const selectedColor = teamsWithMinCount[Math.floor(Math.random() * teamsWithMinCount.length)];
    console.log('Selected color:', selectedColor);
    
    return selectedColor;
}

// Define ServerShip class BEFORE it's used
class ServerShip {
    constructor(x, y, playerId, self = 'nope') {
        this.id = playerId;
        // Use self name if provided and not 'nope', otherwise use default
        this.callSign = self !== 'nope' ? self : playerId.slice(0, 4).toUpperCase();
        
        // Check for existing data
        const savedData = persistentPlayerData.get(this.callSign);
        
        // Check if we need to reset scores
        if (shouldResetScores(lastScoreReset)) {
            console.log('Performing daily score reset at UTC midnight');
            // Reset scores for all saved players
            for (const [callSign, data] of persistentPlayerData.entries()) {
                data.score = 0;
                data.streak = [];
            }
            lastScoreReset = new Date().toISOString();
        }

        if (savedData) {
            // Restore saved data
            this.score = savedData.score || 0;
            this.collections = savedData.collections || {
                gold: 0,
                water: 0,
                kryptonite: 0
            };
            this.streak = savedData.streak || [];
            console.log(`Restored data for player ${this.callSign}:`, {
                score: this.score,
                collections: this.collections
            });
        } else {
            // Initialize new data
            this.score = 0;
            this.collections = {
                gold: 0,
                water: 0,
                kryptonite: 0
            };
            this.streak = [];
        }

        // Always reset these properties on new connection
        this.health = 100;
        this.maxHealth = 100;
        this.homeX = x;
        this.homeY = y;
        this.x = x;
        this.y = y;
        this.angle = -Math.PI / 2;
        this.velocity = { x: 0, y: 0 };
        this.acceleration = { x: 0, y: 0 };
        this.rotatingLeft = false;
        this.rotatingRight = false;
        this.engineOn = false;
        this.pattern = Math.random() < 0.5 ? SHIP_PATTERNS.FIGHTER : SHIP_PATTERNS.UFO;
        this.color = assignBalancedTeam();
        teamCounts[this.color]++;
        this.radius = SHIP_RADIUS;
        this.isColliding = false;
        this.lastCollisionTime = 0;
        this.collisionCooldown = 500;
        this.lastStreakTime = 0;

        // Log restoration of data
        if (savedData) {
            console.log(`Restored data for player ${this.callSign}:`, {
                score: this.score,
                collections: this.collections
            });
        }
    }

    // Add method to save data
    saveData() {
        persistentPlayerData.set(this.callSign, {
            score: this.score,
            collections: this.collections,
            streak: this.streak,
            lastSeen: Date.now()
        });
        console.log(`Saved data for player ${this.callSign}:`, {
            score: this.score,
            collections: this.collections
        });
    }

    update(deltaTime) {
        const dt = Math.min(deltaTime, 32) / 16;

        // Get the averaged config for this ship's type
        const config = getShipMovementConfig(this.pattern);

        // Rotation
        if (this.rotatingLeft) this.angle -= config.ROTATION_SPEED * dt;
        if (this.rotatingRight) this.angle += config.ROTATION_SPEED * dt;

        // Thrust
        if (this.engineOn) {
            const thrustAngle = this.angle - Math.PI / 2;
            this.velocity.x += Math.cos(thrustAngle) * config.THRUST_POWER * dt;
            this.velocity.y += Math.sin(thrustAngle) * config.THRUST_POWER * dt;
        }

        // Apply border anti-gravity force
        const borderForce = calculateBorderForce(this.x, this.y);
        this.velocity.x += borderForce.x * dt;
        this.velocity.y += borderForce.y * dt;

        // Apply friction
        this.velocity.x *= Math.pow(config.FRICTION, dt);
        this.velocity.y *= Math.pow(config.FRICTION, dt);

        // Enforce speed limit
        const speed = Math.sqrt(this.velocity.x ** 2 + this.velocity.y ** 2);
        if (speed > config.MAX_SPEED) {
            const scale = config.MAX_SPEED / speed;
            this.velocity.x *= scale;
            this.velocity.y *= scale;
        }

        updateShipPosition(this.id, this);
    }

    takeDamage(fromBullet = false) {
        const now = Date.now();
        
        // If it's bullet damage, or if enough time has passed since last collision
        if (fromBullet || (now - this.lastCollisionTime >= this.collisionCooldown)) {
            this.health = Math.max(0, this.health - 1);
            
            // Only update collision time for non-bullet damage
            if (!fromBullet) {
                this.lastCollisionTime = now;
            }
        }
    }

    heal() {
        this.health = this.maxHealth;  // Restore to max health
    }

    getState() {
        return {
            position: { x: this.x, y: this.y },
            home: { x: this.homeX, y: this.homeY },
            angle: this.angle,
            velocity: this.velocity,
            score: this.score,
            controls: {
                rotatingLeft: this.rotatingLeft,
                rotatingRight: this.rotatingRight,
                engineOn: this.engineOn
            },
            color: this.color,
            pattern: this.pattern,
            isColliding: this.isColliding,
            callSign: this.callSign,
            health: this.health,
            maxHealth: this.maxHealth,
            collections: this.collections,
            streak: this.streak  // Add this
        };
    }

    warpHome() {
        this.x = this.homeX;
        this.y = this.homeY;
        this.velocity = { x: 0, y: 0 };
        this.angle = -Math.PI / 2;
    }

    canTakeCollisionDamage() {
        return (Date.now() - this.lastCollisionTime) >= this.collisionCooldown;
    }
}

// At the top with other game state
const gameState = {
    players: new Map(),
    homePositions: new Map(),
    bullets: new Map(),
    collectibles: new Map(),  // Add this line to initialize collectibles
    lastUpdate: Date.now()
};

// Add bullet management functions
// Add rocket upgrade tracking
const playerUpgrades = new Map(); // Track player upgrades

// Add rocket upgrade function
function addRocketUpgrade(playerId) {
    if (!playerUpgrades.has(playerId)) {
        playerUpgrades.set(playerId, {});
    }
    const upgrades = playerUpgrades.get(playerId);
    upgrades.rocket = (upgrades.rocket || 0) + 1;
    console.debug(`🚀 Rocket upgrade added for player ${playerId}, level: ${upgrades.rocket}`);
}

// Enhanced bullet creation with rocket upgrade
function createBullet(playerId, ship, bulletType = 'normal') {
    const upgrades = playerUpgrades.get(playerId) || {};
    const rocketLevel = upgrades.rocket || 0;
    
    let bulletSpeed = ship.pattern === 'ufo' ? 5 : 10;
    let bulletDamage = 1;
    let bulletSize = 1;
    let particleCount = 0;
    
    // Apply rocket effects
    if (bulletType === 'rocket') {
        // Base rocket damage is 3 (not 1)
        bulletDamage = 3 + rocketLevel; // 3 base damage + upgrade bonus
        bulletSpeed *= (1 + rocketLevel * 0.5); // 50% speed increase per level
        bulletSize = 1 + rocketLevel * 0.3; // Bigger bullets
        particleCount = rocketLevel * 3; // More particles per level
    }
    
    const bulletId = `bullet_${playerId}_${Date.now()}`;
    
    // Bullet always shoots FORWARD in ship's direction
    const bulletVelocityX = Math.sin(ship.angle) * bulletSpeed;
    const bulletVelocityY = -Math.cos(ship.angle) * bulletSpeed;
    
    // Add ship's velocity to bullet
    const finalVelocityX = bulletVelocityX + ship.velocity.x;
    const finalVelocityY = bulletVelocityY + ship.velocity.y;
    
    // Ensure minimum forward speed
    const minForwardSpeed = bulletSpeed * 0.5;
    const currentForwardSpeed = Math.sqrt(bulletVelocityX * bulletVelocityX + bulletVelocityY * bulletVelocityY);
    
    let adjustedVelocityX = finalVelocityX;
    let adjustedVelocityY = finalVelocityY;
    
    if (currentForwardSpeed < minForwardSpeed) {
        const scaleFactor = minForwardSpeed / currentForwardSpeed;
        adjustedVelocityX = bulletVelocityX * scaleFactor + ship.velocity.x;
        adjustedVelocityY = bulletVelocityY * scaleFactor + ship.velocity.y;
    }
    
    // Create the bullet with rocket properties
    const bullet = {
        id: bulletId,
        x: ship.x,
        y: ship.y,
        angle: ship.angle,
        speed: bulletSpeed,
        velocity: {
            x: adjustedVelocityX,
            y: adjustedVelocityY
        },
        color: ship.color,
        playerId: playerId,
        created: Date.now(),
        lifespan: 2000,
        type: bulletType,
        damage: bulletDamage,
        size: bulletSize,
        particleCount: particleCount,
        particles: [] // Will be populated for rocket bullets
    };
    
    // Add particles for rocket bullets
    if (bulletType === 'rocket' && particleCount > 0) {
        for (let i = 0; i < particleCount; i++) {
            bullet.particles.push({
                x: ship.x,
                y: ship.y,
                vx: (Math.random() - 0.5) * 2, // Random spread
                vy: (Math.random() - 0.5) * 2,
                life: 30 + Math.random() * 20, // Random life
                maxLife: 30 + Math.random() * 20,
                color: ship.color,
                size: Math.random() * 2 + 1
            });
        }
    }
    
    gameState.bullets.set(bulletId, bullet);
    
    // If it's a UFO, create additional bullets (but only for regular bullets, not rockets)
    if (ship.pattern === 'ufo' && bulletType === 'normal') {
        const angles = [0.2, -0.2]; // Spread angles
        angles.forEach((angleOffset, index) => {
            const bulletId2 = `bullet_${playerId}_${Date.now()}_${index + 2}`;
            const bullet2VelocityX = Math.sin(ship.angle + angleOffset) * bulletSpeed;
            const bullet2VelocityY = -Math.cos(ship.angle + angleOffset) * bulletSpeed;
            const bullet2FinalX = bullet2VelocityX + ship.velocity.x;
            const bullet2FinalY = bullet2VelocityY + ship.velocity.y;
            
            const bullet2 = {
                ...bullet,
                id: bulletId2,
                angle: ship.angle + angleOffset,
                velocity: {
                    x: bullet2FinalX,
                    y: bullet2FinalY
                }
            };
            
            gameState.bullets.set(bulletId2, bullet2);
        });
    }
    
    return bullet;
}

// Enhanced bullet update with particle system
function updateBullets() {
    const now = Date.now();
    for (const [bulletId, bullet] of gameState.bullets) {
        // Move bullet using velocity
        bullet.x += bullet.velocity.x;
        bullet.y += bullet.velocity.y;
        
        // Update particles for rocket bullets
        if (bullet.particles && bullet.particles.length > 0) {
            bullet.particles.forEach(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.life--;
                
                // Add some randomness to particle movement
                particle.vx += (Math.random() - 0.5) * 0.1;
                particle.vy += (Math.random() - 0.5) * 0.1;
            });
            
            // Remove dead particles
            bullet.particles = bullet.particles.filter(particle => particle.life > 0);
        }
        
        // Check if bullet is in any safe zone
        if (isInSafeZone(bullet.x, bullet.y)) {
            gameState.bullets.delete(bulletId);
            continue;
        }
        
        // Remove expired bullets
        if (now - bullet.created > bullet.lifespan) {
            gameState.bullets.delete(bulletId);
            continue;
        }
        
        // Check collisions with ships
        for (const [playerId, player] of gameState.players) {
            if (player.ship && bullet.playerId !== playerId) {
                const dx = bullet.x - player.ship.x;
                const dy = bullet.y - player.ship.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < SHIP_RADIUS) {
                    // Apply damage based on bullet type
                    const damage = bullet.damage || 1;
                    for (let i = 0; i < damage; i++) {
                        player.ship.takeDamage(true);
                    }
                    gameState.bullets.delete(bulletId);
                    break;
                }
            }
        }
    }
}

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
        // Check if position is within any safe zone (300 units)
        const dx = Math.abs(x - homePos.x);
        const dy = Math.abs(y - homePos.y);
        if (dx <= 150 && dy <= 150) { // 300/2 = 150 units from center
            return true;
        }
    }
    return false;
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


// Resource fields storage
const resourceFields = new Map();



// Now initialize the fields
console.log('\n=== Initializing Resource Fields ===');
for (let i = 0; i < FIELD_CONFIG.COUNT; i++) {
    createOrUpdateField(i, resourceFields);
}

// Black holes storage
const blackHoles = new Map();

// Black hole configuration (similar to FIELD_CONFIG)
const BLACK_HOLE_CONFIG = {
    COUNT: 1,  // Start with one black hole
    POSITIONS: [
        { x: -1000, y: -1000 }
    ]
};

// Initialize black holes
console.log('\n=== Initializing Black Holes ===');
for (let i = 0; i < BLACK_HOLE_CONFIG.COUNT; i++) {
    blackHoles.set(`bh${i}`, {
        id: `bh${i}`,
        x: BLACK_HOLE_CONFIG.POSITIONS[i].x,
        y: BLACK_HOLE_CONFIG.POSITIONS[i].y
    });
}
console.log('Black holes initialized:', Object.fromEntries(blackHoles)); // Add this line


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

    // Update bullets
    updateBullets();

    // Check ship-to-ship collisions with bouncing
    checkAllShipCollisions(gameState.players, (playerId, collisionData) => {
        io.to(playerId).emit('collision', collisionData);
    });

    // Check collectible collisions
    checkAllCollectibleCollisions(gameState.players, gameState.collectibles, (playerId, result, collectible) => {
        // Get player data
        const player = gameState.players.get(playerId);
        const playerName = player?.name || `Player_${playerId.substring(0, 8)}`;
        
        // Log collection with player name
        console.log(`Player ${playerName} collected ${collectible.type}:`, result);
        
        // Update shared state with Master Server ONLY when resources change
        if (player) {
            const currentResources = player.resources || { gold: 0, water: 0, kryptonite: 0 };
            const currentScore = player.score || 0;
            
            // Update local resources
            currentResources[collectible.type] = (currentResources[collectible.type] || 0) + 1;
            const newScore = currentScore + result.total;
            
            player.resources = currentResources;
            player.score = newScore;
            
            // Update Master Server (SSOT) with new state
            updateSharedPlayerState(playerName, {
                name: playerName,
                resources: currentResources,
                score: newScore
            }).then(success => {
                if (success) {
                    console.log(`Updated SSOT for ${playerName}:`, { resources: currentResources, score: newScore });
                } else {
                    console.error(`Failed to update SSOT for ${playerName}`);
                }
            });
        }
        
        // Notify player
        io.to(playerId).emit('collectible_collected', result);
    }, collectItem);

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
            bullets: Array.from(gameState.bullets.values())  // Add this line
                .filter(bullet => isInRange(  // Only send bullets in range
                    playerShip.x, 
                    playerShip.y, 
                    bullet.x, 
                    bullet.y, 
                    VISIBILITY_RANGE
                )),
            blackHoles: Object.fromEntries(blackHoles), // Add this line
            status: 'running'
        };
        
        // Add this debug log (but only log once every 5 seconds to avoid spam)
        if (Date.now() % 5000 < 16) {  // Will log roughly every 5 seconds
            console.log('Sending game state with black holes:', 
                JSON.stringify(playerGameState.blackHoles));
        }
        io.to(playerId).emit('game_state', playerGameState);
    }
}

// Start game loop
setInterval(gameLoop, 1000 / TICK_RATE);


// Replace the old function call with:
scheduleNextSpawn(gameState, resourceFields, (newCollectible) => {
    // Optional: Add server-specific logic here (e.g., emit to clients)
    // io.emit('collectibleSpawned', newCollectible);
});

// More permissive CORS for Express
app.use(cors({
    origin: [
        "http://localhost:8000",
        "http://localhost:5173", 
        "http://127.0.0.1:8000",
        "https://self-api.com"    // Add this
    ],
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    credentials: true
}));

const httpServer = createServer(app);

// More detailed Socket.IO configuration
const io = new Server(httpServer, {
    cors: {
        origin: [
            "https://self-api.com" ,
            "http://localhost:8000", 
            "http://localhost:5173", 
            "http://127.0.0.1:8000", 
            "http://84.80.133.32:3000",
            "http://84.80.133.32:3001",  // Add your AWS IP
            "http://84.80.133.32:8000",   // Add your AWS IP
            "http://192.168.2.20:8000"  // Add the LAN IP
        ],
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

// Add this function to create/register player with Master Server
const registerPlayerWithMaster = async (socketId, playerName) => {
    try {
        // Only create, don't update existing
        const response = await fetch(`${MASTER_SERVER_URL}/master/players/state/${playerName}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: playerName,
                create_only: true  // Only send the flag, no default values
            })
        });
        
        if (response.ok) {
            console.log(`Player ${playerName} registered with Master Server`);
            return true;
        }
    } catch (error) {
        console.error('Failed to register player with Master Server:', error);
    }
    return false;
};

// Modify the connection handler
io.on('connection', (socket) => {
    console.log('Player connected:', socket.id);

    // Get self parameter from handshake data
    const self = socket.handshake.query.self || 'nope';
    
    // Use self as player identifier if available, otherwise generate one
    const playerName = self !== 'nope' ? self : `Player_${socket.id.substring(0, 8)}`;
    
    console.log(`Player identified as: ${playerName} (self: ${self})`);
    
    // Register player with Master Server
    registerPlayerWithMaster(socket.id, playerName);

    // Create home position first
    const homePosition = createHomePosition(socket.id);
    gameState.homePositions.set(socket.id, homePosition);
    
    // Initialize player with ID and self parameter
    gameState.players.set(socket.id, {
        ship: new ServerShip(
            homePosition.x,
            homePosition.y,
            socket.id,
            self
        ),
        name: playerName,
        resources: { gold: 0, water: 0, kryptonite: 0 },
        score: 0
    });

    // Load from Master Server (SSOT) - but don't overwrite DB record
    getSharedPlayerState(playerName).then(sharedState => {
        if (sharedState && sharedState.resources) {
            const player = gameState.players.get(socket.id);
            player.resources = sharedState.resources;
            player.score = sharedState.score;
            
            // Emit SSOT data immediately (remove the setTimeout)
            socket.emit('update-player-resources', {
                resources: sharedState.resources,
                score: sharedState.score
            });

            console.log('=== SERVER EMITTING SSOT DATA (IMMEDIATE) ===');
            console.log('Emitting to socket:', socket.id);
            console.log('Data:', sharedState);
            
            console.log(`Loaded SSOT data for ${playerName}:`, sharedState);
        }
    });

    // Emit SSOT data immediately after the promise (not inside it)
    socket.emit('update-player-resources', {
        resources: { gold: 0, water: 0, kryptonite: 0 }, // Default values
        score: 0
    });

    console.log('=== SERVER EMITTING SSOT DATA (IMMEDIATE) ===');
    console.log('Emitting to socket:', socket.id);

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

    // Update the player_input handler to properly handle shooting
    socket.on('player_input', (input) => {
        //console.log('Received player_input:', input); // Debug log
        
        const player = gameState.players.get(socket.id);
        if (!player || !player.ship) {
            console.log('No player or ship found for socket:', socket.id); // Debug log
            return;
        }
        
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
            case 'shoot':
                /*console.log('Processing shoot input:', { // Debug log
                    playerId: socket.id,
                    inSafeZone: isInSafeZone(player.ship.x, player.ship.y, socket.id),
                    lastShot: player.lastShot,
                    now: Date.now()
                });*/
                
                // Only handle shooting on keydown (when value is true)
                if (input.value === true) {
                    // Don't allow shooting from safe zones
                    if (isInSafeZone(player.ship.x, player.ship.y, socket.id)) {
                        //console.log('Shoot blocked - player in safe zone'); // Debug log
                        // Send feedback to the player
                        socket.emit('shoot_blocked', { reason: 'in_safe_zone' });
                        return;
                    }
                    
                    // Check shooting cooldown
                    const now = Date.now();
                    // Longer cooldown for UFO (500ms) vs fighter (250ms)
                    const cooldown = player.ship.pattern === 'ufo' ? 500 : 250;
                    
                    if (player.lastShot && now - player.lastShot < cooldown) {
                        //console.log('Shoot blocked - cooldown not finished'); // Debug log
                        return;
                    }
                    
                    player.lastShot = now;
                    //console.log('Creating bullet for player:', socket.id); // Debug log
                    createBullet(socket.id, player.ship);
                }
                break;
            case 'rocket':
                if (player.ship) {
                    console.debug('🚀 Creating rocket bullet for player:', socket.id);
                    createBullet(socket.id, player.ship, 'rocket');
                }
                break;
            case 'warp_home':
                player.ship.warpHome();
                break;
        }
    });

    // Add team balancing function
    // This function is now defined above the ServerShip class
    
    // Add cleanup when player disconnects
    socket.on('disconnect', async () => {
        const player = gameState.players.get(socket.id);
        if (player) {
            // Save shared state to Master Server
            try {
                await updateSharedPlayerState(player.name, {
                    resources: player.resources,
                    score: player.score,
                    name: player.name
                });
            } catch (error) {
                console.error('Failed to save shared state:', error);
            }
        }
        const playerToDelete = gameState.players.get(socket.id);
        if (playerToDelete && playerToDelete.ship) {
            // Save player data before cleanup
           // playerToDelete.ship.saveData();
            
            // Log before decrement
            console.log('Before disconnect team counts:', {...teamCounts});
            teamCounts[playerToDelete.ship.color]--;
            // Log after decrement
            console.log('After disconnect team counts:', {...teamCounts});
        }
        gameState.players.delete(socket.id);
        gameState.homePositions.delete(socket.id);
        console.log('Player disconnected:', socket.id);
        console.log('Current team counts:', teamCounts);
    });

    // Add debug logging for team balance
    socket.on('connect', () => {
        console.log('New player connected. Current team counts:', teamCounts);
    });

    // Handle chat messages
    socket.on('chat_message', (data) => {
        console.log(`Chat received: `);
        console.log(data);
    
        const player = gameState.players.get(socket.id);
        if (!player || !player.ship) return;

        const message = {
            content: data.isGlobal ? data.content.slice(1) : data.content,
            sender: player.ship.callSign,
            color: player.ship.color,
            type: data.isGlobal ? 'global' : 'team',
            timestamp: Date.now()
        };

        if (data.isGlobal) {
            // Send to all players
            io.emit('chat_broadcast', message);
        } else {
            // Send only to team members
            for (const [playerId, p] of gameState.players) {
                if (p.ship.color === player.ship.color) {
                    io.to(playerId).emit('chat_broadcast', message);
                }
            }
        }

        // Check if it's a help command
        const helpResult = parseHelpCommand(message.content);
        if (helpResult) {
            console.log(`Help command received: ${helpResult.category}`);
            // Send help information to the player
            socket.emit('help', {
                category: helpResult.category,
                content: helpResult.formatted
            });
            return; // Don't broadcast help commands to other players
        }
    });

    // Add to the connection handler - announce player join
    const player = gameState.players.get(socket.id);
    if (player && player.ship) {
        // Send to all players on same team
        for (const [playerId, otherPlayer] of gameState.players) {
            if (otherPlayer.ship.color === player.ship.color) {
                io.to(playerId).emit('system_message', 
                    `${player.ship.callSign} joined your team`
                );
            }
        }
    }

    // Send next reset time to player
    socket.emit('score_reset_info', {
        nextReset: getNextResetTime().toISOString(),
        lastReset: lastScoreReset
    });
});

// Start the server
httpServer.listen(PORT, '0.0.0.0', () => {
    console.log(`Socket.IO server running on port ${PORT} on all interfaces`);
    // Update the server announcement
    console.log('Server accessible at: http://localhost:3000');
    console.log('Allowed origins:', [
        "http://localhost:8000", 
        "http://localhost:5173", 
        "http://127.0.0.1:8000"
    ]);
});

// Enhanced player count function
const getActivePlayerCount = () => {
    const count = gameState.players.size;
    console.log(`Active players: ${count}`);
    return count;
};

// Add function to get shared player state
const getSharedPlayerState = async (playerName) => {
    try {
        const response = await fetch(`${MASTER_SERVER_URL}/master/players/state/${playerName}`);
        if (response.ok) {
            return await response.json();
        }
    } catch (error) {
        console.error('Failed to get shared player state:', error);
    }
    return null;
};

// Add function to update shared player state
const updateSharedPlayerState = async (playerName, state) => {
    try {
        const response = await fetch(`${MASTER_SERVER_URL}/master/players/state/${playerName}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(state)
        });
        return response.ok;
    } catch (error) {
        console.error('Failed to update shared player state:', error);
        return false;
    }
};

// Enhanced heartbeat function with correct URL and error handling
const sendHeartbeat = async () => {
    try {
        const playerCount = getActivePlayerCount();
        const heartbeatData = {
            server_id: GAME_SERVER_ID, // Use the variable instead of hardcoded value
            current_players: playerCount,
            status: 'online'
        };

        console.log('Sending heartbeat:', heartbeatData);

        const response = await fetch(`${MASTER_SERVER_URL}/master/worlds/heartbeat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(heartbeatData)
        });
        
        if (response.ok) {
            const result = await response.json();
            console.log('Heartbeat sent successfully:', result);
        } else {
            console.error('Heartbeat failed with status:', response.status);
            const errorText = await response.text();
            console.error('Error details:', errorText);
        }
    } catch (error) {
        console.error('Heartbeat failed:', error.message);
        // Add more detailed error logging
        if (error.code === 'ECONNREFUSED') {
            console.error('Could not connect to master server - is it running?');
        }
    }
};

// Send heartbeat every 30 seconds
setInterval(sendHeartbeat, 30000);

// Send initial heartbeat after a short delay
setTimeout(sendHeartbeat, 5000);

process.on('SIGTERM', async () => {
    try {
        // Unregister from master server
        await fetch(`${MASTER_SERVER_URL}/master/worlds/unregister`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ server_id: GAME_SERVER_ID })
        });
    } catch (error) {
        console.error('Failed to unregister:', error);
    }
    process.exit(0);
});

// Add at the top with other constants
const persistentPlayerData = new Map();  // Stores player data by callsign

// Update the cleanup interval with proper error handling
setInterval(() => {
    try {
        const now = Date.now();
        for (const [callSign, data] of persistentPlayerData.entries()) {
            if (!data.lastSeen || now - data.lastSeen > DATA_RETENTION_TIME) {
                persistentPlayerData.delete(callSign);
                console.log(`Cleaned up old data for player ${callSign}`);
            }
        }
    } catch (error) {
        console.error('Error in data cleanup:', error);
    }
}, DATA_RETENTION_TIME); // Run cleanup once per day

// Add at the top with other utility functions
function shouldResetScores(lastScoreReset) {
    if (!lastScoreReset) return true;
    
    const now = new Date();
    const lastReset = new Date(lastScoreReset);
    
    // Check if we've passed midnight UTC since the last reset
    return now.getUTCDate() !== lastReset.getUTCDate() ||
           now.getUTCMonth() !== lastReset.getUTCMonth() ||
           now.getUTCFullYear() !== lastReset.getUTCFullYear();
}

// Add with other game state
let lastScoreReset = new Date().toISOString();  // Initialize to current time

function getNextResetTime() {
    const now = new Date();
    const tomorrow = new Date(now);
    tomorrow.setUTCDate(tomorrow.getUTCDate() + 1);
    tomorrow.setUTCHours(0, 0, 0, 0);
    return tomorrow;
}

// Add after other setInterval calls
function checkAndResetScores() {
    if (shouldResetScores(lastScoreReset)) {
        console.log('Daily score reset at UTC midnight');
        
        // Reset scores for all active players
        for (const [_, player] of gameState.players) {
            if (player && player.ship) {
                player.ship.score = 0;
                player.ship.streak = [];
            }
        }
        
        // Reset scores in persistent storage
        for (const [callSign, data] of persistentPlayerData.entries()) {
            data.score = 0;
            data.streak = [];
        }
        
        // Update last reset time
        lastScoreReset = new Date().toISOString();
        
        // Notify all connected players
        io.emit('system_message', 'Daily score reset - All scores have been reset to 0');
        
        // Force a game state update to all clients
        for (const [playerId, player] of gameState.players) {
            if (player && player.ship) {
                io.to(playerId).emit('game_state', {
                    players: Object.fromEntries(
                        Array.from(gameState.players.entries())
                            .filter(([_, p]) => p && p.ship)
                            .map(([id, p]) => [id, p.ship.getState()])
                    ),
                    status: 'running'
                });
            }
        }
    }
}

// Check every minute
setInterval(checkAndResetScores, 60 * 1000);

// Also check on server start
checkAndResetScores();

// Add to the game state sent to clients
function getGameState(playerId) {
    const player = gameState.players.get(playerId);
    if (!player || !player.ship) return null;

    return {
        players: Object.fromEntries(
            Array.from(gameState.players.entries())
                .filter(([_, p]) => p && p.ship)
                .map(([id, p]) => [id, p.ship.getState()])
        ),
        nextScoreReset: getNextResetTime().toISOString(),
        status: 'running'
    };
}