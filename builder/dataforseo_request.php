<?php
	
	include("../config.php");

	$getconf = mysqli_query($conn, "SELECT name,value_ FROM `config`");

	while($getconfig = mysqli_fetch_object($getconf)){

		if($getconfig->name == "MAX_THREADS"){
			$MAX_THREADS = $getconfig->value_;
		}elseif($getconfig->name == "PAUSED"){
			$PAUSED = $getconfig->value_;
		}elseif($getconfig->name == "MAX_KEYWORDS_X_THREAD"){
			$MAX_KEYWORDS_X_THREAD = $getconfig->value_;
		}elseif($getconfig->name == "SLEEP_THREAD_BETWEEN_KEYWORDS"){
			$SLEEP_THREAD_BETWEEN_KEYWORDS = $getconfig->value_;
		}elseif($getconfig->name == "PROXY"){
			$PROXY = $getconfig->value_;
		}elseif($getconfig->name == "PROXY_IP_PORT"){
			$PROXY_IP_PORT = $getconfig->value_;
		}elseif($getconfig->name == "PROXY_USER_PASSWORD"){
			$PROXY_USER_PASSWORD = $getconfig->value_;
		}elseif($getconfig->name == "RUN_CRONJOBS"){
			$RUN_CRONJOBS = $getconfig->value_;
		}
	}

	if($RUN_CRONJOBS == 0){
		if(getenv('cron') == 1) {
			echo "The script was run from the crontab entry";
			die();
		} else {
			echo "The script was run from a webserver, or something else";
		}
	}
			
	// You can download this file from here https://cdn.dataforseo.com/v3/examples/php/php_RestClient.zip
	require('RestClient.php');
	$api_url = 'https://api.dataforseo.com/';
	
	try {
		// Instead of 'login' and 'password' use your credentials from https://app.dataforseo.com/api-dashboard
		$client = new RestClient($api_url, null, 'phaygarth@btinternet.com', '0121f2b7f2488b63');
	} catch (RestClientException $e) {
		echo "\n";
		print "HTTP code: {$e->getHttpCode()}\n";
		print "Error code: {$e->getCode()}\n";
		print "Message: {$e->getMessage()}\n";
		print  $e->getTraceAsString();
		echo "\n";
		exit();
	}

	$post_array = array();
	$website_check = mysqli_query($conn, "SELECT * FROM `websites` WHERE `built`<`dripfeed_current` AND `done_today`='0' AND `skip`='0' LIMIT 1");

	if(mysqli_num_rows($website_check) > 0){
		while($row = mysqli_fetch_object($website_check)){
			$limit = ($row->dripfeed_current-$row->built);//*4
		
			$sel = mysqli_query($conn, "SELECT idkeywords,original FROM keywords WHERE scraped=0 and `website`='".$row->id."' LIMIT ".$limit);
			if(mysqli_num_rows($sel) > 0){
				while($keyword = mysqli_fetch_object($sel)){
					mysqli_query($conn, "UPDATE `keywords` SET `scraped`='2',`last_action`='g_scraper', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE idkeywords='".$keyword->idkeywords."'");
						$post_array[] = array(
								"language_name" => "English",
								"location_name" => "United States",
								"keyword" => mb_convert_encoding($keyword->original, "UTF-8"),
								"priority" => 1,
								"postback_data" => "advanced",
								"postback_url" => 'http://winhook.org/script_new/admin/dataforseo_result.php?id=$id&keyword_id='.$keyword->idkeywords.''
						);
				}
			}
		}
	}
	//chunk it to max 100 keywords, api limit is 100/request
	$post_array=array_chunk($post_array, 100);
	foreach($post_array as $array){
		if (count($array) > 0) {
		try {
			// POST /v3/serp/google/organic/task_post
			// in addition to 'google' and 'organic' you can also set other search engine and type parameters
			// the full list of possible parameters is available in documentation
			$result = $client->post('/v3/serp/google/organic/task_post', $array);
			print_r($result);
			// do something with post result
		} catch (RestClientException $e) {
			echo "\n";
			print "HTTP code: {$e->getHttpCode()}\n";
			print "Error code: {$e->getCode()}\n";
			print "Message: {$e->getMessage()}\n";
			print  $e->getTraceAsString();
			echo "\n";
		}
		}
	}
	$client = null;
?>