<?php
include('../config.php');

function find_and_replace($original_text, $find_and_replace_website,$conn){

	
	$select = mysqli_query($conn, "SELECT * FROM `config_global` WHERE `name`='FIND_AND_REPLACE'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
				$find_and_replace_website = explode("|", $find_and_replace_website);
				
				$value = str_replace('sitename', $find_and_replace_website[0], $row->value);
				$value = str_replace('siteurl', $find_and_replace_website[1], $value);
				
				$look = explode("\r\n", $value);
				
				foreach($look as $name){
					$parts = explode('|', $name);

					$original_text = preg_replace_callback('/'.$parts[0].'/i', function ($matches) use ($parts) {
				
					if(ucfirst(substr($matches[0], 0, 1)) == substr($matches[0], 0, 1)){
						$text = ucfirst($parts[1]);
					} else {
						$text = $parts[1];
					}
					return $text;
				}, $original_text);

				}
		}
	}
	
	return $original_text;
}
$original_text = "This site is Windowsreport.com (or Windows Report) and it's homepage is https://windowsreport.com.";

echo find_and_replace($original_text, 'My Site|mysite.com', $conn);

?>