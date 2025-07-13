<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// Constants for hardcoded player data
const GAME_CONFIG = {
    player: {
        name: "TESTER",
        id: "cdef"
    },
    canvas: {
        width: 800,
        height: 600
    },
    ship: {
        size: { width: 36, height: 48 }, // 6x scale
        position: { x: 400, y: 500 },
        velocity: { x: 0, y: 0 },
        maxSpeed: 5,
        rotationSpeed: 3
    },
    bullet: {
        size: { width: 6, height: 6 },
        speed: 7,
        maxCount: 3
    }
};

// Game state
const gameCanvas = ref(null);
const ctx = ref(null);
const isPlaying = ref(false);
const score = ref(0);
const ship = ref({
    x: GAME_CONFIG.ship.position.x,
    y: GAME_CONFIG.ship.position.y,
    angle: 0,
    velocity: { x: 0, y: 0 },
    bullets: [],
    firing: false,
    thrusting: false,
    rotatingLeft: false,
    rotatingRight: false
});

// Game loop control
let animationFrame = null;

onMounted(() => {
    initializeGame();
});

function initializeGame() {
    gameCanvas.value = document.querySelector('canvas');
    ctx.value = gameCanvas.value.getContext('2d');
    
    // Set initial canvas state
    ctx.value.fillStyle = 'black';
    ctx.value.fillRect(0, 0, GAME_CONFIG.canvas.width, GAME_CONFIG.canvas.height);
    
    // Setup event listeners
    document.addEventListener('keydown', handleKeyDown);
    document.addEventListener('keyup', handleKeyUp);
    
    console.log('GAME:', {
        status: 'init',
        player: GAME_CONFIG.player.name,
        id: GAME_CONFIG.player.id
    });
}

function startGame() {
    if (isPlaying.value) return;
    
    isPlaying.value = true;
    score.value = 0;
    ship.value = {
        x: GAME_CONFIG.ship.position.x,
        y: GAME_CONFIG.ship.position.y,
        angle: 0,
        velocity: { x: 0, y: 0 },
        bullets: [],
        firing: false,
        thrusting: false,
        rotatingLeft: false,
        rotatingRight: false
    };
    
    gameLoop();
}

function gameLoop() {
    if (!isPlaying.value) return;
    
    updateShip();
    updateBullets();
    drawGame();
    
    animationFrame = requestAnimationFrame(gameLoop);
}

function updateShip() {
    // Rotation
    if (ship.value.rotatingLeft) {
        ship.value.angle -= GAME_CONFIG.ship.rotationSpeed * Math.PI / 180;
    }
    if (ship.value.rotatingRight) {
        ship.value.angle += GAME_CONFIG.ship.rotationSpeed * Math.PI / 180;
    }
    
    // Thrust
    if (ship.value.thrusting) {
        ship.value.velocity.x += Math.sin(ship.value.angle) * 0.1;
        ship.value.velocity.y -= Math.cos(ship.value.angle) * 0.1;
    }
    
    // Apply velocity limits
    const speed = Math.sqrt(ship.value.velocity.x ** 2 + ship.value.velocity.y ** 2);
    if (speed > GAME_CONFIG.ship.maxSpeed) {
        const ratio = GAME_CONFIG.ship.maxSpeed / speed;
        ship.value.velocity.x *= ratio;
        ship.value.velocity.y *= ratio;
    }
    
    // Update position
    ship.value.x += ship.value.velocity.x;
    ship.value.y += ship.value.velocity.y;
    
    // Screen wrapping
    if (ship.value.x < 0) ship.value.x = GAME_CONFIG.canvas.width;
    if (ship.value.x > GAME_CONFIG.canvas.width) ship.value.x = 0;
    if (ship.value.y < 0) ship.value.y = GAME_CONFIG.canvas.height;
    if (ship.value.y > GAME_CONFIG.canvas.height) ship.value.y = 0;
}

function updateBullets() {
    // Create new bullet if firing
    if (ship.value.firing && ship.value.bullets.length < GAME_CONFIG.bullet.maxCount) {
        ship.value.bullets.push({
            x: ship.value.x + Math.sin(ship.value.angle) * 20,
            y: ship.value.y - Math.cos(ship.value.angle) * 20,
            velocity: {
                x: Math.sin(ship.value.angle) * GAME_CONFIG.bullet.speed,
                y: -Math.cos(ship.value.angle) * GAME_CONFIG.bullet.speed
            }
        });
        score.value -= 10; // Cost per shot
    }
    
    // Update bullet positions and remove off-screen bullets
    ship.value.bullets = ship.value.bullets.filter(bullet => {
        bullet.x += bullet.velocity.x;
        bullet.y += bullet.velocity.y;
        return bullet.x >= 0 && 
               bullet.x <= GAME_CONFIG.canvas.width && 
               bullet.y >= 0 && 
               bullet.y <= GAME_CONFIG.canvas.height;
    });
}

function drawGame() {
    const ctx = ctx.value;
    
    // Clear canvas
    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, GAME_CONFIG.canvas.width, GAME_CONFIG.canvas.height);
    
    // Draw ship
    ctx.save();
    ctx.translate(ship.value.x, ship.value.y);
    ctx.rotate(ship.value.angle);
    ctx.strokeStyle = 'white';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(0, -15);
    ctx.lineTo(10, 15);
    ctx.lineTo(0, 10);
    ctx.lineTo(-10, 15);
    ctx.closePath();
    ctx.stroke();
    ctx.restore();
    
    // Draw bullets
    ctx.fillStyle = 'red';
    ship.value.bullets.forEach(bullet => {
        ctx.fillRect(
            bullet.x - GAME_CONFIG.bullet.size.width/2,
            bullet.y - GAME_CONFIG.bullet.size.height/2,
            GAME_CONFIG.bullet.size.width,
            GAME_CONFIG.bullet.size.height
        );
    });
    
    // Draw score
    ctx.fillStyle = 'white';
    ctx.font = '20px Arial';
    ctx.fillText(`Score: ${score.value}`, 10, 30);
}

function handleKeyDown(event) {
    switch(event.key) {
        case 'ArrowLeft':
            ship.value.rotatingLeft = true;
            break;
        case 'ArrowRight':
            ship.value.rotatingRight = true;
            break;
        case 'ArrowUp':
            ship.value.thrusting = true;
            break;
        case ' ':
            ship.value.firing = true;
            break;
    }
}

function handleKeyUp(event) {
    switch(event.key) {
        case 'ArrowLeft':
            ship.value.rotatingLeft = false;
            break;
        case 'ArrowRight':
            ship.value.rotatingRight = false;
            break;
        case 'ArrowUp':
            ship.value.thrusting = false;
            break;
        case ' ':
            ship.value.firing = false;
            break;
    }
}

onUnmounted(() => {
    isPlaying.value = false;
    if (animationFrame) {
        cancelAnimationFrame(animationFrame);
    }
    document.removeEventListener('keydown', handleKeyDown);
    document.removeEventListener('keyup', handleKeyUp);
});
</script>

<template>
    <div class="game-container">
        <canvas 
            ref="gameCanvas"
            :width="GAME_CONFIG.canvas.width"
            :height="GAME_CONFIG.canvas.height"
            class="game-canvas"
        />
        
        <div v-if="!isPlaying" class="game-overlay" @click="startGame">
            <div class="text-2xl text-white mb-4">
                Click to Start
            </div>
            <div class="text-sm text-gray-400">
                {{ GAME_CONFIG.player.name }} ({{ GAME_CONFIG.player.id }})
            </div>
        </div>
    </div>
</template>

<style scoped>
.game-container {
    position: relative;
    width: 800px;
    height: 600px;
    margin: 0 auto;
    background: black;
}

.game-canvas {
    display: block;
}

.game-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.85);
    cursor: pointer;
}
</style> 