<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { CanvasManager } from '../core/Canvas';

const gameContainer = ref(null);
const canvasStack = ref(null);
const canvasManager = ref(null);

onMounted(() => {
  // Initialize canvas manager
  canvasManager.value = new CanvasManager();
  
  // Set up canvas layers
  if (canvasStack.value) {
    // Add all canvas layers to the DOM
    Object.values(canvasManager.value.layers).forEach(canvas => {
      canvasStack.value.appendChild(canvas);
    });
    
    // Store contexts for easy access
    const ctx = canvasManager.value.contexts.game;
    
    // Initialize game state here
    canvasManager.value.drawStarfield(); // Draw initial background
    
    // Start game loop
    requestAnimationFrame(gameLoop);
  }
});

const gameLoop = () => {
  if (!canvasManager.value) return;
  
  const ctx = canvasManager.value.contexts.game;
  ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
  
  // Game update logic here
  
  requestAnimationFrame(gameLoop);
};

onUnmounted(() => {
  // Cleanup
  if (canvasManager.value) {
    Object.values(canvasManager.value.layers).forEach(canvas => {
      canvas.remove();
    });
  }
});
</script>

<template>
  <div class="game-container" ref="gameContainer">
    <div class="canvas-stack" ref="canvasStack">
      <!-- Canvas layers will be mounted here -->
    </div>
  </div>
</template>

<style>
.game-container {
  width: 100%;
  height: 100%;
  background: #000;
  position: relative;
  overflow: hidden;
}

.canvas-stack {
  position: relative;
  width: 100%;
  height: 100%;
}
</style> 