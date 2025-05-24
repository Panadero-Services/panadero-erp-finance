const RL=9,VG=50,WR=[[-1,0],[-1,1],[0,1],[1,1],[1,0],[1,-1],[0,-1],[-1,-1]],SJ='dbl',BL='black';
function othello(canvas){
    var ctx = canvas.getContext("2d"),bord=new Bord();
    canvas.addEventListener('click',spelerBeurt,false);
    render(); 
    function addEvents(){if(Math.floor((event.layerY-canvas.offsetTop)/VG)==0)bord.kleurOm(Math.floor(RL/2),Math.floor(RL/2),BL);  } 
        canvas.addEventListener(SJ+'click',addEvents,false);
    function spelerBeurt(event){
        if (bord.whoIs =='black'){  
            var aOpt=checkOpties();    
            if (aOpt.length){           
                var y=Math.floor((event.layerY-canvas.offsetTop)/VG), 
                    x=Math.floor((event.layerX-canvas.offsetLeft)/VG);
                if (bord.isGeldig(y,x,bord.whoIs)){  
                    bord.kleurOm(y,x,bord.whoIs);   
                    render(bord.whoIsNot,bord.whoIs);
                }
            } else render(bord.whoIsNot,bord.whoIs);
        }
    }  
    function computerBeurt(){
        var aOpt=checkOpties();    
        if (aOpt.length) bord.kleurOm(parseInt(aOpt[0][0]),parseInt(aOpt[0][1]),bord.whoIs);
        render(bord.whoIsNot,bord.whoIs);
    }
    function checkOpties(){
        var aOpt=[];
        for (var y=0; y<RL; y++){
            for (var x=0; x<RL; x++){
                if (bord.isGeldig(y,x,bord.whoIs)){                 
                    if (y==1 || x==1 || x==(RL-2) || y==(RL-2)) aOpt.push([[y],[x]]);
                    else aOpt.unshift([[y],[x]]);
                    if (y==0 || x==0 || x==RL-1 || y==RL-1){ y=RL; x=RL }
                }
            }
        } return aOpt;
    }
    function render(whoIs='black',whoIsNot='white'){
        bord.whoIs=whoIs; bord.whoIsNot=whoIsNot;
        for (var y=0; y<RL; y++){
            for (var x=0; x<RL; x++){
                ctx.beginPath();
                ctx.fillStyle='green';
                ctx.strokeStyle='silver';
                ctx.rect(x*VG,y*VG,VG,VG);
                ctx.fill();
                ctx.stroke();
                ctx.beginPath();
                ctx.fillStyle=bord.stenen[y][x];
                ctx.strokeStyle=ctx.fillStyle;
                ctx.arc(x*VG+VG/2,y*VG+VG/2,20,0,Math.PI*2);
                ctx.fill();
            }        
        }   if (whoIs=='white')setTimeout(function(){ computerBeurt();},1000);
    }
}
Bord = function(){
    this.stenen=[];
    for (var y=0;y<RL;y++){
        this.stenen.push([]);
        for (var x=0; x<RL; x++) this.stenen[y].push('green');
    } this.initialiseer(); alert('In this special Othello100-version: Enigma:) the goal is to fill ALL FIELDS, black or white. Crack the Code!!!');
}
Bord.prototype.initialiseer = function(){   
    for (var y=0; y<RL; y++) for (var x=0; x<RL; x++) this.stenen[y][x]='green';
    for (var i=0; i<8; i++) this.stenen[Math.floor(RL/2)+WR[i][0]][Math.floor(RL/2)+WR[i][1]]=['black','white'][Math.floor(Math.random()*2)];
};
Bord.prototype.kleurOm = function (rij,kol,kleur){
    for (var i=0; i<8; i++){
        var y=rij+WR[i][0]; var x=kol+WR[i][1];
        if(!(y<0 || y>(RL-1) || x<0 || x>(RL-1))){
            if (this.stenen[y][x]==this.whoIsNot){
                while (this.whoIsNot == this.stenen[y][x]){
                    y+=WR[i][0]; x+=WR[i][1];
                    if (y<0 || y>(RL-1) || x<0 || x>(RL-1)) break;
                    if (this.stenen[y][x]==this.whoIs){     
                        y=rij+WR[i][0]; x=kol+WR[i][1];
                        while (this.stenen[y][x]==this.whoIsNot){
                            this.stenen[y][x]=this.whoIs;
                            y+=WR[i][0]; x+=WR[i][1];
                        }
                    }
                }
            }
        }
    } this.stenen[rij][kol]=this.whoIs;
};
Bord.prototype.isGeldig= function (rij,kol,kleur) {
    if (this.stenen[rij][kol]!=='green') return 0;
    for (var i=0; i<8; i++){
        var y=rij+WR[i][0],x=kol+WR[i][1];         
        if(!(y<0 || y>(RL-1) || x<0 || x>(RL-1))){
            while (this.whoIsNot == this.stenen[y][x]){
                y+=WR[i][0]; x+=WR[i][1];
                if (y<0 || y>(RL-1) || x<0 || x>(RL-1)) break;   
                if (this.stenen[y][x]== this.whoIs) return 1;
            }
        }
    }
};