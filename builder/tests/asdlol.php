<?php


$skip = 2;
$intro = "2";
$cause = "1";
$fix = "1";

if(empty($intro) OR empty($cause) OR empty($fix)){
	$matched = "no";
}
echo "matched:".$matched."<br>";

if($skip == 1 && $matched == "no"){
	echo "skip";
}elseif($skip == 2 && $matched == "no"){
	echo "<h1>It fails to match intro, cause and fix. It is set to OLD method, counting words. Result: </h1>".$old;
}elseif(!empty($intro) && !empty($cause) && !empty($fix)) {
					echo "some content for intro cause fix.";
				}
	
?>