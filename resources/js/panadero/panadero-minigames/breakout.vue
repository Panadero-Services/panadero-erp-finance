<script setup>
import {computed, onMounted, ref} from 'vue';

import { panaderoMiniGames } from "panadero-minigames";



import Stars from '@/panadero/Stars.vue';


const emit = defineEmits(['status'])

const props =defineProps({
    id: Number,
    callSign: String, 
    activated: Number
});

const _emitStatus = (_status) =>{
 emit('status', _status);
}



// webhooks
onMounted(async ()=> {
  //canvas = document.getElementById('bordCanvas');
    //othello(eval('breakout'+props.id));
    breakout(document.getElementById('breakout' + props.id));

})


function breakout(canvas){

  const aCol = ['#0E0C40','#353387','#6464BF','#A1A6FF','#CECAFF'];
  const ballCol = ['#0095DD','#64E04A','#E8C740','#FF453F'];
  const goldColor = '#CFB53B';
  const silverColor = '#C0C0C0';

  //var canvas = document.getElementById('breakout'+props.id);

  var ctx = canvas.getContext("2d");
  var ballRadius = 10;
  var ballX = canvas.width/2;       //  xPos of ball
  var ballY = canvas.height-80; //  yPos of ball

  var deltaX = 5;
  var deltaY = -5;

  var paddleHeight = 10;
  var paddleWidth = 75;
  var paddleX = (canvas.width-paddleWidth)/2;
  var rightPressed = false;
  var leftPressed = false;
  var brickColumnCount = 7;
  var brickRowCount = 8;
  var brickWidth = 50;
  var brickHeight = 20;
  var brickPadding = 5;
  var brickOffsetTop = 80;
  var brickOffsetLeft = 50;
  var batPos = 0;
  var score = 0;
  var lives = 3;
  var totBricks=0;
  var crates=0;
  var nextLife = 1000;
  var nBalls = 1;
  var nLevel =1;
  var bricks = [];
  var balls = [];
  var batDelta =0;
  var silverBrickOn=0


// set interval
var tid = setInterval(mycode, 20000);
function mycode() {
batDelta +=25; if (batDelta>150)  batDelta=-25;
  // do some stuff...
  // no need to recall the function (it's an interval, it'll loop forever)
}

batDelta
function resetBricks(level,nBrickRows,nBrickCols) {
    for(var c=0; c<5; c++) {
        balls[c] = { x: canvas.width/2, y: canvas.height-80, deltaX: 3, deltaY: -3 };
    }
    totBricks=0;
    for(var c=0; c<nBrickRows; c++) {
   bricks[c] = [];
   for(var r=0; r<nBrickCols; r++) {
     bricks[c][r] = { x: 0, y: 0, status: 1, color: aCol[0] };
     totBricks++;
   }
 }
}
resetBricks(nLevel,brickRowCount,brickColumnCount);

document.addEventListener("keydown", keyDownHandler, false);
document.addEventListener("keyup", keyUpHandler, false);
document.addEventListener("mousemove", mouseMoveHandler, false);

function collisionDetection() {
  for(var c=0; c<brickRowCount; c++) {
    for(var r=0; r<brickColumnCount; r++) {
      var b = bricks[c][r];
      if(b.status == 1) {
       for (i=0;i<nBalls;i++){
        if(balls[i].x > b.x && balls[i].x < b.x+brickWidth && balls[i].y > b.y && balls[i].y < b.y+brickHeight) {
          balls[i].deltaY *= -1;
          // hitting goldColor
          if (b.color==goldColor) { 
            crates++;
            score+=(crates*50);
            //balls[i].deltaX=Math.floor((Math.random() * 2) + 3);
            //balls[i].deltaY=Math.floor((Math.random() * 2) + 3);
            b.color=aCol[4]
            nBalls++; if(nBalls>4)nBalls=4;
    }
          // hitting silverColor
          if (b.color==silverColor) { 
            crates+=2; 
            score+=(crates*10);
            if (crates>10) {
              balls[i].deltaX = 1;
              balls[i].deltaY = (crates%5)+2;
            }
            //balls[i].deltaX=Math.floor((Math.random() * 3) + 1);
            //balls[i].deltaY=Math.floor((Math.random() * 3) + 1);
            b.color=aCol[4];
            paddleWidth = 50+batDelta ;
          }
          if (b.color==aCol[4]) { 
            b.status = 0; 
            totBricks--;
          }
          else {
            for (var i=0;i<4;i++) {
              if (b.color==aCol[i]) { 
               b.color=aCol[i+1];
               i=4; 
               if (Math.floor((Math.random() * 50) + 1)==9){
                b.color=goldColor;

              }
              if (Math.floor((Math.random() * 100) + 1)==9){
                b.color=silverColor;
                batDelta = 125;
              }
            }
          }
        }   
        score+=(nBalls*nLevel);

        if (score >= nextLife) {lives++;nextLife+=2000}
        if(totBricks == 0) {
          //alert("Good job!");

            //document.location.reload();
            balls = [];
            bricks = [];
            lives++;

            resetBricks(++nLevel,++brickRowCount,brickColumnCount);
            draw();

          }
        }
      }
    }
  }
}
}

function drawBall() {
  for (let i=0;i<nBalls;i++){
    ctx.beginPath();

    ctx.arc(balls[i].x,balls[i].y , ballRadius, 0, Math.PI*2);
  ctx.fillStyle = ballCol[i];

    ctx.fill();
    ctx.closePath();
  }  
}
function drawPaddle() {
  let pWidth = paddleWidth;
  if (nBalls==4) pWidth+=50;
  ctx.beginPath();
  ctx.rect(paddleX, canvas.height-paddleHeight,pWidth , paddleHeight);
  ctx.fillStyle = "#0095DD";
  ctx.fill();
  ctx.closePath();
}
function drawBricks() {
  silverBrickOn=0; 
  for(var c=0; c<brickRowCount; c++) {
    for(var r=0; r<brickColumnCount; r++) {
      if(bricks[c][r].status == 1) {
        var brickX = (r*(brickWidth+brickPadding))+brickOffsetLeft;
        var brickY = (c*(brickHeight+brickPadding))+brickOffsetTop;
        bricks[c][r].x = brickX;
        bricks[c][r].y = brickY;
        ctx.beginPath();
        ctx.rect(brickX, brickY, brickWidth, brickHeight);
        ctx.fillStyle = bricks[c][r].color;

        if (bricks[c][r].color==silverColor) silverBrickOn=1; 

        ctx.fill();
        ctx.closePath();
      }
    }
  }
}
function drawScore() {
  ctx.font = "16px Arial";
  ctx.fillStyle = "#0095DD";
  ctx.fillText("Score: "+score, 8, 20);
  ctx.fillText("Crates: "+crates, 188, 20);
 // ctx.fillText("BatPos: "+batPos.toFixed(1)+ " %", 188, 20);
 ctx.fillText("Level: "+nLevel, 318, 20);
 ctx.fillText("deltaY: "+balls[0].deltaY +" | "+balls[1].deltaY+" | "+balls[2].deltaY+" | "+balls[3].deltaY,8,50);
 ctx.fillText("deltaX: "+balls[0].deltaX +" | "+balls[1].deltaX+" | "+balls[2].deltaX+" | "+balls[3].deltaX,178,50);

if (silverBrickOn) ctx.fillText("batDelta: "+batDelta, 378, 50);


}
function drawLives() {
  ctx.font = "16px Arial";
  ctx.fillStyle = "#0095DD";
  ctx.fillText("Lives: "+lives, canvas.width-75, 20);
}

function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawBricks();
  drawBall();
  drawPaddle();
  drawScore();
  drawLives();
  collisionDetection();

  let pWidth = paddleWidth;
  if (nBalls==4) pWidth+=50;

  // indien rechts uit canvas of links  ..draai richting om
  // if (x+deltaX)>(720-10) of (x+deltaX)<10 

  for (let i=0;i<nBalls;i++){
    if(balls[i].x + balls[i].deltaX > canvas.width-ballRadius || balls[i].x + balls[i].deltaX < ballRadius) {
      balls[i].deltaX *= -1;
    }

  // indien boven uit canvas
  // if (y+deltaY) <10.... draai richting om
  if(balls[i].y + balls[i].deltaY < ballRadius) {
    balls[i].deltaY *= -1;
  }     
  // indien onder uit canvas
  else if(balls[i].y + balls[i].deltaY > canvas.height-ballRadius) {

        // indien hits paddle
      if(balls[i].x > (paddleX-3) && balls[i].x < paddleX+3 + pWidth) {


// evaluate position of the bat ---> xDirection of ball + randomize 2-3
let l = balls[i].x-paddleX ;
let r = paddleX+ pWidth - balls[i].x;
let batPos =l/(l+r)*100;
if (batPos< 50){ balls[i].deltaX =((batPos-60)/10).toFixed(0)-Math.floor(Math.random() * 3);}
if (batPos>50){ balls[i].deltaX =Math.floor(Math.random() * 3); }
if (batPos>60){ balls[i].deltaX =1+Math.floor(Math.random() * 2); }
if (batPos>70){ balls[i].deltaX =2+Math.floor(Math.random() * 2); }
if (batPos>80){ balls[i].deltaX =3+Math.floor(Math.random() * 3); }
if (batPos>90){ balls[i].deltaX =4+Math.floor(Math.random() * 3); }

balls[i].deltaY = -balls[i].deltaY;

}
else {
  if(nBalls>1) {
    if(i < nBalls) {
      nBalls--;
      balls[i]=balls[i+1];
       // reset 
            balls[i+1] = { x: canvas.width/2, y: canvas.height-80, deltaX: 3, deltaY: -3 };

          }
        }
        else { // lost ball
          lives--;
          if(!lives) {  // game over
            //alert("GAME OVER");
            //document.location.reload();
         }
         else {
          paddleWidth = Math.max(paddleWidth,75);
          balls[i].deltaX = 3;  balls[i].deltaY = -3;
          balls[i].x = canvas.width/2;
          balls[i].y = canvas.height-30;
          paddleX = (canvas.width-paddleWidth)/2;
        }
      }
    }
  }

  if(rightPressed && paddleX < canvas.width-paddleWidth) {
    paddleX += 7;
  }
  else if(leftPressed && paddleX > 0) {
    paddleX -= 7;
  }

  balls[i].x += balls[i].deltaX;
  balls[i].y += balls[i].deltaY;
}
requestAnimationFrame(draw);
}
draw();

function keyDownHandler(e) {
  if(e.key == "Right" || e.key == "ArrowRight") {
    rightPressed = true;
  }
  else if(e.key == "Left" || e.key == "ArrowLeft") {
    leftPressed = true;
  }
}

function keyUpHandler(e) {
  if(e.key == "Right" || e.key == "ArrowRight") {
    rightPressed = false;
  }
  else if(e.key == "Left" || e.key == "ArrowLeft") {
    leftPressed = false;
  }
}

function mouseMoveHandler(e) {
  var relativeX = e.clientX - canvas.offsetLeft;
  if(relativeX > 0 && relativeX < canvas.width) {
    paddleX = relativeX - paddleWidth/2;
  }
}

}

//const Sim = new panaderoMinigames('spaceShip');
//const simMove = ref('');

</script>


<template>
<div class="-mt-32 ml-4 scale-50">

    <canvas :id="'breakout'+id" width="260" height="420"></canvas> 

</div>
<footer>
    <div id="footerBreakout"></div>
</footer>



</template>



