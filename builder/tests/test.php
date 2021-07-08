<?php
include("../config.php");
$sel1 = mysqli_query($conn, "SELECT idkeywords,website,original,related1,related2,related3,related4,related5,related6,related7,related8,word2,word3,result1,result2,result3,result4,result5,result6,result7,result8,result9,result10,scraped FROM keywords WHERE `idkeywords`='4'");
		
			$row1 = mysqli_fetch_array($sel1);
			//echo $row1["idkeywords"];
				$kws = "";
				$objeto = "";
				$arreglo="";
				/* $kw = "";
				
				*/
				//echo "keyword id: ";
				
				$objeto = (object)[];
				list($objeto->idkeywords,$objeto->website,$objeto->original,$objeto->related1,$objeto->related2,$objeto->related3,$objeto->related4,$objeto->related5,$objeto->related6,$objeto->related7,$objeto->related8,$objeto->word2,$objeto->word3,$objeto->result1,$objeto->result2,$objeto->result3,$objeto->result4,$objeto->result5,$objeto->result6,$objeto->result7,$objeto->result8,$objeto->result9,$objeto->result10,$objeto->scraped)=$row1;
				$arreglo[]=$objeto;
				
				$kws = $arreglo;
				
			$kw=$kws[0];
			$kw->proxy_ip_port = "37.48.118.4:13081";
			$kw->proxy_user_pass = "phaygarth:H3@dSS2akT41";
			
		//print_r($kw);
		
include '/home/fixcomputererror/public_html/script/simple_html_dom.php';
	function debug($dat){
		if(is_array($dat)||is_object($dat)){
			print_r($dat);
		}else{
			echo $dat."\n";
		}
		flush();
	}
//function to clean html tags
function guardararchivo($filename,$body){
	$fp = fopen($filename, 'w');
	fwrite($fp, $body);
	fclose($fp);
	return $body;
}
function nohtml($r){
	$r=trim(preg_replace("/<[^>]+>/ism"," ",$r));
	$r=trim(preg_replace("/&nbsp;/ism"," ",$r));
	$r=trim(preg_replace("/[ \t\n\r]+/ism"," ",$r));
	return trim(html_entity_decode($r));
}
//function to clean white spaces
function clean($r){
	$r=trim(preg_replace("/[ \t\n\r]+/ism"," ",$r));
	return $r;
}
function title($r){
	$r=trim(preg_replace("/[ \t\n\r]+/ism"," ",$r));
	$r=str_replace(" ...","",$r);	
	$r=explode(" - ",$r);
	$r=$r[0];
	$r=explode(" | ",$r);
	$r=$r[0];
	return $r;
}

//class scrape
class scrape {
	var $ch;
	var $columns_separated="\t";
	var $fields=array();
	var $url="";
	var $followref=false;
	var $urlant="";
	var $headers=array ('User-Agent: Mozilla/5.0 (X11; Linux i686; rv:36.0) Gecko/20100101 Firefox/36.0','Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8','Accept-Language: en-US,en;q=0.5','Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7','Connection: keep-alive');

	//function scraper
	function scraper($obj,$use_proxy, $conn){
		//debug($obj);
		//exit;
		//nicob
		//qwer1234
		//$this->havecookies();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 35);
			curl_setopt($ch, CURLOPT_TIMEOUT, 35);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array ('User-Agent: Mozilla/5.0 (X11; Linux i686; rv:36.0) Gecko/20100101 Firefox/36.0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: en-US,en;q=0.5',
			'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
			'Connection: keep-alive'
			));
		
		
		//GET PROXY
		$proxy="";
		if($use_proxy==1){
			//$where=" WHERE usable=1 ORDER BY times,ok DESC,fail, RAND() LIMIT 1";
			//$where=" WHERE usable=1 ORDER BY times, RAND() LIMIT 1";
			
			flush();
			$proxyrand = rand(1,1);
			/*if($proxyrand == "0"){
				$proxyip="108.59.14.208:13080";
			} elseif($proxyrand == "1"){
				$proxyip="95.211.175.167:13150";
			}*/
			if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			   curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
			/*$proxyrand = rand(0,9);
			if($proxyrand == "0"){
				$proxyip="199.115.116.233:1000";
			} elseif($proxyrand == "1"){
				$proxyip="199.115.116.233:1001";
			} elseif($proxyrand == "2"){
				$proxyip="199.115.116.233:1002";
			} elseif($proxyrand == "3"){
				$proxyip="199.115.116.233:1003";
			} elseif($proxyrand == "4"){
				$proxyip="199.115.116.233:1004";
			} elseif($proxyrand == "5"){
				$proxyip="199.115.116.233:1005";
			} elseif($proxyrand == "6"){
				$proxyip="199.115.116.233:1006";
			} elseif($proxyrand == "7"){
				$proxyip="199.115.116.233:1007";
			} elseif($proxyrand == "8"){
				$proxyip="199.115.116.233:1008";
			} elseif($proxyrand == "9"){
				$proxyip="199.115.116.233:1009";
			}*/

			//echo "Proxy: ".$this->proxy.' Tipo:'.$tipo.' Timeout:'.$proxy->conntimeout.' USADO:'.$proxy->times.' OK:'.$proxy->ok.' FAIL:'.$proxy->fail."\n";
			//$proxy->times=$proxy->times+1;
			//$proxy->update();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

		}
		//END GET PROXY
	
	
		$google_lang="en";
		$google_lookup="www.google.com";


		
		//debug($body);
		if($_GET['uule'] == 1){
			$url="https://".$google_lookup."/search?start=0&num=10&q=".urlencode($obj->original)."&client=google-csbe&hl=".$google_lang."&uule=w+CAIQICIDVVNB";
		} elseif($_GET['near'] == 1) {
			$url="https://".$google_lookup."/search?start=0&num=10&q=".urlencode($obj->original)."&client=google-csbe&hl=".$google_lang."&near=hendersonville,+nc";
		} else {
			$url="https://".$google_lookup."/search?start=0&num=10&q=".urlencode($obj->original)."&client=google-csbe&hl=".$google_lang;
		}
		///START $body
		$tries=0;
		$body="";
		$url2=trim($url2);
		if($url2!=""){
			$url=$url2;
		}

		if($url!="" && !empty($obj->original)){
			curl_setopt($ch, CURLOPT_URL, $url);
			if($use_proxy==1){
				$proxyip = $obj->proxy_ip_port;
				echo "SETTING ".$proxyip."\n";
				curl_setopt($ch, CURLOPT_PROXY, $proxyip);
				
				if(!empty($obj->proxy_user_pass)){
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $obj->proxy_user_pass);
				}
				
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			}else{
				curl_setopt($ch, CURLOPT_PROXY, '');
			}
			$body = curl_exec($ch);
		}
	
		///END $body
		$curlerror = curl_errno($ch);
		if(!$curlerror){
			//debugging
			debug($body);
			//
			$phrases=array();
			$phrases[]=$obj->original;
			$rels=array();
			$founds1=false;
			if(preg_match("/<h3[^>]*>Searches related to/ism",$body)){
				$founds1=true;
				if(preg_match_all("/<p class=\"nVcaUb\"><a href=\"\/search\?[^>]+>(.*)<\/a>/ismU",$body,$match)){
					debug($match);
					//debug($body);
					//$rels=$match[1];
					$rels[]=nohtml($match[1][0]);
					$rels[]=nohtml($match[1][1]);
					$rels[]=nohtml($match[1][2]);
					$rels[]=nohtml($match[1][3]);
					$rels[]=nohtml($match[1][4]);
					$rels[]=nohtml($match[1][5]);
					$rels[]=nohtml($match[1][6]);
					$rels[]=nohtml($match[1][7]);
					$rels[]=nohtml($match[1][8]);
					$rels[]=nohtml($match[1][9]);

					$phrases[]=nohtml($match[1][0]);
					$phrases[]=nohtml($match[1][1]);
					$phrases[]=nohtml($match[1][2]);
					$phrases[]=nohtml($match[1][3]);
					$phrases[]=nohtml($match[1][4]);
					$phrases[]=nohtml($match[1][5]);
					$phrases[]=nohtml($match[1][6]);
					$phrases[]=nohtml($match[1][7]);
					$phrases[]=nohtml($match[1][8]);
					$phrases[]=nohtml($match[1][9]);
					
					$obj->related1=nohtml($match[1][0]);
					if(!empty(nohtml($match[1][0]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][0])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related2=nohtml($match[1][1]);
					if(!empty(nohtml($match[1][1]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][1])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related3=nohtml($match[1][2]);
					if(!empty(nohtml($match[1][2]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][2])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related4=nohtml($match[1][3]);
					if(!empty(nohtml($match[1][3]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][3])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related5=nohtml($match[1][4]);
					if(!empty(nohtml($match[1][4]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][4])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related6=nohtml($match[1][5]);
					if(!empty(nohtml($match[1][5]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][5])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related7=nohtml($match[1][6]);
					if(!empty(nohtml($match[1][6]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][6])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
					$obj->related8=nohtml($match[1][7]);
					if(!empty(nohtml($match[1][7]))){
						//mysqli_query($conn, "INSERT INTO `keywordsqa`(`original`,`original1`,`website`) VALUES('".nohtml($match[1][7])."','".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."')");
					}
				}
			}
			$max2=0;
			$max3=0;
			foreach($phrases as $phrase){
				if(preg_match_all("/([^ \r\n\t\v]+)/ism",$phrase,$match)){
					$cnt=count($match[1]);
					for($i=0;$i<=$cnt-2;$i++){
						$s=$match[1][$i]." ".$match[1][$i+1];
						$k=0;
						foreach($phrases as $rel){
							if(preg_match("/".preg_quote($s,'/')."/ism",$rel)){
								$k++;
							}
						}
						if($k>$max2){
							$max2=$k;
							$obj->word2=$s;
						}
					}
					for($i=0;$i<=$cnt-3;$i++){
						$s=$match[1][$i]." ".$match[1][$i+1]." ".$match[1][$i+2];
						$k=0;
						foreach($phrases as $rel){
							if(preg_match("/".preg_quote($s,'/')."/ism",$rel)){
								$k++;
							}
						}
						if($k>$max3){
							$max3=$k;
							$obj->word3=$s;
						}
					}
				}
			}
			if($obj->word3==""){
				$obj->word3=$obj->word2;
			}
			//$obj->update();
			//mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',scraped='".mysqli_real_escape_string($conn, $obj->scraped)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
			//
			//if(preg_match_all("/<h3 class=\"r\"><a [^>]*href=\"(https?[^\"]+)\"[^>]*>(.*)<\/a><\/h3>(.*)<span class=\"st\">(.*)<\/span><\/div>/ismU",$body,$match)){
			//if(preg_match_all("/<div class=\"rc\"><div class=\"r\"><a [^>]*href=\"(https?[^\"]+)\"[^>]*><h3 class=\"LC20lb\">(.*)<\/h3>(.*)<\/a>(.*)<span class=\"st\">(.*)<\/span></ismU",$body,$match)){
			//if(preg_match_all("/<div class=\"rc\"><div class=\"r\"><a href=\"(https?[^\"]+)\"[^>]*>[^!!!]*<h3 class=\"LC20lb\">([^!!!]*)<\/h3><\/a>([^!!!]*)<span class=\"st\">([^!!!]*)<\/span></ismU",$body,$match)){
			if(preg_match_all("/<div class=\"rc\"><div class=\"r\"><a href=\"(https?[^\"]+)\"[^>]*>[^!!!]*<h3 class=\"LC20lb DKV0Md\">([^!!!]*)<\/h3><div([^!!!]*)<span class=\"st\">([^!!!]*)<\/span></ismU",$body,$match)){
				//debug($match);
				$mat=$match[1];
				$mat1=$match[2];
				$obj->result1title=title($mat1[0]);
				$obj->result2title=title($mat1[1]);
				$obj->result3title=title($mat1[2]);
				$obj->result4title=title($mat1[3]);
				$obj->result5title=title($mat1[4]);
				$obj->result6title=title($mat1[5]);
				$obj->result7title=title($mat1[6]);
				$obj->result8title=title($mat1[7]);
				$obj->result9title=title($mat1[8]);
				$obj->result10title=title($mat1[9]);
				//$mat2=$match[5];
				$mat2=$match[4];
				$obj->result1descr=$mat2[0];
				$obj->result2descr=$mat2[1];
				$obj->result3descr=$mat2[2];
				$obj->result4descr=$mat2[3];
				$obj->result5descr=$mat2[4];
				$obj->result6descr=$mat2[5];
				$obj->result7descr=$mat2[6];
				$obj->result8descr=$mat2[7];
				$obj->result9descr=$mat2[8];
				$obj->result10descr=$mat2[9];
				$obj->result1=$mat[0];
				$obj->result2=$mat[1];
				$obj->result3=$mat[2];
				$obj->result4=$mat[3];
				$obj->result5=$mat[4];
				$obj->result6=$mat[5];
				$obj->result7=$mat[6];
				$obj->result8=$mat[7];
				$obj->result9=$mat[8];
				$obj->result10=$mat[9];
				$obj->scraped=1;
				//$obj->update();
				//debug($body);
				//mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',result1title='".mysqli_real_escape_string($conn, $obj->result1title)."',result2title='".mysqli_real_escape_string($conn, $obj->result2title)."',result3title='".mysqli_real_escape_string($conn, $obj->result3title)."',result4title='".mysqli_real_escape_string($conn, $obj->result4title)."',result5title='".mysqli_real_escape_string($conn, $obj->result5title)."',result6title='".mysqli_real_escape_string($conn, $obj->result6title)."',result7title='".mysqli_real_escape_string($conn, $obj->result7title)."',result8title='".mysqli_real_escape_string($conn, $obj->result8title)."',result9title='".mysqli_real_escape_string($conn, $obj->result9title)."',result10title='".mysqli_real_escape_string($conn, $obj->result10title)."',result1descr='".mysqli_real_escape_string($conn, $obj->result1descr)."',result2descr='".mysqli_real_escape_string($conn, $obj->result2descr)."',result3descr='".mysqli_real_escape_string($conn, $obj->result3descr)."',result4descr='".mysqli_real_escape_string($conn, $obj->result4descr)."',result5descr='".mysqli_real_escape_string($conn, $obj->result5descr)."',result6descr='".mysqli_real_escape_string($conn, $obj->result6descr)."',result7descr='".mysqli_real_escape_string($conn, $obj->result7descr)."',result8descr='".mysqli_real_escape_string($conn, $obj->result8descr)."',result9descr='".mysqli_real_escape_string($conn, $obj->result9descr)."',result10descr='".mysqli_real_escape_string($conn, $obj->result10descr)."',scraped='".mysqli_real_escape_string($conn, $obj->scraped)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
				//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`+1 WHERE `id`='".$obj->website."'");
				//
				
				///////////people also ask additions//////////
				if(preg_match("/<h2[^>]*>People also ask/ism",$body)){
					//echo "are people also ask<br>";
				if(preg_match_all("/\\\\x3cdiv class\\\\x3d\\\\x22mod\\\\x22 data-md\\\\x3d(.*);\}\)\(\);\(function\(\)\{/ismU",$body,$match)){
					//echo "gasesc intrebari";
					//echo "<br><br><br><br><br><div>";
					foreach($match[0] as $element){
						
						$element = str_replace("Search for: ", "", $element);
						$element = str_replace("\\x3c", "<", $element);
						$element = str_replace("\\x3d", "=", $element);
						$element = str_replace("\\x3e", ">", $element);
						$element = str_replace("\\x22", "\"", $element);
						$element = str_replace("\\x26", "&", $element);
						$element = str_replace("\\xbd", "&#xbd;", $element);
						$element = str_replace("&amp;", "&", $element);
						$element = str_replace(" ... ", " ", $element);
						$element = str_replace("...", "", $element);
						$element = str_replace("<b>", "", $element);
						$element = str_replace("</b>", "", $element);
						$element = str_replace("\\u201c", "\"", $element);
						$element = str_replace("\\u201d", "\"", $element);
						$element = str_replace("\\u2013", "-", $element);
						$element = str_replace("–", "-", $element);
						$element = preg_replace("/\'\);\}\)(.*)ion\(\)\{/", "", $element);
						//echo "<hr><textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						$element = str_replace("", "", $element);
						$element = str_replace("\">","\">\n", $element);
						$element = str_replace("</li>","</li>\n", $element);
						$element = str_replace("</ol>","</ol>\n", $element);
						$element = str_replace("</ul>","</ul>\n", $element);
						$element = str_replace("</div>","</div>\n", $element);
						$element = preg_replace("/<li class=\"(.*)\">/", "<li>", $element);
						$element = preg_replace("/<ol class=\"(.*)\">/", "<ol>", $element);
						$element = preg_replace("/<ul class=\"(.*)\">/", "<ul>", $element);
						$element = str_replace("</tr>","</tr>\n", $element);
						$element = str_replace("</th>","</th>\n", $element);
						$element = preg_replace("/<tr class=\"(.*)\">/", "<tr>", $element);
						$element = preg_replace("/<a class=\"w13wLe\" (.*)rows<\/a>/s", "", $element);
						$element = preg_replace("/<a class=\"w13wLe\" (.*)row<\/a>/s", "", $element);
						$element = preg_replace("/<div class=\"ZGh7Vc\">(.*)More items<\/a><\/div>/s", "", $element);
						//$element = preg_replace("/<div class=\"Od5Jsd\">(.*)<\/div>/s", "", $element);
						//echo "<hr><div class=".$element;
						$html = str_get_html($element);
						//paragraph
						if($html->find('div[class="mod"]', 0)){
							$content = $html->find('div[class="mod"] div', 0)->innertext;							
							$content = preg_replace("/<span class=\"kX21rb\">(.*)<\/span>/s", "", $content);
							$content = preg_replace("/<span class=\"(.*)\">/", "", $content);
							$content = str_replace("</span>","", $content);
							//echo "<hr>Question: ".$html->find('div[class="match-mod-horizontal-padding"] a', 0)->plaintext."<br>";
							$question1 = trim($html->find('div[class="match-mod-horizontal-padding"] a', 0)->plaintext);
							//echo "Answer: <textarea rows=\"15\" cols=\"70\">".$content."</textarea><br>";
							$answer = trim($content);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$url = trim($html->find('div[class="r"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$urltitle = trim($html->find('div[class="r"] a h3', 0)->plaintext);
							$date = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($date)){
								$date = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							$date = trim($date);
							//echo "Date: ".$date."<br>";
							//mysql_query("UPDATE keywords SET `question".$question."`='".mysqli_real_escape_string($conn, $question1)."',`answer".$question."`='".mysqli_real_escape_string($conn, $answer)."',`url".$question."`='".mysqli_real_escape_string($conn, $url)."',`urltitle".$question."`='".mysqli_real_escape_string($conn, $urltitle)."',`date".$question."`='".mysqli_real_escape_string($conn, $date)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1") or die(mysql_error());
							//mysqli_query($conn, "INSERT INTO `qa`(`original`,`website`, `question`,`answer`,`url`,`urltitle`,`qdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $question1)."','".mysqli_real_escape_string($conn, $answer)."','".mysqli_real_escape_string($conn, $url)."','".mysqli_real_escape_string($conn, $urltitle)."','".mysqli_real_escape_string($conn, $date)."')") or die(mysql_error());
						
						}
						//echo "<hr>".$element."<br>";
						//echo "<hr><textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						
						
					}
					//echo "</div><br><br>";
					
				}
			}
				///////////people also ask end////////////////
				///////////featured snippets//////////////////
				if(preg_match_all("/<div class=\"bkWMgd\">(.*)About Featured Snippets<\/a><\/span><\/div><\/div><\/div><\/div><\/div>/ismU",$body,$match)){
					//debug($match);
					//echo "<hr>gasesc 1";
					//echo "<br><br><br><br><br><div><b></b>";
					foreach($match[0] as $element){
						$element = str_replace("\\x3c", "<", $element);
						$element = str_replace("\\x3d", "=", $element);
						$element = str_replace("\\x3e", ">", $element);
						$element = str_replace("\\x22", "\"", $element);
						$element = str_replace("\\x26", "&", $element);
						$element = str_replace("\\xbd", "&#xbd;", $element);
						$element = str_replace("&amp;", "&", $element);
						$element = str_replace(" ... ", "", $element);
						$element = str_replace("...", "", $element);
						$element = str_replace("<b>", "", $element);
						$element = str_replace("</b>", "", $element);
						$element = str_replace("\\\\u201c", "\"", $element);
						$element = str_replace("\\\\u201d", "\"", $element);
						$element = str_replace("\\\\u2013", "-", $element);
						$element = str_replace("–", "-", $element);
						$element = preg_replace("/\'\);\}\)(.*)ion\(\)\{/", "", $element);
						$element = str_replace("", "", $element);
						
						$html = str_get_html($element);
						//paragraph
						if($html->find('div[class="LGOjhe"]', 0)){
							//echo "Found: <b>Paragraph</b><br>";
							$snippettype = 1;
							//echo "Title: <br>";
							$snippettitle = "";
							//echo "Content: <textarea rows=\"15\" cols=\"70\">".$html->find('div[class="LGOjhe"]', 0)->plaintext."</textarea><br>";
							$snippetcontent = trim($html->find('div[class="LGOjhe"]', 0)->plaintext);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$snippeturl = trim($html->find('div[class="r"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="r"] a h3', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							//echo "Content: ".$html->find('div[class="LGOjhe"]', 0)->plaintext."<br>";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
							//echo "exista";
						}
						//list
						if($html->find('div[class="di3YZe"]', 0)){
							//echo "Found: <b>List</b><br>";
							$snippettype = 2;
							//echo "Title: ".$html->find('div[class="di3YZe"] div[class="co8aDb"]', 0)->plaintext."<br>";
							$snippettitle = trim($html->find('div[class="di3YZe"] div[class="co8aDb"]', 0)->plaintext);
							$content = $html->find('div[class="di3YZe"] div[class="RqBzHd"]', 0)->innertext;
							$content = str_replace("\">","\">\n", $content);
							$content = str_replace("</li>","</li>\n", $content);
							$content = str_replace("</ol>","</ol>\n", $content);
							$content = str_replace("</ul>","</ul>\n", $content);
							$content = preg_replace("/<li class=\"(.*)\">/", "<li>", $content);
							$content = preg_replace("/<ol class=\"(.*)\">/", "<ol>", $content);
							$content = preg_replace("/<ul class=\"(.*)\">/", "<ul>", $content);
							//echo "Content: <textarea rows=\"15\" cols=\"70\">".$content."</textarea><br>";
							$snippetcontent = trim($content);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$snippeturl = trim($html->find('div[class="r"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="r"] a h3', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							//echo "Content: ".$html->find('div[class="LGOjhe"]', 0)->plaintext."<br>";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`,`snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
							//echo "exista";
						}
						//table
						if($html->find('div[class="webanswers-webanswers_table__webanswers-table"]', 0)){
							//echo "Found: <b>Table</b><br>";
							$snippettype = 3;
							//echo "Title: <br>";
							$snippettitle = "";
							$content = $html->find('div[class="webanswers-webanswers_table__webanswers-table"] table', 0)->outertext;
							$content = str_replace("\">","\">\n", $content);
							$content = str_replace("</tr>","</tr>\n", $content);
							$content = str_replace("</th>","</th>\n", $content);
							$content = preg_replace("/<tr class=\"(.*)\">/", "<tr>", $content);
							//echo "Content: <textarea rows=\"15\" cols=\"70\">".$content."</textarea><br>";
							$snippetcontent = trim($content);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$snippeturl = trim($html->find('div[class="r"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="r"] a h3', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							echo "Date: ".$date."<br>";
							echo "Image: no such thing<br>";
							//echo "exista";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
						}
						//video
						if($html->find('div[class="jTSIHb"]', 0)){
							//echo "Found: <b>Video</b><br>";
							$snippettype = 4;
							//echo "Title: <br>";
							$snippettitle = "";
							$content = $html->find('iframe', 0)->outertext;
							$content = str_replace("\">","\">\n", $content);
							$content = preg_replace("/\" class=\"(.*)\"/", "\"", $content);
							//echo "Content: <textarea rows=\"15\" cols=\"70\">".$content."</textarea><br>";
							$snippetcontent = trim($content);
							//echo "Url: ".$html->find('div[class="jTSIHb"] a', 0)->href."<br>";
							$snippeturl = trim($html->find('div[class="jTSIHb"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="jTSIHb"] a span', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="jTSIHb"] a span', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							//echo "Date: ".$date."<br>";
							//echo "Image: no such thing<br>";
							//echo "exista";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
						}
						
						//echo "<hr><div class=".$element;
						//echo "<textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						//echo "<hr><div ".$element;
					}
					//echo "</div><br><br><br><br><br>";
					
				}
				///////////featured snippets end//////////////
				
				
				/////////////////////////////FOR SCRAPER USE ONLY/////////////////////////////////
				$result1s = "0";
				$result2s = "0";
				$result3s = "0";
				$result4s = "0";
				$result5s = "0";
				$result6s = "0";
				$result7s = "0";
				$result8s = "0";
				$result9s = "0";
				$result10s = "0";
				//mysql_query("INSERT INTO `scrape`(`original`,`word2`,`word3`,`related1`,`related2`,`related3`,`related4`,`related5`,`related6`,`related7`,`related8`,`result1`,`result1s`,`result2`,`result2s`,`result3`,`result3s`,`result4`,`result4s`,`result5`,`result5s`,`result6`,`result6s`,`result7`,`result7s`,`result8`,`result8s`,`result9`,`result9s`,`result10`,`result10s`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."','".mysqli_real_escape_string($conn, $obj->word2)."','".mysqli_real_escape_string($conn, $obj->word3)."','".mysqli_real_escape_string($conn, $obj->related1)."','".mysqli_real_escape_string($conn, $obj->related2)."','".mysqli_real_escape_string($conn, $obj->related3)."','".mysqli_real_escape_string($conn, $obj->related4)."','".mysqli_real_escape_string($conn, $obj->related5)."','".mysqli_real_escape_string($conn, $obj->related6)."','".mysqli_real_escape_string($conn, $obj->related7)."','".mysqli_real_escape_string($conn, $obj->related8)."','".mysqli_real_escape_string($conn, $obj->result1)."','".$result1s."','".mysqli_real_escape_string($conn, $obj->result2)."','".$result2s."','".mysqli_real_escape_string($conn, $obj->result3)."','".$result3s."','".mysqli_real_escape_string($conn, $obj->result4)."','".$result4s."','".mysqli_real_escape_string($conn, $obj->result5)."','".$result5s."','".mysqli_real_escape_string($conn, $obj->result6)."','".$result6s."','".mysqli_real_escape_string($conn, $obj->result7)."','".$result7s."','".mysqli_real_escape_string($conn, $obj->result8)."','".$result8s."','".mysqli_real_escape_string($conn, $obj->result9)."','".$result9s."','".mysqli_real_escape_string($conn, $obj->result10)."','".$result10s."')");
				//////////////////////////END  FOR SCRAPER USE ONLY//////////////////////////////
				
				debug($obj);
			}elseif(preg_match("/\<div class\=\"med card-section\"\>/",$body,$matchh)){
				//if no results from google
					//mysqli_query($conn, "DELETE FROM keywords WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					//debug($body);
					debug("No results from google...");
			}else{
				if(!$founds1){
					$obj->scraped=0;
					//$obj->update();
					//mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',scraped='".mysqli_real_escape_string($conn, $obj->scraped)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					//debug($body);
					debug("POSSIBLE GOOGLE BAN");
				}
			}
		}else{
			$obj->scraped=0;
			//$obj->update();
			//mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',scraped='".mysqli_real_escape_string($conn, $obj->scraped)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
			//
			if($use_proxy==1){
				echo "<br>".curl_error($ch)."<br>";
				echo $curlerror."<br>";
			}
		}
	}
	function setpost($post=""){
		if($post!=""){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}else{
			curl_setopt($ch, CURLOPT_POST, 0);
		}
	}
	function setheaders($headers=array()){
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
	}
	function curlerror(){
		return curl_errno($ch);
	}
	function curlerrortext(){
		return curl_error($ch);
	}	

	function close(){
		curl_close($ch);
	}
}
$scraper=new scrape();
$scraper->scraper($kw,1, $conn);
?>
