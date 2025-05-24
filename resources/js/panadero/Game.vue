<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {computed, onMounted, onActivated, onServerPrefetch, ref} from 'vue'

//import SPACESHIP from '@/panadero/assets/ss1.png';
import SPACESHIP from '@/panadero/assets/ss1.png';
import BULLET from '@/panadero/assets/bullet_red2.png'
import ENEMY1 from '@/panadero/assets/enemy1.png'
import ENEMY2 from '@/panadero/assets/enemy2.png'
import ENEMY3 from '@/panadero/assets/enemy3.png'
import ENEMY4 from '@/panadero/assets/enemy4.png'
import ATOM from '@/panadero/assets/self1.png'
import DIAMOND1 from '@/panadero/assets/diamond.png'
import DIAMOND2 from '@/panadero/assets/diamond.png'

import { useGameStore } from '@/stores/game';
let gameS = useGameStore();


const emit = defineEmits(['status'])


const props = defineProps({
  id: Number,
  cvxWidth: Number,
  cvxHeight: Number, 
  callSign: String, 
  infoPanel: Boolean,
  spriteSize: Number,
  activated: Number

});

gameS.callSign = props.callSign;

var user = props.callSign;
var tokenBalance = 0;
var Credits = 0;
var tokenSymbol = '-';
var Callname = props.callSign;
var gameOn = 0; /// game has started
var gameScale = props.spriteSize; /// assets, sprites, 
var gameVelocity = 0.5; /// default speed- multiplier..for small screens * 0.5 .. large screens * 1.5
var stageVelocity = 1;  /// scenario speed-multiplier

// alter for bigger screen
gameVelocity += gameScale / 30 ; 

Credits = 25;

var canvas;    


/** @notice init game global buttons */


/** @notice init game global variables */
/** @notice launch() will start te game */
async function initGame() {
  /// enable/disable main game buttons based on game and login status
  //document.getElementById("btn-logout").disabled = (user && gameOn==0) ? false : true;
  //document.getElementById("btn-launch").disabled = (gameOn==0) ? false : true;
  //document.getElementById("btn-login").disabled = (!user && gameOn==0) ? false : true;
  //document.getElementById("btn-refresh").disabled = true;

//  if (user){
    // **** bypass New
    //  document.getElementById("wallet").innerHTML = user.get('ethAddress'); 
    //await retrieveTokenBalance();
    // **** bypass New
    //  Credits = await retrieveCredits();
    //Callname = user.get('ethAddress').substr(-4);
  //    Callname = 'jaw';
  //    console.log(user);
  //        startGame(Callname);
  //}

}

/** @notice Moralis login procedure */
async function login() {
   try {
      user = await Moralis.authenticate({ signingMessage: "spaceShip Login" })
      await initGame();
      await retrieveTokenBalance();
      console.log(user.get('ethAddress'));

   } catch(error) {
     console.log(error)
   }
}

/** @notice Moralis logout procedure */
async function logOut() {
  await Moralis.User.logOut();
  clearVariables();
  console.log("logged out");
}

/** @notice Moralis defaults */
async function clearVariables() {
  user = 0;
  tokenBalance = 0;
  Credits = 0 ;
  tokenSymbol = 0;
  Callname = "- -";
  document.getElementById("wallet").innerHTML = 'none';
  document.getElementById("tokenBalance").innerHTML = tokenBalance;
  document.getElementById("tokenSymbol").innerHTML = tokenSymbol ;
  document.getElementById("Credits").innerHTML = Credits ;
  initGame();
  console.log("variables cleared");
}


/** @notice Moralis retrieve user token balance */
async function retrieveTokenBalance() {
    let balances = await loadAssets('bsc testnet');
    let n =await  balances.filter(x=>x.tokenAddress == tokenAddress);
    tokenBalance = (n[0].balance/ (10 ** n[0].decimals));
    document.getElementById("tokenBalance").innerHTML = tokenBalance;
    tokenSymbol = n[0].symbol;
    document.getElementById("tokenSymbol").innerHTML = tokenSymbol ;
}


/** @notice Moralis retrieve player credit from Moralis db */
async function retrieveCredits(){
  const Credits = Moralis.Object.extend("Credits");
  const query = new Moralis.Query(Credits);
  query.equalTo("player", user.get('ethAddress'));
  const object = await query.first();
  let cred = 0;
  if (object) cred = object.attributes.amount;
  document.getElementById("Credits").innerHTML = cred ;
  return (cred);
}


/** @notice Moralis retrieve user all balances */
async function loadAssets(_c) {
  let a = user.get('ethAddress');
  let balances = await Moralis.Web3.getAllERC20({ chain: smartChain, address: a });
  return balances;
}


/** @notice Moralis send tokens */
async function transferTokens(_amount) {
  try {
    user = await Moralis.authenticate({ signingMessage: "transferTokens" })
    const options = {type: "erc20", 
    amount: Moralis.Units.Token(_amount, "18"), 
    receiver: receiverAddress,
    contractAddress: tokenAddress}
    let result = await Moralis.transfer(options);
    let credits = _amount * 25;
    await setCredits(credits, true)
    alert ('succesfully deposit '+ _amount + ' ATPAD for '+ credits+ ' credits');
  } catch(error) {
    console.log(error)
  }
}


/** @notice Moralis write credits record in db */
async function setCredits(_amount, updateDb = false) {
  Credits += _amount;
  document.getElementById("Credits").innerHTML = Credits ;
  if (updateDb) {
    // set moralis db credits
    const mClass = Moralis.Object.extend("Credits");
    const mQuery = new Moralis.Query(mClass);
    mQuery.equalTo("player", user.get('ethAddress'));
    let mObject = await mQuery.first();

    if (!mObject) { 
      // new record
      mObject= new mClass();
      mObject.set("player",user.get("ethAddress"));
    }
    mObject.set("amount",Credits);
    await mObject.save();
  }
}

/// end section 1 



/// @dev section 2 game launch
/*       *     .        *  .    .       *    *   . 
/*          *     .        *  .    *    *   . 
 *  .  .          LAUNCH THE GAME .  *
   *     .      * .        .          * .      * 
  .      * .        .          * .       *   . 
*/

/** @notice launch : check requirements, then start the game! */
async function launch() {


console.log('launch');

  //  check requirements
  if (Credits < 10) {
    alert('Unsufficient Funds !');
    return;
  }
  setCredits(-10, true);
    user = Moralis.User.current();
    if (user) {
      try {
        gameOn = 1;
        document.getElementById("btn-launch").disabled = true;
        document.getElementById("btn-logout").disabled = true;
        gameScale = document.getElementById("gameScale").innerHTML;
        await setCanvasButtons(true);
        await setScaleSizes();
        startGame(user);
      } catch(error) {
      console.log(error)
    }
  }
  else{
    alert ('pse login Metamask 1st !')
  }
}


/** @notice launch : disable or enable canvas buttons */
async function setCanvasButtons(_flag){
  for(let i=1;i<10;i++){
    document.getElementById("btn"+i).disabled = _flag;
  }
}


/** @notice launch : execute refresh button */
async function refresh() {
  await location.reload();
  console.log("refresh()");
}


/** @notice launch : render main buttons on the screen */
//document.getElementById("btn-login").onclick = login;
//document.getElementById("btn-logout").onclick = logOut;
//document.getElementById("btn-launch").onclick = launch;
//document.getElementById("btn-refresh").onclick = refresh;


/** @notice launch : render canvas size based on settings */
async function setScaleSizes(){
  sS_SIZE = { width: gameScale*6, height: gameScale*12 };
  E_SIZE = { width: gameScale*12, height: gameScale*8 };
  E_SIZE2 = { width: gameScale*12, height: gameScale*12 };
  STATION_SIZE = { width: gameScale*75, height: gameScale*75 };
  sS_POSITION = { x: canvas.width/2, y: canvas.height-(sS_SIZE.height +10)};
  if (canvas.height<=400) gameVelocity = 0.8; 
  if (canvas.height<=300) gameVelocity = 0.6; 
}

const GRAVITY = 0.01;
const THRUST = 1;
const MAX_VELOCITY=15;
const degToRad = Math.PI / 180;

//const SPACESHIP = '@/panadero/assets/ss1.png'
//const BULLET = '@/panadero/assets/bullet_red2.png'
//const BULLET_SIZE = { width: 3, height: 3 };
const BULLET_SIZE = { width: gameScale*3, height: gameScale*3 };

var sS_SIZE ;
var sS_POSITION ;
var E_SIZE ;
var E_SIZE2 ;
var STATION_SIZE ;

var canvas;
var ctx;

/** @notice launch : game globals*/
function doCanvas(){

  console.log('doCanvas');
  canvas = document.getElementById('starship'+props.id);
  ctx = canvas.getContext('2d');

  sS_SIZE = { width: gameScale*6, height: gameScale*12 };
  sS_POSITION = { x: canvas.width/2, y: canvas.height*0.75};
  E_SIZE = { width: gameScale*12, height: gameScale*8 };
  E_SIZE2 = { width: gameScale*12, height: gameScale*12 };
  STATION_SIZE = { width: gameScale*75, height: gameScale*75 };

  console.log('E_SIZE', E_SIZE);
}


/// end section 2


/// @dev section 3 class description
/*       *     .        *  .    .       *    *   . 
/*          *     .        *  .    *    *   . 
 *  .  .      class: Game  *        .           *
 *  .  .      class: Scenario  *      .
 *  . *       class: Ship  *                *
 *  .  .        --> inherited  class: SpaceShip  *
 *  .  .        --> inherited  class: Enemmy  *
 *  .  .        --> inherited  class: Friend  *
 *  . *       class: Bullet  *                *
 *     .      * .        .          * .      * 
  .      * .        .          * .       *   . 
*/

/** @notice classes : Game */
class Game{

  /// @notice class game.constructor
  constructor({_paper= true, _lps=6, _fps=600}) {
    this.sS = spaceShip;
    //this.score=0;
    this.originalHigh=0;
    this.stageNr=0;
    this.nftEarned='';
    this.enemies =[];
    this.friends =[];
    this.reset = 1;
    this.crash = 0;
    this.powerUp=0;
    this.tick=0;
    this.creditsEarned=0;    
    this.moonWealths=0;    
    this.version = "SELF-MINIGAME v0.53";  
    this.intro = "SELF-MINIGAME v0.53";  
    this.status = "SELF-MINIGAME v0.53";  
    this.font = ['12px Arial', '12px Arial', '14px Arial'];
    if(gameScale >2) this.font = ['12px Arial', '16px Arial', '20px Arial'];
    this.lps = _lps;  // loops per second
    this.fps = _fps   // frames per second
    this.user;
  }

  /// @notice class game.init
  async init(){
    console.log('SolarSystemGame init()');
    this.sS = new spaceShip(sS_SIZE, sS_POSITION,0,SPACESHIP, this.font);
    this.loop();
  }

  /// @notice class game.loop
  async loop() {
    var T = this;
    setTimeout(function () {
     // T.checkPowerUp();
      T.checkNewStage();
      if (gameS.score > gameS.highScore) gameS.highScore = gameS.score;
      T.loop();
    }, 1000 / this.lps);
  }

  /// @notice class game.checkNewStage
  async checkNewStage(){
    var T=this;
      if (T.reset==1) {
        //T.intro
        if (T.stageNr>0) T.creditsEarned++;
        T.stageNr++;

        if (T.stageNr > 22) T.stageNr=1;

        /// reduce the maxBullets -1 after each stage
        if (T.sS.maxBullets > 1) T.sS.maxBullets--;

        T.intro= "Stage: "+T.stageNr;
        console.log('stage: ',T.stageNr);
        var scene = new Scenario(T.stageNr);
        await scene.init();
        console.log('scene',scene);
        setTimeout(function(){ T.intro='' }, 5000);
        T.friends=[];
        T.enemies =[];
        let e;
        for (let i=0; i<scene.stage.enemy.length;i++) {
          for (let j=0; j<scene.stage.enemy[i];j++) {
            e =  new enemy(scene.enemies[i]);
            T.enemies.push(e);
          }
        }

      // lets randomly change some enemy behaviour
      /// @notice class game.checkNewStage.reverse
      // this function needs to change!!!!
      function reverse() {
        setTimeout(function(){ 
           T.enemies.filter(x => x.behaviour=='reverse').map(a=>{
          if (getRandomInt(1, 5)>3) a.velocity.x= -a.velocity.x;
          if (getRandomInt(1, 5)>3) a.velocity.y= -a.velocity.y;
          });
           reverse();
        }, 24000);
      }
      reverse();

      /// @notice class game.checkNewStage.side
      // this function needs to change!!!!
      function side() {
        setTimeout(function(){ 
           T.enemies.filter(x => x.behaviour=='side').map(a=>{
          if (getRandomInt(1, 10)>7) {a.velocity.x++; a.velocity.y=0}; 
          if (getRandomInt(1, 10)>7) {a.velocity.x=0; a.velocity.y++}; 
          });
           side();
        }, 12000);
      }
      side();

      // this function needs to change!!!!
      // overflows on purpose
      let f;
      for (let i=0; i<scene.stage.friend.length;i++) {
        for (let j=0; j<scene.stage.friend[i];j++) {
          f =  new friend(scene.friends[i].size, {x:0,y:200},0,scene.friends[i].id);
          T.friends.push(f);
          setTimeout(function(){ T.friends[T.friends.length-1].active=0; }, 24000);
        }
      }
    }

    if (T.stageNr % 2 == 0) { 
      // T.powerUp=1;
    }

    if(canvas.height>=800){
     // e= new enemy(STATION_SIZE, {x:100,y:400},0,STATION1,-1,'station');
      //T.enemies.push(e);
    }
  T.reset=0;
  }

  /// @notice class game.updateRtv
  /// renders the game info
  async updateRtv() {
    // gameInfo

      ctx.font = this.font[0];
      ctx.fillStyle = 'white';
      ctx.textAlign = "left";
      ctx.fillText(this.version, 20, 0);

      let remainEnemies = this.enemies.filter( obj => obj.active !== 0);

    if (props.infoPanel) {
      ctx.fillText("posX : "+parseInt(this.sS.position.x) , 10, 35);
      ctx.fillText("posY : "+parseInt(this.sS.position.y) , 10, 50);
      ctx.fillText("angle : "+parseInt(toDegrees(this.sS.angle))%360 , 10, 65);
      ctx.fillText("velocityX : "+parseFloat(this.sS.velocity.x).toFixed(5) , 10, 80);
      ctx.fillText("velocityY : "+parseFloat(this.sS.velocity.y).toFixed(5) , 10, 95);
      ctx.fillText("bullets : "+this.sS.bullets.length , 10, 110);
      ctx.fillText("enemies : "+remainEnemies.length , 10, 125);
      ctx.fillText("reset : "+this.reset , 10, 140);
      ctx.fillText("crash : "+this.crash , 10, 155);
      ctx.fillText("stage : "+this.stageNr , 10, 170);
      ctx.fillText("friends : "+this.friends.length , 10, 185);
      ctx.fillText("SELFs : " + this.moonWealths, 10, 200);
    }

    // score
    ctx.fillStyle = 'white';
    ctx.font = this.font[2];
    ctx.textAlign = "center";
    ctx.fillText(("0".repeat(12)+gameS.score).substr(-12), (canvas.width/2), 20);
    //ctx.fillStyle = '#33ff88';
    //ctx.fillText("earned : " + this.creditsEarned,canvas.width -60 ,20 );

    // highscore
    ctx.fillStyle = 'cyan';
    ctx.font = this.font[1];
    ctx.fillText(("0".repeat(12)+gameS.highScore).substr(-12), (canvas.width/2), 40);
    ctx.fillText(gameS.highScoreHolder, (canvas.width/2), 60);

    if (remainEnemies==0) {
      console.log('stage cleared');
      this.reset=1;
    }
  }

  /// @notice class game.introLine
  async introLine(t, c='white') {
    ctx.font = this.font[1];
    ctx.textAlign = "center";
    ctx.fillStyle = c;
    ctx.fillText(t, (canvas.width/2), (canvas.height/2));
    document.getElementById("infoLine").innerHTML = t;
  }

  /// @notice class game.statusLine
  async statusLine(t, c='white') {
    ctx.font = this.font[1];
    ctx.textAlign = "center";
    ctx.fillStyle = c;
    ctx.fillText(t, (canvas.width/2), (canvas.height-50));
  }

  /// @notice class game.crashShip
  /// this ends the game
  async crashShip() {
    var T = this;
    T.crash++;

    console.log('crash');
    emit('status', 'error');

    let _payload = {  "self":gameS.callSign, 
                      "provider":"spaceShip", 
                      "score":gameS.score, 
                      "stage":T.stageNr,
                      "bonus":T.moonWealths,
                      "nft_earned":'none', 
                      "json" :"{}" 
                  };
    
    console.log('before_setScore', _payload);

      await gameS.setScore('Score',_payload);

    if (gameS.score > game.originalHigh){
      _payload = {"title":"highScore", "nvalue":gameS.score, "description":gameS.callSign, "json" :"{}" };
      await gameS.set('Game',_payload);
      console.log('NewHigh!!!')
    }    
    console.log('done');

    return;
  }
}
/** end classes: game */


/** @notice classes : Scenario */
class Scenario{

  /// @notice class scenario.constructor
  constructor(stage){

    this.enemies =[
      {"id" : ENEMY1, "size" : E_SIZE,"speed" : 0, "type": "fighter", "behaviour": "" },
      {"id" : ENEMY2, "size" : E_SIZE,"speed" : 0 ,"type": "fighter", "behaviour": "reverse"},
      {"id" : ENEMY3, "size" : E_SIZE, "speed": 0, "type": "fighter", "behaviour": "side"},
      {"id" : ENEMY4, "size" : E_SIZE, "speed": 2, "type": "fighter", "behaviour": "" },
      {"id" : ATOM, "size" : E_SIZE2, "speed": 1, "type": "fighter", "behaviour": "" }
    ]; 

    this.friends =[
      {"id" : DIAMOND1, "size" : { "width": 24, "height": 24 },"speed" : 0, "type": "diamond" },
      {"id" : DIAMOND2, "size" : { "width": 16, "height": 16 },"speed" : 0, "type": "diamond2" }
    ]; 

    this.stations =[
      {"id" : "assets/station/station1.png", "size" : STATION_SIZE,"speed" : 0, "type": "station", "behaviour": "" },
    ]; 

    var stages = [
      {"stage": 1, "enemy": [12,0,0,0,0], "station": [0,0,0,0], "speed": 1, "friend":[0,0], "crates": 0},
      {"stage": 2, "enemy": [0,12,0,0,0], "station": [0,0,0,0], "speed": 1,"friend":[1,0], "crates": 0},
      {"stage": 3, "enemy": [0,0,12,0,0], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 4, "enemy": [6,6,6,0,0], "station": [1,0,0,0], "speed": 1,"friend":[0,1], "crates": 0},
      {"stage": 5, "enemy": [0,0,0,12,0], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 6, "enemy": [0,0,0,0,12], "station": [0,0,0,0], "speed": 1,"friend":[1,0], "crates": 0},
      {"stage": 7, "enemy": [4,4,4,4,4], "station": [0,2,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 8, "enemy": [20,0,0,0,0], "station": [0,0,0,0], "speed": 1,"friend":[1,0], "crates": 0},
      {"stage": 9, "enemy": [0,20,0,0,0], "station": [0,0,0,0], "speed": 1,"friend":[0,1], "crates": 0},
      {"stage": 10, "enemy": [0,0,20,0,0], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 11, "enemy": [9,9,9,0,0], "station": [0,0,0,0], "speed": 1,"friend":[1,0], "crates": 0},
      {"stage": 12, "enemy": [0,0,0,20,0], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 13, "enemy": [0,0,0,0,20], "station": [0,0,0,0], "speed": 1,"friend":[0,1], "crates": 0},
      {"stage": 14, "enemy": [10,10,10,10,10], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 15, "enemy": [30,0,0,0,0], "station": [0,0,0,0], "speed": 1,"friend":[1,0], "crates": 0},
      {"stage": 16, "enemy": [0,30,0,0,0], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 17, "enemy": [0,0,30,0,0], "station": [0,0,0,0], "speed": 1,"friend":[1,0], "crates": 0},
      {"stage": 18, "enemy": [0,0,0,30,0], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 19, "enemy": [0,0,0,0,30], "station": [0,0,0,0], "speed": 1,"friend":[0,1], "crates": 0},
      {"stage": 20, "enemy": [12,12,12,12,12], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0},
      {"stage": 21, "enemy": [15,15,15,15,15], "station": [0,0,0,0], "speed": 1,"friend":[0,1], "crates": 0},
      {"stage": 22, "enemy": [20,20,20,20,20], "station": [0,0,0,0], "speed": 1,"friend":[0,0], "crates": 0}
    ];
    this.stage =stages[stage -1];

    var platforms = [
      {startX: 44, endX:104, startY:120, endY:121, color:'green'},
      {startX: 84, endX:144, startY:220, endY:221, color:'white'}
    ];

  }
  
  /// @notice class game.scenario.init
  async init(){
    stageVelocity = this.stage.speed;

    //      this.scene =  this.seedStage(this.stageNr);
    /*
          if (T.stageNr==1){
              T.intro= "Welcome to the prototype of SolarSystem (scenario #1) "+T.stageNr;
              setTimeout(function(){ T.intro="use arrows+<SHIFT> to navigate (f=fire) ";
                setTimeout(function(){ T.intro=" Success astronauts catching diamonds !!";
                            setTimeout(function(){ T.intro=" ";
                      }, 5000); 
                  }, 5000); 
                }, 5000);
          } else {
              T.intro= "Stage: "+T.stageNr;
              setTimeout(function(){ T.intro='' }, 5000);
          }
    */
  }
}
/** end classes: scenario */


/** @notice classes : Ship */
class Ship {

  /// @notice class ship.constructor
  constructor(size,position,angle,shipimg) {
      this.active = 1;

      this.size = size;
      this.position = position;
      this.angle = 0;
      this.shipimg=shipimg;
      this.color = 'white';
      this.fireOn = false;
      this.locked = false;
      this.bullets =[];
      this.maxBullets=1;
      this.shootMode=1;            
      this.rotatingLeft = false;
      this.rotatingRight = false;
      this.velocity = {
        x: 0,
        y: 0,
      };
  }
  
  /// @notice class ship.move
  move() {
      // Angle has to be in radians
      // Change the position based on velocity
      this.position.x += this.velocity.x * gameVelocity * stageVelocity;
      this.position.y += this.velocity.y * gameVelocity * stageVelocity;
      // Move sS to other side when leaving screen
      this.position.x = (canvas.width + this.position.x) % canvas.width;
      this.position.y = (canvas.height + this.position.y) % canvas.height;
      // Turning
      if (this.rotatingLeft) this.angle -= degToRad*2.5;
      if (this.rotatingRight) this.angle += degToRad*2.5;
      // Acceleration
      if (this.engineOn) {
        this.velocity.x += (THRUST / 100) * Math.sin(this.angle);
        this.velocity.y -= (THRUST / 100) * Math.cos(this.angle);
      }
      if (this.engineBoost) {
        this.velocity.x += (THRUST / 30) * Math.sin(this.angle); 
        this.velocity.y -= (THRUST / 30) * Math.cos(this.angle); 
      }
      // Limit maxSpeed
      if (this.velocity.x>MAX_VELOCITY) this.velocity.x=MAX_VELOCITY;
      if (this.velocity.y>MAX_VELOCITY) this.velocity.y=MAX_VELOCITY;
      if (this.velocity.y<-MAX_VELOCITY) this.velocity.y=-MAX_VELOCITY;
      if (this.velocity.x<-MAX_VELOCITY) this.velocity.x=-MAX_VELOCITY;

      // Update the velocity depending on gravity
      this.velocity.y += GRAVITY / 100;
    }

  /// @notice class ship.shooting
    shooting () {
      if (this.bullets.length) {
        for (let bullet of this.bullets) {
          if (bullet.active) {
            bullet.move();
            bullet.draw();
          }
            if ( bullet.position.x < 0 || bullet.position.x > canvas.width || bullet.position.y < 0 || bullet.position.y > canvas.height ){
              bullet.active = 0;
            }
        }
        this.bullets = this.bullets.filter( obj => obj.active !== 0);
      }
    }
  }
/** end classes: ship */


/** @notice classes : SpaceShip */
  class spaceShip  extends Ship{

  /// @notice class spaceShip.constructor
  constructor(size, position, angle, shipimg, _font) {
    super(size,position,angle, shipimg);
    this.engineOn = false;
    this.engineBoost = false;
    this.font = _font;
  }

  /// @notice class spaceShip.engine
  engine() {
    if (this.engineOn || this.engineBoost) {
      const fireYPos = this.size.height / 2 + 5;
      const fireXPos = this.size.width * 0.25;
      ctx.beginPath();
      ctx.moveTo(-fireXPos, fireYPos);
      ctx.lineTo(fireXPos, fireYPos);
      ctx.lineTo(0, fireYPos + Math.random() * 50);
      ctx.lineTo(-fireXPos, fireYPos);
      ctx.closePath();
      ctx.font = this.font[0];
      if (this.engineBoost){
        ctx.fillStyle = 'red';
        ctx.fillText("Afterburner!", 10, 50);
      } else {
        ctx.fillStyle = 'orange';
        ctx.fillText("SELF-MINIGAMfFfE", 10, 50);
      }
      ctx.fill();
    }
    // Flame for engine
      ctx.font = this.font[0];
      ctx.textAlign = "left";
      ctx.fillStyle = "#ee0088";
//      ctx.fillText("Luis", 20, -15);
      ctx.fillText(Callname, 20, -0);
  }

  /// @notice class spaceShip.draw
  draw() {
    const triangleCenterX = this.position.x + 0.5 * this.size.width;
    const triangleCenterY = this.position.y + 0.5 * this.size.height;
    ctx.translate(triangleCenterX, triangleCenterY);
    ctx.rotate(this.angle);
    var img = new Image();
    img.src = this.shipimg;
    ctx.drawImage(img,-this.size.width/2,-this.size.height/2,this.size.width,this.size.height);
  }

}

/** end classes: SpaceShip */


/** @notice classes : enemy */
  class enemy extends Ship{

  /// @notice class enemy.constructor
    constructor(eType) {
      let size = eType.size;
      let position = {x:Math.floor(Math.random() * 800),y:100};
      let angle = 0;
      let shipimg = eType.id;

      super(size, position, angle, shipimg);

      this.extraSpeed = eType.speed;
      this.behaviour  = eType.behaviour;
      this.type = eType.type;

      this.velocity = {
        x: -1+Math.floor(Math.random() * 3)+this.extraSpeed ,
        y: Math.floor(Math.random() * 3)+ this.extraSpeed 
      };

      if (this.type=='station') this.velocity={x:0.1,y:0};
    }

  /// @notice class enemy.shooting
  shooting() {
  }

  /// @notice class enemy.draw
  draw() {
    var img = new Image();
    img.src = this.shipimg;
    if (this.active) {      
      ctx.drawImage(img,this.position.x,this.position.y,this.size.width,this.size.height);
    }
  }
  
  /// @notice class enemy.move
  move() {
      this.position.x += this.velocity.x * gameVelocity * stageVelocity;
      this.position.y += this.velocity.y * gameVelocity * stageVelocity;
      this.position.x = (canvas.width + this.position.x) % canvas.width;
      this.position.y = (canvas.height + this.position.y) % canvas.height;
    }
  }
/** end classes: enemy */


/** @notice classes : Bullet */
class Bullet {

  /// @notice class bullet.constructor
  constructor(size, posX,posY, velX,velY,angle) {
    this.active = 1;
    this.color = 'white';
    this.size = size;
    this.position = rotate(posX+(sS_SIZE.width/2),posY+(sS_SIZE.height/2),posX+(sS_SIZE.width/2),posY,toDegrees(angle));
   // this.position = {x:posX+8,y:posY};
    this.statusOn = false;
    this.velocity = {
      x: velX,
      y: velY
    };
  }

  /// @notice class bullet.draw
  draw() {
    var img = new Image();
    img.src = BULLET;
      ctx.drawImage(img,this.position.x,this.position.y,this.size.width,this.size.height);
  }

  /// @notice class enemy.move
  move(){
    this.position.x+=this.velocity.x*2*gameVelocity;
    this.position.y+=this.velocity.y*2*gameVelocity;
  }
}
/** end classes: bullet */



/** @notice classes : friend */
class friend extends Ship {

  /// @notice class friend.constructor
  constructor(size, position, angle, shipimg, vX=3, vY=0) {
    super(size,position,angle, shipimg);
    this.velocity = {
      x: vX,
      y: vY
    };
  }
  
  /// @notice friend.shooting
  shooting () {
  }

  /// @notice friend.draw
  draw() {
    var img = new Image();
    img.src = this.shipimg;
    this.angle -= degToRad*1;
    if (this.active) ctx.drawImage(img,this.position.x,this.position.y,this.size.width,this.size.height);
  }

  /// @notice friend.move
  move() {
    this.position.x += this.velocity.x;
    this.position.y += this.velocity.y;
    this.position.x = (canvas.width + this.position.x) % canvas.width;
  }
}
/** end classes: friend */

/// end section 3 



/// @dev section 4 game routines
/*       *     .        *  .    .       *    *   . 
/*          *     .        *  .    *    *   . 
 *  .  .          GAME ROUTINES .  *
   *     .      * .        .          * .      * 
  .      * .        .          * .       *   . 
*/

/// @notice routine.draw
//var fps = 1000;
function draw() {
//  setTimeout(function () {
  game.tick++;if(game.tick>game.fps)game.tick=0;
  ctx.clearRect( 0, 0,canvas.width,canvas.height);
  ctx.save();

  game.enemies.forEach((e) => {
    e.draw();
    e.move();
  });

  game.friends.forEach((f) => {
    f.draw();
    f.move();
  });
  
  game.sS.move();
  game.sS.draw();
  game.sS.engine();
  ctx.restore();
  game.sS.shooting();

  handleLaserCollision();

  if (game.sS.fire && game.sS.bullets.length < game.sS.maxBullets) { 
    newBullet();
  }
  game.updateRtv();
  //game.introLine(game.intro);

  if (game.crash==0) requestAnimationFrame(draw);
  //  }, 1000 / fps);
}

/// @notice routine.newBullet
function newBullet(){
  if (game.sS.fire && game.sS.bullets.length < game.sS.maxBullets) { 
    let velX = game.sS.velocity.x;
    let velY = game.sS.velocity.y;
    if(game.sS.shootMode==1){
      velX= 5 * Math.sin(game.sS.angle);
      velY= -5 * Math.cos(game.sS.angle);
    }
    let bullet = new Bullet(BULLET_SIZE,game.sS.position.x,game.sS.position.y,velX,velY,game.sS.angle);
    gameS.score-=10;
    game.sS.bullets.push(bullet);
  }
}

/// @notice routine.handleKeyInput
function handleKeyInput(event) {
  const { keyCode, type } = event;
  const isKeyDown = type === 'keydown' ? true : false;

  if (keyCode === 37) game.sS.rotatingLeft = isKeyDown;
  if (keyCode === 39) game.sS.rotatingRight = isKeyDown;
  if (keyCode === 38) game.sS.engineOn = isKeyDown;
  if (keyCode === 16) game.sS.engineBoost = isKeyDown;
  if (keyCode === 70) game.sS.fire = isKeyDown;
}

// Event Listeners
document.addEventListener('keydown', handleKeyInput);
document.addEventListener('keyup', handleKeyInput);

/// @notice routine.handleLaserCollision
var handleLaserCollision = function() {
  for (let friend of game.friends) {
    if (friend.active && hitTest(game.sS, friend,-10)){
        friend.active = 0;
        game.sS.maxBullets+=3;
        game.moonWealths+=1;
        game.creditsEarned+=2;
        if (game.sS.maxBullets> 25)game.sS.maxBullets=25;
    } 
    for (let bullet of game.sS.bullets) {
      let collision = hitTest(bullet, friend);
      if (collision && bullet.active && friend.active) {

        game.intro= "Don't shoot Gover ..you paperhanded astronaut!!!";
        setTimeout(function(){ game.intro='' }, 10000);

        friend.active = 0;
        bullet.active = 0;
        gameS.score -=10000;
      }
    }    
  }

  for (let enemy of game.enemies) {
    let correction = -10;
    if (enemy.type=='station') correction = -50;
    if (enemy.active && hitTest(game.sS, enemy,correction)){ 
      game.crashShip();
    }
    for (let bullet of game.sS.bullets) {
      let collision = hitTest(bullet, enemy);
      if (collision && bullet.active && enemy.active ) {

        if (enemy.type == 'fighter'){
          console.log('you destroyed an enemy');
          enemy.active = 0;
        }
        
        bullet.active = 0;
        
        // increase enemy speed and frequency of enemy spawns
     //   speedMultiplier += .025;
     //   if (enemySeedFrameInterval > 20) {
     //     enemySeedFrameInterval -= 2;
     //   }
        
        // increase score
        gameS.score +=1000;
       // scoreNode.textContent = score;
      }

    }
  }
}


/// @notice routine.hitTest
var hitTest = function (item1, item2,correction=0) {
  let collision = true;
  if (
    item1.position.x > item2.position.x + item2.size.width+correction ||
    item1.position.y > item2.position.y + item2.size.height+correction ||
    item2.position.x > item1.position.x + item1.size.width+correction ||
    item2.position.y > item1.position.y + item1.size.height+correction
  ) {
    collision = false;
  }
  return collision;
}

/// @notice routine.toRadians
var toRadians = function (angle) {
    return angle * (Math.PI / 180);
}

/// @notice routine.toDegrees
var toDegrees = function (radians) {
    let dg = radians * (180 / Math.PI);
   while (dg < 0 ) dg +=360;
      return dg;
}

/// @notice routine.roundNumber()
var roundNumber = function(number, decimals) {
  decimals = decimals || 5;
  return Math.round(number * Math.pow(10, decimals)) / Math.pow(10, decimals);
}

var MathD = {
  sin: function(number){
      return roundNumber(Math.sin(toRadians(number)));
  },
  cos: function(number){
      return roundNumber(Math.cos(toRadians(number)));
  },
  tan: function(number){
      return roundNumber(Math.tan(toRadians(number)));
  },
  asin: function(number){
      return roundNumber(toDegrees(Math.asin(number)));
  },
  acos: function(number){
     return roundNumber(toDegrees(Math.acos(number)));
  },
  atan: function(number){
     return roundNumber(toDegrees(Math.atan(number)));
  }
};

/// @notice routine.rotate()
function rotate(cx, cy, x, y, angle,anticlock_wise = false) {
    if(angle == 0){
        return {x:parseFloat(x), y:parseFloat(y)};
    }if(anticlock_wise){
        var radians = (Math.PI / 180) * angle;
    }else{
        var radians = (Math.PI / -180) * angle;
    }
    var cos = Math.cos(radians);
    var sin = Math.sin(radians);
    var nx = (cos * (x - cx)) + (sin * (y - cy)) + cx;
    var ny = (cos * (y - cy)) - (sin * (x - cx)) + cy;
    return {x:nx, y:ny};
}

/// @notice routine.freeze()
async function freeze(pause=3) { return new Promise(resolve => { setTimeout(() => { resolve(); }, pause*1000);});}

/// @notice routine.getRandomInt()
function getRandomInt(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min) + min); //The maximum is exclusive and the minimum is inclusive
}


/// INIT THE GAME
var game = new Game(true, 6, 600);

/// @notice startGame
async function startGame(){
  console.log('startGame');
  gameOn = 1;
  gameS.score = 0;
  
  await game.init();

  //alert('startGame');
  // Start the game
  await draw();

}

onMounted(async () => {

    console.log('mounted');
    //  let _payload = {"title":"highScore", "nvalue":225, "description":"init", "json" :"{}" };
    await gameS.getHighScore('Game');
    
  if (gameS.highScore===undefined) gameS.highScore = 0;

    game.originalHigh = gameS.highScore;
    console.log('setGame');

    doCanvas();
    startGame();

    emit('status', 'active');



})

</script>
<template>
  <div v-if="!gameOn" class="h1 text-purple-600 text-xs dark:text-white text-center pt-1">
   <!-- <a @click="startGame">SELF{{gameOn}}</a> -->
  </div>
  <canvas :id="'starship'+id" :width="cvxWidth" :height="cvxHeight" ></canvas>
</template>