<?php

require('./youtube.php');
#简单的调试
$url = $_GET['url'];
if($url){
  	$yt = new YouTube($url); 
}else{
	$yt = new YouTube('http://www.youtube.be/watch?v=pAk1XIB9Ok0'); 
}
$yt->debug();
