<?php
include("../config.php");

$ptitle = "windows vista appdata cleanup fix";
$select = mysqli_query($conn, "SELECT * FROM `config_global`") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
			echo $ptitle."<br>";
			if($row->name == "LOOK_FOR"){
				$look = explode("\r\n", $row->value);
				foreach($look as $pm){
					if(preg_match("/".$pm."/i", $ptitle)){
						$prefix_found = 1;
						echo $pm." found"."<br>";
					}
				}
			}
			if($row->name == "PREFIX_LIST"){
				$prefixes = explode("\r\n", $row->value);
				shuffle($prefixes);
				echo $prefixes[0]."<br>";
			}
		}
	}
	
function domainmatch($url){
	$info = parse_url($url);
	$host = $info['host'];
	$host_names = explode(".", $host);
	$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
	
	return $bottom_host_name;
}

echo domainmatch("https://support.microsoft.com");
?>