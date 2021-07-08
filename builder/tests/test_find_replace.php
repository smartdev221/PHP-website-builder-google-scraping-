<?php

$original = "This site is Windowsreport.com (or Windows Report) and it's homepage is https://windowsreport.com.";

$find_and_replace= "Windows Report|My Site
windowsreport.com|mysite.com";
$look = explode("\r\n", $find_and_replace);


foreach($look as $name){
	$parts = explode('|', $name);

	$original = preg_replace_callback('/'.$parts[0].'/i', function ($matches) use ($parts) {
		
		
     if(ucfirst(substr($matches[0], 0, 1)) == substr($matches[0], 0, 1)){
		$text = ucfirst($parts[1]);
	} else {
		$text = $parts[1];
	}
	return $text;
}, $original);
	
}

echo $original;


?>