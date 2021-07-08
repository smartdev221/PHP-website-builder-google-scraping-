<?php
include '/home/fixcomputererror/public_html/script/simple_html_dom.php';
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
			
			flush();
			
			if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			   curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}

			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

		}
		//END GET PROXY
	
	
		$url="https://www.bing.com/search?q=".urlencode($obj->original);
		
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
			$founds1=false;
			if(preg_match("/<span class=\"sb_count\">/",$body)){
				$founds1=true;
				
			}
						
			//$obj->update();
			///mysqli_query($conn, "UPDATE keywords SET scraped_bing='".mysqli_real_escape_string($conn, $obj->scraped_bing)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
			//
			//debug($body);
			if(preg_match_all("/<li class=\"b_algo\"><h2><a href=\"(https?[^\"]+)\"[^>]*>[^!!!]*([^^]*)<\/a><\/h2>/ismU",$body,$match)){
				//debug($match);
				$mat=$match[1];
				$mat1=$match[2];
				
				$obj->scraped_bing=1;
				
				//$obj->update();
				//debug($body);
				///mysqli_query($conn, "UPDATE keywords SET scraped_bing='".mysqli_real_escape_string($conn, $obj->scraped_bing)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
				///mysqli_query($conn, "UPDATE `websites` SET `scraped_bing`=`scraped_bing`+1 WHERE `id`='".$obj->website."'");
				//
				///////////featured snippets//////////////////
					//debug($match);
					//echo "<hr>gasesc 1";
					//echo "<br><br><br><br><br><div><b></b>";
								debug($body);			
						$html = str_get_html($body);
						//paragraph
						if($html->find('ul[class="b_vList b_divsec"]', 0)){
							$paragraph = $html->find('div[class="tab-content"]', 0);
							echo "Found: <b>Paragraph</b><br>";
							$snippettype = 1;
							//echo "Title: <br>";
							$snippettitle = "";
							//echo "Content: <textarea rows=\"15\" cols=\"70\">".$html->find('div[class="LGOjhe"]', 0)->plaintext."</textarea><br>";
							$snippetcontent = trim($paragraph->find('ul[class="b_vList b_divsec"]', 0)->plaintext);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$snippeturl = trim($paragraph->find('div a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = "";
							$snippetdate = "";							
							
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							echo "Content: ".$snippetcontent."<br>";
							if(!empty($snippetcontent)){
								///mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`, `bing`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '1', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
							}
							//echo "exista";
						}
						//list
						if($html->find('ol[class="b_dList"]', 0)){
							$list = $html->find('div[class="tab-content"]', 0);
							echo "Found: <b>List</b><br>";
							$snippettype = 2;
							//echo "Title: ".$html->find('div[class="di3YZe"] div[class="co8aDb"]', 0)->plaintext."<br>";
							$snippettitle = "";
							$content = $list->find('ol[class="b_dList"]', 0)->innertext;
							$content = str_replace("\">","\">\n", $content);
							$content = str_replace("</li>","</li>\n", $content);
							$content = str_replace("</ol>","</ol>\n", $content);
							$content = str_replace("</ul>","</ul>\n", $content);
							$content = preg_replace("/<li class=\"(.*)\">/", "<li>", $content);
							$content = preg_replace("/<ol class=\"(.*)\">/", "<ol>", $content);
							$content = preg_replace("/<ul class=\"(.*)\">/", "<ul>", $content);
							echo "Content: <textarea rows=\"15\" cols=\"70\">".$content."</textarea><br>";
							$snippetcontent = trim($content);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$snippeturl = trim($list->find('div a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = "";
							$snippetdate = "";
							
							if(!empty($snippetcontent)){					
								///mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`,`bing`,`snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '1', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
							}
							//echo "exista";
						}
						//table
						if($html->find('div[class="b_snippet"]', 0)){
							$table = $html->find('div[class="b_snippet"]', 0);
							echo "Found: <b>Table</b><br>";
							$snippettype = 3;
							//echo "Title: <br>";
							$snippettitle = "";
							$content = $table->find('table', 0)->innertext;
							$content = str_replace("\">","\">\n", $content);
							$content = str_replace("</tr>","</tr>\n", $content);
							$content = str_replace("</th>","</th>\n", $content);
							$content = preg_replace("/<tr class=\"(.*)\">/", "<tr>", $content);
							$content = preg_replace("/h=\"(.*)\"/U", "<tr>", $content);
							echo "Content: <textarea rows=\"15\" cols=\"70\">".$content."</textarea><br>";
							$snippetcontent = trim($content);
							//echo "Url: ".$html->find('div[class="r"] a', 0)->href."<br>";
							$snippeturl = trim($table->find('a[class="b_moreLink"]', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = "";
							$snippetdate = "";
														
							echo "Date: ".$date."<br>";
							echo "Image: no such thing<br>";
							//echo "exista";
							if(!empty($snippetcontent)){
								///mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`, `bing`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '1', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
							}
						}
												
						//echo "<hr><div class=".$element;
						//echo "<textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						//echo "<hr><div ".$element;
					
					//echo "</div><br><br><br><br><br>";
					
				///////////featured snippets end//////////////
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
							$answer = preg_replace("/<div\sclass=\"rvIhN\">.*<\/div>/sU", "", $answer);
							$answer = preg_replace("/<div\sclass=\"Od5Jsd\">.*<\/div>/sU", "", $answer);
							$answer = preg_replace("/<span\sclass=\"kX21rb\">.*<\/div>/sU", "", $answer);
							$answer = preg_replace("/".$date."/", "", $answer);
							if($snippets == 0 && preg_match('/what/i', $question1)){
								$snippets = 1;
								mysqli_query($conn, "INSERT INTO `featuredsnippets`(`original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '5','".mysqli_real_escape_string($conn, $question1)."','".mysqli_real_escape_string($conn, $answer)."','','','')") or die(mysql_error());
							} else {
								mysqli_query($conn, "INSERT INTO `qa`(`original`,`website`, `question`,`answer`,`url`,`urltitle`,`qdate`) VALUES('".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $question1)."','".mysqli_real_escape_string($conn, $answer)."','".mysqli_real_escape_string($conn, $url)."','".mysqli_real_escape_string($conn, $urltitle)."','".mysqli_real_escape_string($conn, $date)."')") or die(mysql_error());
							}
						}
						//echo "<hr>".$element."<br>";
						//echo "<hr><textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						
						
					}
					//echo "</div><br><br>";
					
				}
			}
				///////////people also ask end////////////////

				
				
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
			}else{
				if(!$founds1){
					$obj->scraped_bing=0;
					//$obj->update();
					///mysqli_query($conn, "UPDATE keywords SET scraped_bing='".mysqli_real_escape_string($conn, $obj->scraped_bing)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					debug($body);
					debug("POSSIBLE BING BAN");
				}
			}
		}else{
			$obj->scraped_bing=0;
			//$obj->update();
			///mysqli_query($conn, "UPDATE keywords SET scraped_bing='".mysqli_real_escape_string($conn, $obj->scraped_bing)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
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
?>
