<?php

//file_put_contents(__DIR__."/".$_GET['id'].".html",  gzdecode(file_get_contents("php://input")));

if(!empty($_GET['keyword_id'])){
	$data = gzdecode(file_get_contents("php://input"));
	
	$data = json_decode($data, true);
	file_put_contents(__DIR__."/".$_GET['id']."-json.html", print_r($data,true));
	
	$items = $data['tasks']['0']['result']['0']['items'];
	
	$i_organic = 0;
	$i_video = 0;
	$dta = $_GET['keyword_id']." ";
	foreach($items as $item){
		if($item['type'] == "organic"){
			$i_organic++;
			if($i_organic < 11){
					$dta.="Result ".$i_organic.": ".$item['title']." desc: ".$item['description']." url:".$item['url']."\n";
			}
		}
		if($item['type'] == "people_also_ask"){
			foreach($item['items'] as $pa){
				$dta.="PAA : ".$pa['expanded_element']['0']['title']." desc: ".$pa['expanded_element']['0']['description']."\n";
			}
		}
		if($item['type'] == "related_searches"){
			foreach($item['items'] as $related){
				$dta.="Related : ".$related."\n";
			}
		}
		if($item['type'] == "video"){
			foreach($item['items'] as $video){
				$i_video++;
				$dta.="Video ".$i_video.": ".$video['url']."\n";
			}
		}
		if($item['type'] == "featured_snippet"){
					$dta.="Snippet : ".$item['title']." desc: ".$item['description']." url:".$item['url']."\n";
		}
		
	}
	//print_r($data['tasks']['result']['items']);
	file_put_contents(__DIR__."/".$_GET['id'].".html", $dta);
}

?>
