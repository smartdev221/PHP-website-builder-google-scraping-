<?php
ini_set('memory_limit', '2048M');

include("../../config.php");

function domain_path($url){
	$info = parse_url($url);
	$host = $info['host'];
	$path = explode("/", $info['path']);
	$host_path = $host. "/" . $path[1];
	
	return $host_path;
}

$select = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1'");
$domains = array();
while($row = mysqli_fetch_object($select)){
	
	for($i = 1; $i<= 10; $i++){
		$domain = domain_path($row->{'result'.$i});
		$domains[$domain][]= $row->{'result'.$i}."<br>";
	}
	
}
echo "COUNT:".count($domains)."<br>";
array_multisort(array_map('count', $domains), SORT_DESC, $domains);

//print_r($domains);
$i = 0;
foreach($domains as $domain=>$link){
	if($i <= 500){
		echo $domain." => ".count($link)."<br>";
	}
	$i++;
	foreach($link as $l){
		if(preg_match('/topic|forum|thread/', $l)){
			$banned[$domain][] = $l;
			//$banned[] = $l;
		}
	}
}
array_multisort(array_map('count', $banned), SORT_DESC, $banned);

//print_r($banned);
foreach($banned as $domain=>$link){
	//echo $domain." => ".count($link)."<br>";
}
?>