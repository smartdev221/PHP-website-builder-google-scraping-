<?php
include("../config.php");
include '/home/fixcomputererror/public_html/script/simple_html_dom.php';

function get_source($url){
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array ('User-Agent: Mozilla/5.0 (X11; Linux i686; rv:36.0) Gecko/20100101 Firefox/36.0',
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
	'Accept-Language: en-US,en;q=0.5',
	'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	'Connection: keep-alive'
	));
	curl_setopt($ch, CURLOPT_URL, $url);
	
	$body = curl_exec($ch);
	
	$html = str_get_html($body);
	
	foreach($html->find('li[class="square"]') as $element){
		$item1['link'] = $element->find('img', 0)->{'data-original'};
		$elements[] = $item1;
	}
	
	
	return $elements;
}
function download($url, $conn){

	$urlinfo = parse_url($url);
	
	//explode path to get image
	$imgname = explode("/", $urlinfo['path']);
	$imgname = array_reverse($imgname);
	//extracting image from path and looking for extension
	$extension = explode(".", $imgname[0]);
	$extension = array_reverse($extension);

	$ch = curl_init();
	$headers = array('Host: '.$urlinfo['host'],'Referer: '.$urlinfo['scheme'].'://'.$urlinfo['host'], 'User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//$fp = fopen('progr/'.$name.'.png', 'wb');
	//curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch,  CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	
	if($extension[0] == "jpg" OR $extension[0] == "png"){

	$result = curl_exec ($ch); 
	
	if(!file_exists(__DIR__.'/avatars/')){
		mkdir(__DIR__.'/avatars/');
	}
	//check if image was downloaded
	if(!empty($result)){
		//file_put_contents($location.'/'.$name.'.'.$extension[0], $result);
		mysqli_query($conn, "INSERT INTO `avatars`(`extension`) VALUES('".$extension[0]."')");
		$id = mysqli_insert_id($conn);
		mysqli_query($conn, "UPDATE `avatars` set `filename`='".$id.'.'.$extension[0]."' WHERE `id`='".$id."'");
		file_put_contents(__DIR__.'/avatars/'.$id.'.'.$extension[0], $result);
		
		echo $id.'.'.$extension[0]."<br>";
	}
	
	}
}
$asd = get_source("https://uifaces.co/?provider%5B%5D=9&provider%5B%5D=7&provider%5B%5D=5&provider%5B%5D=11&provider%5B%5D=13&provider%5B%5D=8&from_age=18&to_age=50&gender%5B%5D=malehttps://uifaces.co/?provider%5B%5D=9&provider%5B%5D=7&provider%5B%5D=5&provider%5B%5D=11&provider%5B%5D=13&provider%5B%5D=8&from_age=18&to_age=50&gender%5B%5D=male");


foreach($asd as $image){
	download($image['link'], $conn);
}

//print_r($asd);

?>