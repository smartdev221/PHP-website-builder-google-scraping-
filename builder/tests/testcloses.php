<?php
function getClosest($search, $arr) {
   $closest = null;
   foreach ($arr as $item) {
      if ($closest === null || abs($search - $closest) > abs($item['words'] - $search)) {
         //$closest = $item['words'];
         $closest = $item['words'];
      }
   }
   return $closest;
}

$array = array();
$array[] = array("words"=>11, "text"=>"1");
$array[] = array("words"=>120, "text"=>"1");
$array[] = array("words"=>124, "text"=>"1");
$array[] = array("words"=>136, "text"=>"1");
$array[] = array("words"=>174, "text"=>"1");
$array[] = array("words"=>85, "text"=>"1");
$array[] = array("words"=>42, "text"=>"1");
$array[] = array("words"=>32, "text"=>"1");
$array[] = array("words"=>1, "text"=>"1");
$array[] = array("words"=>200, "text"=>"1");
$array[] = array("words"=>234, "text"=>"1");
$array[] = array("words"=>135, "text"=>"1");

$array1[] = 11;
$array1[] = 120;
$array1[] = 124;
$array1[] = 136;
$array1[] = 174;
$array1[] = 85;
$array1[] = 42;
$array1[] = 32;
$array1[] = 1;
$array1[] = 200;
$array1[] = 234;
$array1[] = 135;

echo "<br>";
	$a=$array1;
	if(count($a)) {
		$a = array_filter($a);
		//echo array_sum($a);
		echo $average = array_sum($a)/count($a);
	}
	echo "startclosest".getClosest(round("83.4285714286"), $array)."-closest";
?>