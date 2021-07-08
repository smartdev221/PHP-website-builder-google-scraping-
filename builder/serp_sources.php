<?php
include("../config.php");

$output = fopen(__DIR__."/serp_sources.csv", "w+");

$select = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `source`!=''");
$domains = array();
while($row = mysqli_fetch_object($select)){
	
	$url = parse_url($row->source);
	$host = $url['host'];
	$sel = mysqli_query($conn, "SELECT `built` FROM `keywords` WHERE `idkeywords`='".$row->idkeywords."'");
	$built = mysqli_fetch_object($sel);
	if($built->built == 1){
		$domains[$host] = $domains[$host]+1;
	}
}
foreach($domains as $domain=>$number){
	
fputcsv($output, array($domain, $number));

}

?>