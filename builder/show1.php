<?php

include("../config.php");

function relative($content){
	
	preg_match_all('/<a\s+href="([A-z:\/.\-_&]+)"(?:.+)?>(.+)<\/a>/', $content, $matches);
	print_r($matches);
	if(count($matches[0]) > 0){
		$i = 0;
		//foreach($matches as $match){
			for($i =0; $i<count($matches[0]); $i++){
			//print_r($match[0]);
			if(!preg_match("/^http/", $matches[1][$i])){
				
				echo "shit...".$matches[1][$i]."\n<br>";
				echo "shit 1...".$matches[2][$i]."\n<br>";
				
				$content = str_replace($matches[0][$i], $matches[2][$i], $content);
			}
			echo "<br><br><br>\n\n\n";
			
			//$i++;
		}
	} 
	
	return $content;
}

$sel = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `id`='435'");
$data = mysqli_fetch_object($sel);

echo relative($data->content)."<br><br><br><br>";
echo "<b>TRANSLATED:</b><br>";
echo $data->content_de;

?>