<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {computed, onMounted, onActivated, onServerPrefetch, ref} from 'vue'


const props = defineProps({
    cvxWidth: Number,
    cvxHeight: Number,
    starsCount: Number
});





function setGameScale(_scale) {
     document.getElementById("gameScale").innerHTML = _scale;
}

function setCanvas(width, height) {
 // var canvas = document.getElementById("background-layer");  
  canvas.width = width;
  canvas.height = height;

  canvas = document.getElementById("game-layer");  
  canvas.width = width;
  canvas.height = height;

  canvas = document.getElementById("ui-layer");  
  canvas.width = width;
  canvas.height = height;

  console.log(width,height)
}

var canvas = document.getElementById("background-layer");  

const STAR_COLOR = '#c9e3ff';
const STAR_SIZE = 2;
const STAR_MIN_SCALE = 0.2;
const STAR_OVERFLOW_THRESHOLD = 50;

let STAR_COUNT;
let starCanvas;
let starCtx;

let starScale = 1; // device pixel ratio
let starWidth;
let starHeight;
let stars = [];
let starVelocity = { x: 0, y: 0, tx: 0, ty: 0, z: 0.0005 };



function doCanvas(){
/// @dev section 5 Stars
/*       *     .        *  .    .       *    *   . 
/*          *     .        *  .    *    *   . 
/*          *     .        *  .    *    *   . 
 .  *  start of the stars   .
 *  .  .   change these values:   .  *
   .      * .        .          * .       */

//STAR_COUNT = ( window.innerWidth + window.innerHeight ) / 25;
STAR_COUNT = props.starsCount;
console.log('STAR_COUNT',STAR_COUNT);
canvas= document.getElementById("bg-layer");
starCanvas= document.getElementById("bg-layer");
starCtx = starCanvas.getContext("2d");

starWidth = starCanvas.width;
starHeight = starCanvas.height;
}

/// @notice stars.starGenerate
function starGenerate() {
   for( let i = 0; i < STAR_COUNT; i++ ) {
    stars.push({
      x: 0,
      y: 0,
      z: STAR_MIN_SCALE + Math.random() * ( 1 - STAR_MIN_SCALE )
    });
   }   
}

/// @notice stars.placeStar
function placeStar(star) {
  star.x = Math.random() * canvas.width;
  star.y = Math.random() * canvas.height;
}

/// @notice stars.recycleStar
function recycleStar(star) {
  let direction = 'z';
  let vx = Math.abs( starVelocity.x ),
      vy = Math.abs( starVelocity.y );
  if( vx > 1 || vy > 1 ) {
    let axis;
    if( vx > vy ) {
      axis = Math.random() < vx / ( vx + vy ) ? 'h' : 'v';
    }
    else {
      axis = Math.random() < vy / ( vx + vy ) ? 'v' : 'h';
    }
    if( axis === 'h' ) {
      direction = starVelocity.x > 0 ? 'l' : 'r';
    }
    else {
      direction = starVelocity.y > 0 ? 't' : 'b';
    }
  }
  star.z = STAR_MIN_SCALE + Math.random() * ( 1 - STAR_MIN_SCALE );
  if( direction === 'z' ) {
    star.z = 0.1;
    star.x = Math.random() * starWidth;
    star.y = Math.random() * starHeight;
  }
  else if( direction === 'l' ) {
    star.x = -STAR_OVERFLOW_THRESHOLD;
    star.y = starHeight * Math.random();
  }
  else if( direction === 'r' ) {
    star.x = starWidth + STAR_OVERFLOW_THRESHOLD;
    star.y = starHeight * Math.random();
  }
  else if( direction === 't' ) {
    star.x = starWidth * Math.random();
    star.y = -STAR_OVERFLOW_THRESHOLD;
  }
  else if( direction === 'b' ) {
    star.x = starWidth * Math.random();
    star.y = starHeight + STAR_OVERFLOW_THRESHOLD;
  }
}

/// @notice stars.starResize
function starResize() {
  starScale = window.devicePixelRatio || 1;
  starWidth = canvas.width;
  starHeight = canvas.height;
  stars.forEach( placeStar );
}

var starFps = 1;
/// @notice stars.starStep
function starStep() {
  setTimeout(function () {
    starCtx.clearRect( 0, 0, starWidth, starHeight );
    starUpdate();
    starRender();
    requestAnimationFrame( starStep );
  }, 1000 / starFps);
}

/// @notice stars.starUpdate
function starUpdate() {
  starVelocity.tx *= 0.96;
  starVelocity.ty *= 0.96;
  starVelocity.x += ( starVelocity.tx - starVelocity.x ) * 0.8;
  starVelocity.y += ( starVelocity.ty - starVelocity.y ) * 0.8;
  stars.forEach( ( star ) => {
    star.x += starVelocity.x * star.z;
    star.y += starVelocity.y * star.z;
    star.x += ( star.x - starWidth/2 ) * starVelocity.z * star.z;
    star.y += ( star.y - starHeight/2 ) * starVelocity.z * star.z;
    star.z += starVelocity.z;
    // recycle when out of bounds
    if( star.x < -STAR_OVERFLOW_THRESHOLD || star.x > starWidth + STAR_OVERFLOW_THRESHOLD || star.y < -STAR_OVERFLOW_THRESHOLD || star.y > starHeight + STAR_OVERFLOW_THRESHOLD ) {
      recycleStar( star );
    }
  } );
}

/// @notice stars.starRender
function starRender() {
  stars.forEach( ( star ) => {
  starCtx.beginPath();
  starCtx.lineCap = 'round';
  starCtx.lineWidth = STAR_SIZE * star.z * starScale;
  starCtx.globalAlpha = 0.5 + 0.5*Math.random();
  starCtx.strokeStyle = STAR_COLOR;
  starCtx.beginPath();
  starCtx.moveTo( star.x, star.y );
  var tailX = starVelocity.x * 2;
  var tailY = starVelocity.y * 2;
  // stroke() wont work on an invisible line
  if( Math.abs( tailX ) < 0.1 ) tailX = 0.5;
  if( Math.abs( tailY ) < 0.1 ) tailY = 0.5;
  starCtx.lineTo( star.x + tailX, star.y + tailY );
    starCtx.stroke();
  } );
}

/// end section 5

/*          *     .        *  .    *    *   . 
 .  *  end  .
 *  .  .          stars.  *
   .      * .        .          * .       */

const startStars = async () => {
console.log('startStarts')
    await doCanvas();

  // Start the stars
  await starGenerate();
  console.log(stars);
  starResize();
  starStep();
}


onMounted(async () => {
  startStars();
})


</script>
<template>

    <canvas id="bg-layer" :width="cvxWidth" :height="cvxHeight" class="bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-700"></canvas>
	
</template>
