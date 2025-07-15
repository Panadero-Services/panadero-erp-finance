<script setup>
import { onMounted, ref } from 'vue';

const generateAllSprites = () => {
  // Create a canvas for each sprite
  const sprites = [
    {
      name: 'ship',
      width: 48,
      height: 48,
      draw: (ctx) => {
        // Main body
        ctx.fillStyle = '#00ff00';
        ctx.fillRect(12, 8, 24, 32);
        // Cannon
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(20, 0, 8, 16);
        // Wings
        ctx.fillStyle = '#00aa00';
        ctx.fillRect(4, 30, 40, 8);
      }
    },
    {
      name: 'ship-thrust',
      width: 96,
      height: 48,
      draw: (ctx) => {
        // Frame 1
        ctx.fillStyle = '#00ff00';
        ctx.fillRect(12, 8, 24, 32);
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(20, 0, 8, 16);
        ctx.fillStyle = '#00aa00';
        ctx.fillRect(4, 30, 40, 8);
        ctx.fillStyle = '#ff6600';
        ctx.fillRect(20, 40, 8, 8);

        // Frame 2
        ctx.translate(48, 0);
        ctx.fillStyle = '#00ff00';
        ctx.fillRect(12, 8, 24, 32);
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(20, 0, 8, 16);
        ctx.fillStyle = '#00aa00';
        ctx.fillRect(4, 30, 40, 8);
        ctx.fillStyle = '#ffaa00';
        ctx.fillRect(20, 40, 8, 12);
      }
    },
    {
      name: 'ship-shield',
      width: 48,
      height: 48,
      draw: (ctx) => {
        // Ship
        ctx.fillStyle = '#00ff00';
        ctx.fillRect(12, 8, 24, 32);
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(20, 0, 8, 16);
        ctx.fillStyle = '#00aa00';
        ctx.fillRect(4, 30, 40, 8);
        // Shield
        ctx.strokeStyle = '#4444ff';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(24, 24, 30, 0, Math.PI * 2);
        ctx.stroke();
      }
    },
    {
      name: 'enemy-basic',
      width: 80,
      height: 40,
      draw: (ctx) => {
        // Frame 1
        ctx.fillStyle = '#ff4444';
        ctx.fillRect(8, 8, 24, 24);
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(4, 20, 32, 4);

        // Frame 2
        ctx.translate(40, 0);
        ctx.fillStyle = '#ff4444';
        ctx.fillRect(8, 8, 24, 24);
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(4, 16, 32, 4);
      }
    },
    {
      name: 'enemy-tank',
      width: 120,
      height: 60,
      draw: (ctx) => {
        // Frame 1
        ctx.fillStyle = '#ff8800';
        ctx.fillRect(10, 10, 40, 40);
        ctx.fillStyle = '#ff6600';
        ctx.fillRect(15, 5, 10, 10);
        ctx.fillRect(35, 5, 10, 10);

        // Frame 2
        ctx.translate(60, 0);
        ctx.fillStyle = '#ff8800';
        ctx.fillRect(10, 10, 40, 40);
        ctx.fillStyle = '#ff6600';
        ctx.fillRect(15, 0, 10, 15);
        ctx.fillRect(35, 0, 10, 15);
      }
    },
    {
      name: 'enemy-fast',
      width: 60,
      height: 30,
      draw: (ctx) => {
        // Frame 1
        ctx.fillStyle = '#ff0088';
        ctx.beginPath();
        ctx.moveTo(15, 5);
        ctx.lineTo(25, 25);
        ctx.lineTo(5, 25);
        ctx.closePath();
        ctx.fill();

        // Frame 2
        ctx.translate(30, 0);
        ctx.beginPath();
        ctx.moveTo(15, 8);
        ctx.lineTo(25, 28);
        ctx.lineTo(5, 28);
        ctx.closePath();
        ctx.fill();
      }
    },
    {
      name: 'bullet',
      width: 8,
      height: 16,
      draw: (ctx) => {
        const gradient = ctx.createLinearGradient(4, 0, 4, 16);
        gradient.addColorStop(0, '#ffff00');
        gradient.addColorStop(1, '#ff6600');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, 8, 16);
      }
    },
    {
      name: 'powerup-double',
      width: 120,
      height: 30,
      draw: (ctx) => {
        for (let i = 0; i < 4; i++) {
          ctx.save();
          ctx.translate(i * 30, 0);
          
          // Glowing orb
          const gradient = ctx.createRadialGradient(15, 15, 5, 15, 15, 15);
          gradient.addColorStop(0, '#00ffff');
          gradient.addColorStop(1, 'rgba(0,255,255,0)');
          ctx.fillStyle = gradient;
          ctx.beginPath();
          ctx.arc(15, 15, 15, 0, Math.PI * 2);
          ctx.fill();
          
          // Double shot icon
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(10, 8, 4, 14);
          ctx.fillRect(16, 8, 4, 14);
          
          ctx.restore();
        }
      }
    },
    {
      name: 'powerup-speed',
      width: 120,
      height: 30,
      draw: (ctx) => {
        for (let i = 0; i < 4; i++) {
          ctx.save();
          ctx.translate(i * 30, 0);
          
          // Glowing orb
          const gradient = ctx.createRadialGradient(15, 15, 5, 15, 15, 15);
          gradient.addColorStop(0, '#ffff00');
          gradient.addColorStop(1, 'rgba(255,255,0,0)');
          ctx.fillStyle = gradient;
          ctx.beginPath();
          ctx.arc(15, 15, 15, 0, Math.PI * 2);
          ctx.fill();
          
          // Speed arrow
          ctx.fillStyle = '#ffffff';
          ctx.beginPath();
          ctx.moveTo(8, 15);
          ctx.lineTo(22, 8);
          ctx.lineTo(22, 22);
          ctx.closePath();
          ctx.fill();
          
          ctx.restore();
        }
      }
    },
    {
      name: 'powerup-shield',
      width: 120,
      height: 30,
      draw: (ctx) => {
        for (let i = 0; i < 4; i++) {
          ctx.save();
          ctx.translate(i * 30, 0);
          
          // Glowing orb
          const gradient = ctx.createRadialGradient(15, 15, 5, 15, 15, 15);
          gradient.addColorStop(0, '#4444ff');
          gradient.addColorStop(1, 'rgba(68,68,255,0)');
          ctx.fillStyle = gradient;
          ctx.beginPath();
          ctx.arc(15, 15, 15, 0, Math.PI * 2);
          ctx.fill();
          
          // Shield icon
          ctx.strokeStyle = '#ffffff';
          ctx.lineWidth = 2;
          ctx.beginPath();
          ctx.arc(15, 15, 8, 0, Math.PI * 2);
          ctx.stroke();
          
          ctx.restore();
        }
      }
    },
    {
      name: 'explosion',
      width: 512,
      height: 64,
      draw: (ctx) => {
        for (let i = 0; i < 8; i++) {
          ctx.save();
          ctx.translate(i * 64, 0);
          
          const gradient = ctx.createRadialGradient(32, 32, 5 + i*3, 32, 32, 20 + i*5);
          gradient.addColorStop(0, '#ffffff');
          gradient.addColorStop(0.4, '#ffaa00');
          gradient.addColorStop(0.6, '#ff6600');
          gradient.addColorStop(1, 'rgba(255,0,0,0)');
          
          ctx.fillStyle = gradient;
          ctx.beginPath();
          ctx.arc(32, 32, 30 - i*2, 0, Math.PI * 2);
          ctx.fill();
          
          ctx.restore();
        }
      }
    }
  ];

  // Generate each sprite
  sprites.forEach(sprite => {
    const canvas = document.createElement('canvas');
    canvas.width = sprite.width;
    canvas.height = sprite.height;
    const ctx = canvas.getContext('2d');
    
    // Draw the sprite
    sprite.draw(ctx);
    
    // Create download link
    const link = document.createElement('a');
    link.download = `${sprite.name}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
  });
};

onMounted(() => {
  generateAllSprites();
});
</script>

<template>
  <div>
    <button @click="generateAllSprites">Generate All Sprites</button>
  </div>
</template>
 