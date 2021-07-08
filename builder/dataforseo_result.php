<?php
include("../config.php");

if(!empty($_GET['keyword_id'])){
	$data = gzdecode(file_get_contents("php://input"));
	
	$data = json_decode($data, true);
	//file_put_contents(__DIR__."/".$_GET['id']."-json.html", print_r($data,true));
	
	$items = $data['tasks']['0']['result']['0']['items'];
	
	$i_organic = 0;
	$i_video = 0;
	$i_related = 0;
	$snippets = 0;
	$idkeywords = $_GET['keyword_id'];
	$sel = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `idkeywords`='".mysqli_real_escape_string($conn, $idkeywords)."'");
	if(mysqli_num_rows($sel) > 0){
		$obj = mysqli_fetch_object($sel);
		foreach($items as $item){
			if($item['type'] == "organic"){
				$i_organic++;
				if($i_organic < 11){
						mysqli_query($conn, "UPDATE `keywords` SET `result".$i_organic."`='".mysqli_real_escape_string($conn, $item['url'])."',`result".$i_organic."title`='".mysqli_real_escape_string($conn, $item['title'])."',`result".$i_organic."descr`='".mysqli_real_escape_string($conn, str_replace('...', '', $item['description']))."', `scraped`='1' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");

						$dta.="Result ".$i_organic.": ".$item['title']." desc: ".$item['description']." url:".$item['url']."\n";
				}
			}
			if($item['type'] == "people_also_ask"){
				foreach($item['items'] as $pa){
					$dta.="PAA : ".$pa['expanded_element']['0']['featured_title']." desc: ".$pa['expanded_element']['0']['description']."\n";
					
					if($snippets == 0 && preg_match('/what/i', $pa['expanded_element']['0']['featured_title'])){
						$snippets = 1;
						mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '5','".mysqli_real_escape_string($conn, $pa['expanded_element']['0']['featured_title'])."','".mysqli_real_escape_string($conn, str_replace('...', '', $pa['expanded_element']['0']['description']))."','','','')") or die(mysqli_error($conn));
					} else {
						mysqli_query($conn, "INSERT INTO `qa`(`idkeywords`, `original`,`website`, `question`,`answer`,`url`,`urltitle`,`qdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $pa['expanded_element']['0']['featured_title'])."','".mysqli_real_escape_string($conn, str_replace('...', '', $pa['expanded_element']['0']['description']))."','".mysqli_real_escape_string($conn, $pa['expanded_element']['0']['url'])."','','')") or die(mysqli_error($conn));
					}
				}
			}
			if($item['type'] == "related_searches"){
				foreach($item['items'] as $related){
					$i_related++;
					mysqli_query($conn, "UPDATE `keywords` SET `related".$i_related."`='".mysqli_real_escape_string($conn, $related)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");

					$dta.="Related ".$i_related.": ".$related."\n";
				}
			}
			if($item['type'] == "video"){
				foreach($item['items'] as $video){
					$i_video++;
					mysqli_query($conn, "UPDATE `keywords` SET `vidresult".$i_video."`='".mysqli_real_escape_string($conn, $video['url'])."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");

					$dta.="Video ".$i_video.": ".$video['url']."\n";
				}
			}
			if($item['type'] == "featured_snippet"){
				$snippets++;
					mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '1','".mysqli_real_escape_string($conn, $item['title'])."','".mysqli_real_escape_string($conn, str_replace('...', '', $item['description']))."','".mysqli_real_escape_string($conn, $item['url'])."','','')") or die(mysqli_error($conn));
							
						$dta.="Snippet : ".$item['title']." desc: ".$item['description']." url:".$item['url']."\n";
			}
			
		}
		mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`+1 WHERE `id`='".$obj->website."'");
	}
	//file_put_contents(__DIR__."/".$_GET['id'].".html", $dta);
}
?>