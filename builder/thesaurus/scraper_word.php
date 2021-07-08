<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
include('config.php');
include 'simple_html_dom.php';

function get_word($url){
	
	$ch = curl_init();
	$headers = array('Host: www.thesaurus.com','Referer: https://www.thesaurus.com/', 'User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_PROXY, '5.79.73.131:13080');
	curl_setopt($ch, CURLOPT_PROXYUSERPWD, "phaygarth:H3@dSS2akT41");
	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch,  CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true); 
	$result = curl_exec ($ch); 
	curl_close ($ch); 
	
	// Create a DOM object
	$html = str_get_html($result);
	//echo $html;
	$tmp = $html->find('li a[class="css-1kg1yv8 eh475bn0"]',0);
	$tmp_orange = $html->find('li a[class="css-1gyuw4i eh475bn0"]',0);
	$tmp_yellow = $html->find('li a[class="css-1n6g4vv eh475bn0"]',0);
	if(count($tmp) >0){
		foreach($html->find('li a[class="css-1kg1yv8 eh475bn0"]') as $element){
			$item['word'] = $element->plaintext;
			$elements[] = $item;
		}
	}elseif(count($tmp_orange) >0){
		foreach($html->find('li a[class="css-1gyuw4i eh475bn0"]') as $element){
			$item['word'] = $element->plaintext;
			$elements[] = $item;
		}
	}else{
		foreach($html->find('li a[class="css-1n6g4vv eh475bn0"]') as $element){
			$item['word'] = $element->plaintext;
			$elements[] = $item;
		}
	}
	
	$details['words'] = $elements;
	$details['definition'] = $html->find('a[class="css-sc11zf ew5makj2"] strong', 0)->plaintext;
	
	return array($details, $result);
}

for($i = 1; $i<=40; $i++){
	
	$select = mysqli_query($conn, "SELECT * FROM `spinwords` WHERE `scraped`='0' LIMIT 1");
	while($row = mysqli_fetch_object($select)){
			mysqli_query($conn, "UPDATE `spinwords` SET `scraped`='1' WHERE `id`='".$row->id."'");

			echo $row->word." -> ".$row->link."<br>";
			
			$array = array();
			
			for($try = 1; $try <=10; $try++){
				echo "\tTry ".$try."<br>";
				$words = get_word($row->link);
				foreach($words[0]['words'] as $word){
					$array[] = trim($word['word']);
				}
				$definition = $words[0]['definition'];
				if(count($words[0]['words']) > 1){
					break;
				}
				echo "<br>";
				usleep(1000000);
			}
			
			mysqli_query($conn, "UPDATE `spinwords` SET `scraped`='1', `definition`='".mysqli_real_escape_string($conn, $definition)."', `spin`='".mysqli_real_escape_string($conn, implode("|", $array))."' WHERE `id`='".$row->id."'");
	}

}

?>