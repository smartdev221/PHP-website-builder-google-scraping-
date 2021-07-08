<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
include('config.php');
include 'simple_html_dom.php';

function get_letters($url){
	
	$ch = curl_init();
	$headers = array('Host: www.thesaurus.com','Referer: https://www.thesaurus.com/', 'User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($ch, CURLOPT_PROXY, $proxyip);
	//curl_setopt($ch, CURLOPT_PROXYUSERPWD, "phaygarth:H3@dSS2akT41");
	//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
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
	foreach($html->find('li[class="tcom css-12pz5dc e1jj5rmm1"]') as $element){
		$item['letter'] = $element->find('a', 0)->plaintext;
		$elements[] = $item;
	}
	$details['letters'] = $elements;
	
	return $details;
}

function get_words($url){
	
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
	foreach($html->find('ul[class="css-fq2xu3 e1j8zk4s0"] li') as $element){
		$item['link'] = $element->find('a', 0)->href;
		$item['word'] = $element->find('a', 0)->plaintext;
		$elements[] = $item;
	}
	
	$details['words'] = $elements;
	$details['pages'] = $html->find('li[class="css-3w1ibo e1wvt9ur0"] a', 0)->{'data-page'};
	
	return array($details, $result);
}

$letters = get_letters("https://www.thesaurus.com/list/a");
//$letters['letters'] = array('r','s','t','u','v','w','x','y','z'); //manual override in case of crash
foreach($letters['letters'] as $letter){
	echo "https://www.thesaurus.com/list/".$letter['letter']."<br>";
	for($i = 1; $i<= 100; $i++){
		
		echo $letter['letter']." scraping ... page: ".$i."<br>";
		//try until it works
		for($try = 1; $try <=10; $try++){
			echo "\tTry ".$try."<br>";
			$words = get_words("https://www.thesaurus.com/list/".$letter['letter']."/".$i);
			if(!empty($words[1])){
				break;
			}
			usleep(1000000);
		}
		if(count($words[0]['words']) == 0){
			echo "we're at the break point..source follows:<br>";
			print_r($words[1]);
			break;
		} else {
			if($i == 1){
				$pages = $words[0]['pages'];
			}
			echo "<b>Page ".$i."/".$pages." total</b><br>";
			echo "Total words here:".count($words[0]['words'])."<br><br>";
			foreach($words[0]['words'] as $word){
				//echo "Word: ".$word['word']."<br>";
				mysqli_query($conn, "INSERT INTO `spinwords`(`word`,`link`) VALUES('".mysqli_real_escape_string($conn, $word['word'])."', '".mysqli_real_escape_string($conn, $word['link'])."')");
			}
		}
		usleep(500000);
		//break if we hit max pages
		if($i == $pages){
			break;
		}
	}
	//echo $letter['letter'];
	
}

?>