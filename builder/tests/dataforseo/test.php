<?php
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

$post_array[] = array(
   "language_name" => "English",
   "location_name" => "United States",
   "keyword" => mb_convert_encoding("i cannot receive large files in outlook", "UTF-8"),
   //easy way to rename files in windows 7
   "priority" => 2,
   "postback_data" => "advanced",
   "postback_url" => 'http://winhook.org/script_new/admin/dataforseo/result.php?id=$id&keyword_id=$kid'
);

// this example has a 3 elements, but in the case of large number of tasks - send up to 100 elements per POST request
if (count($post_array) > 0) {
   try {
      // POST /v3/serp/google/organic/task_post
      // in addition to 'google' and 'organic' you can also set other search engine and type parameters
      // the full list of possible parameters is available in documentation
      $result = $client->post('/v3/serp/google/organic/task_post', $post_array);
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
$client = null;
?>