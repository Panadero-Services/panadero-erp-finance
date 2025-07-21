// socket-server.js
import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';
import cors from 'cors';

const app = express();
const PORT = 3000;

// Add these constants at the top of socket-server.js
const ROTATION_SPEED = 0.1;  // Radians per frame
const THRUST_POWER = 0.2;    // Acceleration per frame
const FRICTION = 0.99;       // Velocity retention per frame
const MAX_SPEED = 10;        // Maximum velocity
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

// In socket-server.js - add shape property
const COLLECTIBLE_TYPES = [
    { 
        type: 'gold',
        color: '#FFD700',
        points: 100, 
        radius: 15,
        shape: 'star'  // ★ shape
    },
    { 
        type: 'water', 
        color: '#00FFFF',
        points: 200,
        radius: 15,
        shape: 'droplet'  // 💧 shape
    },
    { 
        type: 'kryptonite',
        color: '#FF00FF',
        points: 300,
        radius: 15,
        shape: 'crystal'  // ♦ shape
    }
];

const COLLECTIBLE_SPAWN_TIME = {
    MIN: 500,   // 5.0 seconds
    MAX: 600    // 6.0 seconds
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
    constructor(x, y, playerId) {
        this.id = playerId;
        this.callSign = playerId.slice(0, 4).toUpperCase();
        this.health = 100;  // Initial health
        this.maxHealth = 100;  // Maximum health
        this.homeX = x;
        this.homeY = y;
        this.x = x;
        this.y = y;
        this.angle = -Math.PI / 2;  // Start pointing up (-90 degrees)
        this.velocity = { x: 0, y: 0 };
        // Add acceleration initialization
        this.acceleration = { x: 0, y: 0 };  // <-- Add this line
        this.score = 0;
        this.rotatingLeft = false;
        this.rotatingRight = false;
        this.engineOn = false;
        this.pattern = Math.random() < 0.5 ? SHIP_PATTERNS.FIGHTER : SHIP_PATTERNS.UFO;
        
        // Now this function exists when called
        this.color = assignBalancedTeam();
        teamCounts[this.color]++;  // Increment team count
        
        this.radius = SHIP_RADIUS;
        this.isColliding = false;
        this.lastCollisionTime = 0;  // Add collision cooldown tracker
        this.collisionCooldown = 500;  // 500ms cooldown
        
        // Add collection tracking
        this.collections = {
            gold: 0,
            water: 0,
            kryptonite: 0
        };

        // Add streak tracking
        this.streak = [];  // Just track the last 5 collected
        this.lastStreakTime = 0;  // Track when last streak was awarded

        // Add debug log
        console.log(`New ship created for player ${playerId} with pattern: ${this.pattern}`);
    }

    update(deltaTime) {
        const dt = Math.min(deltaTime, 32) / 16;

        // Rotation
        if (this.rotatingLeft) this.angle -= ROTATION_SPEED * dt;
        if (this.rotatingRight) this.angle += ROTATION_SPEED * dt;

        // Thrust - adjust angle to match visual orientation
        if (this.engineOn) {
            const thrustAngle = this.angle - Math.PI / 2;
            this.velocity.x += Math.cos(thrustAngle) * THRUST_POWER * dt;
            this.velocity.y += Math.sin(thrustAngle) * THRUST_POWER * dt;
        }

        // Apply friction
        this.velocity.x *= Math.pow(FRICTION, dt);
        this.velocity.y *= Math.pow(FRICTION, dt);

        // Enforce speed limit
        const speed = Math.sqrt(this.velocity.x ** 2 + this.velocity.y ** 2);
        if (speed > MAX_SPEED) {
            const scale = MAX_SPEED / speed;
            this.velocity.x *= scale;
            this.velocity.y *= scale;
        }

        // Use the proven updateShipPosition function instead of direct position update
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

    checkCollision(otherShip) {
        const dx = this.x - otherShip.x;
        const dy = this.y - otherShip.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        return distance < (this.radius + otherShip.radius) * 2;
    }

    canTakeCollisionDamage() {
        return (Date.now() - this.lastCollisionTime) >= this.collisionCooldown;
    }

    // Add new method to handle streak check
    checkStreak(type) {
        // MUST have exactly 5 in a row of same type
        const hasStreak = this.streak.length === 5 && 
                         this.streak.every(item => item === type);

        // Points calculation MUST match streak status
        const basePoints = type === 'gold' ? 3 : 1;
        const bonus = hasStreak ? 10 : 0;

        return {
            hasStreak,
            bonus,
            total: basePoints + bonus
        };
    }

    // New helper method for point calculation
    calculatePoints(type) {
        // First check if we're in a streak for this type
        const hasStreak = this.streak.length >= 4 && 
                         this.streak.slice(-4).every(item => item === type);

        // Base points
        let points = type === 'gold' ? 3 : 1;
        
        // If in a streak, add bonus to the base value
        if (hasStreak) {
            points += 10;  // Now water will be worth 11, gold 13, kryptonite 11
        }

        return points;
    }

    // Keep existing collectItem method but use helpers
    collectItem(collectible) {
        // In collectItem method
        const lastCollected = this.streak[this.streak.length - 1];
        const isSameType = collectible.type === lastCollected;

        // First add new collection
        if (!isSameType) {
            this.streak = [];
        }
        this.streak.push(collectible.type);

        // NOW check for streak AFTER adding 5th element
        const hasStreak = this.streak.length === 5 && 
                         this.streak.every(item => item === collectible.type);

        // Calculate points including bonus if streak
        const basePoints = collectible.type === 'gold' ? 3 : 1;
        const bonus = hasStreak ? 10 : 0;
        const total = basePoints + bonus;

        // Clear streak after bonus awarded
        if (hasStreak) {
            // Add bonus to the collectible count
            this.collections[collectible.type] += 10;  // Add bonus to collection
            this.streak = [];  // Clear streak
        }

        // Update collections and score
        this.collections[collectible.type]++;
        this.score += total;

        return {
            score: this.score,
            collections: this.collections,
            streak: [...this.streak],
            hasStreak,
            bonus,
            total: total
        };
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
function createBullet(playerId, ship) {
    const bulletSpeed = ship.pattern === 'ufo' ? 5 : 10; // Half speed for UFO
    const bulletId = `bullet_${playerId}_${Date.now()}`;
    
    // Create the first bullet
    const bullet = {
        id: bulletId,
        x: ship.x,
        y: ship.y,
        angle: ship.angle,
        speed: bulletSpeed,
        color: ship.color,
        playerId: playerId,
        created: Date.now(),
        lifespan: 2000 // 2 seconds
    };
    gameState.bullets.set(bulletId, bullet);

    // If it's a UFO, create a second bullet at a slight angle
    if (ship.pattern === 'ufo') {
        const bulletId2 = `bullet_${playerId}_${Date.now()}_2`;
        const bullet2 = {
            ...bullet,
            id: bulletId2,
            angle: ship.angle + 0.2 // Slight spread to the right
        };
        const bullet3 = {
            ...bullet,
            id: `bullet_${playerId}_${Date.now()}_3`,
            angle: ship.angle - 0.2 // Slight spread to the left
        };
        gameState.bullets.set(bulletId2, bullet2);
        gameState.bullets.set(bullet3.id, bullet3);
    }

    //console.log('Creating bullet(s) for player:', playerId, 'Pattern:', ship.pattern);
    //console.log('Current bullet count:', gameState.bullets.size);
}

// Add bullet update function
function updateBullets() {
    const now = Date.now();
    for (const [bulletId, bullet] of gameState.bullets) {
        // Move bullet
        bullet.x += Math.sin(bullet.angle) * bullet.speed;
        bullet.y -= Math.cos(bullet.angle) * bullet.speed;
        
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
                    player.ship.takeDamage(true);  // Bullet damage - no cooldown
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
// World size constants
const WORLD_CONFIG = {
    WIDTH: 20000,     // Much bigger world
    HEIGHT: 16000,    // Proportional
    MIN_FIELD_DISTANCE: 4000
};

const FIELD_CONFIG = {
    COUNT: 3,              // 3 fields
    RADIUS: 1200,          // Field size
    MAX_PER_FIELD: 100,    // Max 100 collectibles per field
    SPAWN_INTERVAL: 3000   // Check spawn every 3 seconds
};

// Resource fields storage
const resourceFields = new Map();

// Helper function to check valid field position
function isValidFieldPosition(x, y, existingFields) {
    for (const field of existingFields.values()) {
        const dx = x - field.x;
        const dy = y - field.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        if (distance < WORLD_CONFIG.MIN_FIELD_DISTANCE) {
            return false;
        }
    }
    return true;
}

// Create or update field position
function createOrUpdateField(fieldId) {
    let x, y;
    if (fieldId === 0) { // Fixed position for testing
        x = 2000;
        y = 2000;
        console.log(`Field ${fieldId} positioned at:`, { x: Math.round(x), y: Math.round(y) });
    } else { // Keep other fields random
        do {
            x = Math.random() * WORLD_CONFIG.WIDTH;
            y = Math.random() * WORLD_CONFIG.HEIGHT;
        } while (!isValidFieldPosition(x, y, resourceFields));
        console.log(`Field ${fieldId} positioned at:`, { x: Math.round(x), y: Math.round(y) });
    }

    resourceFields.set(fieldId, {
        id: fieldId,
        x: x,
        y: y,
        radius: FIELD_CONFIG.RADIUS,
        lastUpdate: Date.now()
    });
}

// Now initialize the fields
console.log('\n=== Initializing Resource Fields ===');
for (let i = 0; i < FIELD_CONFIG.COUNT; i++) {
    createOrUpdateField(i);
}

// Add collision check for collectibles
function checkCollectibleCollision(ship, collectible) {
    const dx = ship.x - collectible.x;
    const dy = ship.y - collectible.y;
    const distance = Math.sqrt(dx * dx + dy * dy);
    
    if (distance < (ship.radius + collectible.radius)) {
        // Keep this healing effect for water (was diamond)
        if (collectible.type === 'water' && collectible.color === '#00FFFF') {  // Changed from 'diamond'
            ship.heal();  // Restore full health
        }
        return true;
    }
    return false;
}

function isInRange(playerX, playerY, objectX, objectY, range) {
    const dx = playerX - objectX;
    const dy = playerY - objectY;
    return Math.sqrt(dx * dx + dy * dy) <= range;
}

// Add new helper function without modifying existing code
function checkPathCollision(oldPos, newPos, collectible) {
    // Increase interpolation points for better accuracy
    const steps = Math.ceil(Math.sqrt(
        Math.pow(newPos.x - oldPos.x, 2) + 
        Math.pow(newPos.y - oldPos.y, 2)
    ) / 10); // One check every 10 units

    // Use velocity-based prediction
    const predictedPath = [];
    for(let i = 0; i <= steps; i++) {
        const t = i / steps;
        // Cubic interpolation for smoother path
        const checkX = oldPos.x + (newPos.x - oldPos.x) * (t * (2 - t));
        const checkY = oldPos.y + (newPos.y - oldPos.y) * (t * (2 - t));
        predictedPath.push({x: checkX, y: checkY});
    }

    // Check each point for collision
    for(const point of predictedPath) {
        const dx = point.x - collectible.x;
        const dy = point.y - collectible.y;
        if(Math.sqrt(dx * dx + dy * dy) < SHIP_RADIUS * 2) {
            return true;
        }
    }
    return false;
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

    // Update bullets
    updateBullets();

    // Check collectible collisions
    for (const [playerId, player] of gameState.players) {
        if (!player || !player.ship) continue;
        
        for (const [collectibleId, collectible] of gameState.collectibles) {
            if (checkCollectibleCollision(player.ship, collectible)) {
                const ship = player.ship;
                const result = ship.collectItem(collectible);
                
                // Log collection
                console.log(`Player ${ship.callSign} collected ${collectible.type}:`, result);
                
                // Remove collectible
                gameState.collectibles.delete(collectibleId);
                
                // Notify player (keep existing format)
                io.to(playerId).emit('collectible_collected', result);
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
            bullets: Array.from(gameState.bullets.values())  // Add this line
                .filter(bullet => isInRange(  // Only send bullets in range
                    playerShip.x, 
                    playerShip.y, 
                    bullet.x, 
                    bullet.y, 
                    VISIBILITY_RANGE
                )),
            status: 'running'
        };
        
        io.to(playerId).emit('game_state', playerGameState);
    }
}

// Start game loop
setInterval(gameLoop, 1000 / TICK_RATE);

// Add spawn function
function spawnCollectibleInField(field) {
    // Count existing collectibles in this field
    const fieldCollectibles = Array.from(gameState.collectibles.values())
        .filter(c => {
            const dx = c.x - field.x;
            const dy = c.y - field.y;
            return Math.sqrt(dx * dx + dy * dy) <= field.radius;
        });

    // Only spawn if under max
    if (fieldCollectibles.length < FIELD_CONFIG.MAX_PER_FIELD) {
        // Random angle
        const angle = Math.random() * Math.PI * 2;
        
        // Use squared random for distance to favor center
        // Math.pow(Math.random(), 3) will cluster more towards center
        // Math.random() alone would give uniform distribution
        const centerBias = Math.pow(Math.random(), 3);  // Cube makes center much more likely
        const distance = centerBias * field.radius;
        
        // Pick random collectible type
        const type = COLLECTIBLE_TYPES[Math.floor(Math.random() * COLLECTIBLE_TYPES.length)];
        
        const collectible = {
            id: 'collectible_' + Date.now(),
            x: field.x + Math.cos(angle) * distance,
            y: field.y + Math.sin(angle) * distance,
            ...type,
            createdAt: Date.now()
        };

        gameState.collectibles.set(collectible.id, collectible);
        
        /*console.log(`Spawned ${type.type} in Field ${field.id} at:`, {
            x: Math.round(collectible.x),
            y: Math.round(collectible.y),
            distanceFromCenter: Math.round(distance)
        });*/
    }
}

function scheduleNextSpawn() {
    // Random time between MIN and MAX
    const delay = Math.random() * (COLLECTIBLE_SPAWN_TIME.MAX - COLLECTIBLE_SPAWN_TIME.MIN) 
                 + COLLECTIBLE_SPAWN_TIME.MIN;
    
    setTimeout(() => {
        // Pick random field
        const fieldId = Math.floor(Math.random() * FIELD_CONFIG.COUNT);
        const field = resourceFields.get(fieldId);
        
        spawnCollectibleInField(field);
        
        // Schedule next spawn
        scheduleNextSpawn();
    }, delay);
}

// Start the spawn cycle
scheduleNextSpawn();

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
            case 'warp_home':
                player.ship.warpHome();
                break;
        }
    });

    // Add team balancing function
    // This function is now defined above the ServerShip class
    
    // Add cleanup when player disconnects
    socket.on('disconnect', () => {
        const player = gameState.players.get(socket.id);
        if (player && player.ship) {
            // Log before decrement
            console.log('Before disconnect team counts:', {...teamCounts});
            teamCounts[player.ship.color]--;
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
});

// Start the server
httpServer.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
    console.log('Allowed origins:', ["http://localhost:8000", "http://localhost:5173", "http://127.0.0.1:8000"]);
});