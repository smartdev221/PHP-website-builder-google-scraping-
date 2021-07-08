<?php
include("../config.php");

$select = mysqli_query($conn, "SELECT * FROM `websites` where `id`='".mysqli_real_escape_string($conn, $_GET['id'])."'");
while($row = mysqli_fetch_object($select)){
	$data = array(
		"not_matching"=>$row->serp_not_matched, 
		"words"=>$row->words, 
		"perday"=>$row->perday,
		"spin"=>$row->spin,
		"exclude_paa_title"=>$row->exclude_paa_title,
		"nosnippets"=>$row->nosnippets,
		"dripfeed"=>$row->dripfeed,
		"serptitle"=>$row->serptitle,
		"prefix"=>$row->prefix,
		"title1"=>$row->title1,
		"title2"=>$row->title2,
		"title3"=>$row->title3,
		"title4"=>$row->title4,
		"title5"=>$row->title5,
		"template1"=>$row->template1,
		"template2"=>$row->template2,
		"template3"=>$row->template3,
		"template4"=>$row->template4,
		"template5"=>$row->template5,
		"find_and_replace"=>$row->find_and_replace,
		"wordpress_post_tags"=>$row->wordpress_post_tags);
		
		echo json_encode($data);
}
?>