<?php

require('./youtube.php');
#简单的调试
$v = $_GET['v'];
if($url){
	$yt = new YouTube($v); 
}else{
	$yt = new YouTube('http://www.youtube.com/watch?v=iQDm6XzgbPw&list=PL14BFF58590E9CE26'); 
}
$yt->debug();
