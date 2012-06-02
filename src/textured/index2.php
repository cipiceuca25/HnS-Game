<?php
session_start();
chdir("/xampp/HomenetSpaces/hnsdesktop/");
include ("db.inc.php");
include ("login.inc.php");

$query = 'SELECT * FROM hns_desktop u JOIN info i ON u.user_id = i.user_id WHERE u.user_id = "' . mysql_real_escape_string($_SESSION['user_id'], $db) . '"';
$result = mysql_query($query, $db) or die(mysql_error($db));

if (mysql_num_rows($result) > 0) {
$row = mysql_fetch_assoc($result);
extract($row);
mysql_free_result($result);
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
<title>Homenet Spaces OS | HTML5 Canvas!</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1, windows-1252" />
<meta name="content-script-type" content="text/javascript" />
<meta name="content-style-type" content="text/css" />
<meta name="author" content="Homenet Spaces Andrew Gerst" />
<meta name="copyright" content="� HnS Desktop" />
<meta name="keywords" content="Homenet, Spaces, HnS, OS, Web, Desktop, The, Place, To, Be, Creative, Andrew, Gerst, Free, Profile, Profiles, Apps, Applications" />
<meta name="description" content="Welcome to Homenet Spaces OS | This is the place to be creative! Feel free to add yourself to our wonderful community by registering! HnS Desktop" />
<script type="text/javascript" src="jquery.js"></script>
<base target="_top" />
<style type="text/css">
div#main {
margin: 0 auto;
width: 780px;
}

#app {
margin-top: 15px;
width: 780px;
}

#holder {
position: relative;
width: 400px;
height: 300px;
left: 125px;
border: 2px solid #333;
}

#sky {
position: absolute;
left: 0;
top: 0;
height: 150px;
width: 400px;
background-color: #ccd;
background-image: url(sky.jpg);
}

#floor {
position: absolute;
left: 0;
top: 150px;
height: 150px;
width: 400px;
background-color: #565;
background-image: url(floor.png);
}

#canvas {
position: absolute;
top: 0;
left: 0;
}

#overlay {
position: absolute;
top: 0;
left: 0;
width: 400px;
height: 300px;
background-image: url(overlay.gif);
}

#map, #underMap {
position: absolute;
}

#code {
position: absolute;
top: 165px;
}
</style>
<script type="text/javascript">
try {
if (top != window) {
top.location.replace(location.href)
}
} catch(ignore){}

var pixelWidth = 2;

function changeResolution(){
	var detail = document.getElementById('hilow').options.selectedIndex;
	pixelWidth = [8,4,2,1][detail];
	samples = [50,100,200,400][detail];
	drawCanvas();
}

var map;
var canvas;
var overlay;
var pic = [];
for (var i = 0; i < 4; i++) pic[i] = new Image(), pic[i].src = 'wall' + i + '.gif';
var pi = Math.PI;
var total = 0;

Number.prototype.range = function(){
	return (this+2*pi)%(2*pi);
}

Number.prototype.roundC = function(){
	return Math.round(this*100)/100;
}

var total=0;
var samples=200;

var arena=[];
arena[0] = [1,1,1,1,1,1,1,1,1,1,1,1,1,1];
arena[1] = [1,0,0,0,0,0,0,0,0,0,0,0,0,1];
arena[2] = [1,0,1,1,1,1,1,1,1,1,1,1,0,1];
arena[3] = [1,0,1,0,0,0,0,0,0,0,0,1,0,1];
arena[4] = [1,0,1,0,0,1,0,1,1,1,0,1,0,1];
arena[5] = [1,0,1,0,1,0,0,0,0,1,0,1,0,1];
arena[6] = [1,0,1,0,0,0,0,1,0,1,0,1,0,1];
arena[7] = [1,0,1,0,1,1,0,0,0,0,0,1,0,1];
arena[8] = [1,0,1,0,0,1,0,1,1,1,0,1,0,1];
arena[9] = [1,0,1,1,0,1,0,0,0,1,0,1,0,1];
arena[10] = [1,0,1,0,0,1,0,1,0,0,0,1,0,1];
arena[11] = [1,0,1,1,1,1,1,1,1,1,1,1,0,1];
arena[12] = [1,0,0,0,0,0,0,0,0,0,0,0,0,1];
arena[13] = [1,1,1,1,1,1,1,1,1,1,1,1,1,1];

var playerPos=[41,31];
var playerDir=Math.PI;
var playerPosZ=1;
var key=[0,0,0,0,0];
var playerVelY=0;
var face=[];
var bit=[];

function wallDistance(theta){
	var dist=[];
	var x = playerPos[0], y = playerPos[1];
	var deltaX, deltaY;
	var distX, distY;
	var stepX, stepY;
	var mapX, mapY
	var atX=Math.floor(x), atY=Math.floor(y);

	for (var i=0; i<samples; i++) {
		theta+=pi/(3*samples)+2*pi;
		theta%=2*pi;
		mapX = atX, mapY = atY;
		deltaX=1/Math.cos(theta);
		deltaY=1/Math.sin(theta);

		if (deltaX>0) {
			stepX = 1;
			distX = (mapX + 1 - x) * deltaX;
		}
		else {
			stepX = -1;
			distX = (x - mapX) * (deltaX*=-1);		
		}
		if (deltaY>0) {
			stepY = 1;
			distY = (mapY + 1 - y) * deltaY;
		}
		else {
			stepY = -1;
			distY = (y - mapY) * (deltaY*=-1);
		}
		

		while (true) {
			if (distX < distY) {
				mapX += stepX;
				if (arena[mapX][mapY]) {
					dist[i]=distX;
					face[i]=2+stepX;
					bit[i]=(y+distX/deltaY*stepY)%1 || 0;
					break;
				}
				distX += deltaX;
			}
			else {
				mapY += stepY;
				if (arena[mapX][mapY]) {
					dist[i]=distY;
					face[i]=1+stepY;
					bit[i]=(x+distY/deltaX*stepX)%1 || 0;
					break;
				}
				distY += deltaY;
			}
		}
	}
	
	return dist;
}


function drawCanvas(){
	map.clearRect(0,0,28,28);
	map.fillStyle="#3366cc";
	map.arc(playerPos[0]*2, playerPos[1]*2, 2, 0, 2*pi, true);
	map.fill();
	map.beginPath();
	map.moveTo(2*playerPos[0], 2*playerPos[1]);

	canvas.clearRect(0,0, 400, 300);
	
	var theta = playerDir-pi/6;

	var dist=wallDistance(theta);
	

	var c;	


	for (var i=0; i<samples; i++) {
		theta+=pi/(3*samples);

		var d2=dist[i];
		var d=d2*Math.cos(theta-playerDir);
		
		var z=1-playerPosZ/2;
		
		var h=300/d;

		canvas.drawImage(pic[face[i]], bit[i]*63, 0, 1, 64, i*pixelWidth, 150-h*z, pixelWidth, h)
	
		map.lineTo(playerPos[0]*2+Math.cos(theta)*d2*2, playerPos[1]*2+Math.sin(theta)*d2*2);
	
	}
	map.fillStyle="#ff0000"
	map.fill();
}

function nearWall(x,y){
	var xx,yy;
	if (isNaN(x)) x=playerPos[0];
	if (isNaN(y)) y=playerPos[1];
	for (var i=-0.1; i<=0.1; i+=0.2) {
		xx=Math.floor(x+i)
		for (var j=-0.1; j<=0.1; j+=0.2) {
			yy=Math.floor(y+j);
			if (arena[xx][yy]) return true;
		}
	}
	return false;
}

function wobbleGun(){
	var mag=playerVelY;
	overlay.style.backgroundPosition=(10+Math.cos(total/6.23)*mag*50)+"px "+(10+Math.cos(total/5)*mag*50)+"px";
}

var jumpCycle=0;

var audio = window.Audio && new Audio("shoot.wav");

function shoot()
{
audio && audio.play();
canvas.save();
canvas.strokeStyle = "#ffff00";
canvas.beginPath();
    
canvas.moveTo(190+xOff, 140+yOff);
canvas.lineTo(250+xOff, 200+yOff);
canvas.closePath();
canvas.stroke();
canvas.restore();
setTimeout('drawCanvas()',100);
}

function update(){

	total++;

	var change=false;

	if (jumpCycle) {
		jumpCycle--;
		change=true;
		playerPosZ = 1 + jumpCycle*(20-jumpCycle)/110;
	}
	else if (key[4]) jumpCycle=20;
	
	if (key[0]) {
		if (!key[1]) {
			playerDir-=0.07; //left
			change=true;
		}
	}
	else if (key[1]) {
		playerDir+=0.07; //right
		change=true;
	}

	if (change) {
		playerDir+=2*pi;
		playerDir%=2*pi;
		document.getElementById("sky").style.backgroundPosition=Math.floor(-playerDir/(2*pi)*2400)+"px 0";
	}

	if (key[2] && !key[3]) {
		if (playerVelY<0.2) playerVelY += 0.04;
	}
	else if (key[3] && !key[2]) {
		if (playerVelY>-0.2) playerVelY -= 0.04;
	}
	else {
		if (playerVelY<-0.02) playerVelY += 0.015;
		else if (playerVelY>0.02) playerVelY -= 0.015;
		else playerVelY=0;
	}
	
	
	if (playerVelY!=0) {

		var oldX=playerPos[0];;
		var oldY=playerPos[1];		
		var newX=oldX+Math.cos(playerDir)*playerVelY;
		var newY=oldY+Math.sin(playerDir)*playerVelY;

		if (!nearWall(newX, oldY)) {
			playerPos[0]=newX;
			oldX=newX;
			change=true;
		}
		if (!nearWall(oldX, newY)) {
			playerPos[1]=newY;
			change=true;
		}

	}
	
	if (playerVelY) wobbleGun();
	if (change) drawCanvas();
}

function changeKey(which, to){
	switch (which){
		case 65:case 37: key[0]=to; break; // left
		case 87: case 38: key[2]=to; break; // up
		case 68: case 39: key[1]=to; break; // right
		case 83: case 40: key[3]=to; break; // down
		case 32: key[4]=to; break; // space bar;
		case 17: key[5]=to; break; // ctrl
		case 66: if (to) { shoot() } break; // b
	}
}
document.onkeydown=function(e){changeKey((e||window.event).keyCode, 1);}
document.onkeyup=function(e){changeKey((e||window.event).keyCode, 0);}

function initUnderMap(){
	var underMap=document.getElementById("underMap").getContext("2d");
	underMap.fillStyle="#fff";
	underMap.fillRect(0,0,200,200);
	underMap.fillStyle="#444";
	for (var i=0; i<arena.length; i++) {
		for (var j=0; j<arena[i].length; j++) {
			if (arena[i][j]) underMap.fillRect(i*2,j*2,2,2);
		}	
	}
}

window.onload=function(){
	var ele = document.getElementById("map");
	if (!ele.getContext) {
	  alert('An error occured creating a Canvas 2D context. This may be because you are using an old browser, if not please contact me and I\'ll see if I can fix the error.');
	  return;
	}
	map=ele.getContext("2d");
	canvas=document.getElementById("canvas").getContext("2d");
	overlay=document.getElementById("overlay");
	document.getElementById("sky").style.backgroundPosition=Math.floor(-playerDir/(2*pi)*2400)+"px 0";
	drawCanvas();
	initUnderMap();
	setInterval(update, 35);
}
</script>
</head>

<body scroll="no">
<!-- Begin page content -->
<div id="main">
<h2>3D Canvas Shooter! | Textured</h2>
<div id="app">
<canvas id="underMap" width="80" height="80"></canvas>
<canvas id="map" width="80" height="80"></canvas>
<div id="holder" style="clear: both;"> 
<div id="sky"></div> 
<div id="floor"></div> 
<canvas id="canvas" width="400" height="300"></canvas> 
<div id="overlay"></div> 
</div> 
<p>Resolution: <select id="hilow"><option>Lower<option>Low<option selected>Medium<option>High</select> <input type="button" onclick="changeResolution()" value="Apply"></p> 
<p>Use the arrow keys to walk around the map. Space bar = jump. B = Shoot.</p>
</div>
<!-- End page content -->
</body>

</html>
<?php mysql_close($db); ?>