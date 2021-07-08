<?php
include("simple_html_dom.php");

$body = file_get_contents("test_source_google.html");

function title($title){
	
	return $title;
	
}

if(preg_match_all("/<div class=\"rc\"[^!!!]*><div class=\"yuRUbf\"><a href=\"(https?[^\"]+)\"[^>]*>[^!!!]*<h3 class=\"LC20lb[^!!!]+\"><span>([^`]*)<\/span><\/h3>[^!!!]*<div([^!!!]*)<span class=\"aCOpRe\">[^!!!]{0,}<span>([^^]*)<\/span></ismU",$body,$match)){
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
				///videos
					if(preg_match_all("/<div class=\"y8AWGd llvJ5e\"[^!!!]*><a href=\"(.*)\"[^!!!]+>/ismU",$body,$vidmatch)){
						debug($vidmatch);
						$vidmat=$vidmatch[1];
						$obj->vidresult1=$vidmat[0];
						$obj->vidresult2=$vidmat[1];
						$obj->vidresult3=$vidmat[2];
						$obj->vidresult4=$vidmat[3];
						$obj->vidresult5=$vidmat[4];
						$obj->vidresult6=$vidmat[5];
						$obj->vidresult7=$vidmat[6];
						$obj->vidresult8=$vidmat[7];
						$obj->vidresult9=$vidmat[8];
						$obj->vidresult10=$vidmat[9];
						$obj->video=1;
					}
				///videos end
				
				//$obj->update();
				//debug($body);
				logger($obj->idkeywords, $obj->original, 'Google scraped', $conn);
				//mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',result1title='".mysqli_real_escape_string($conn, $obj->result1title)."',result2title='".mysqli_real_escape_string($conn, $obj->result2title)."',result3title='".mysqli_real_escape_string($conn, $obj->result3title)."',result4title='".mysqli_real_escape_string($conn, $obj->result4title)."',result5title='".mysqli_real_escape_string($conn, $obj->result5title)."',result6title='".mysqli_real_escape_string($conn, $obj->result6title)."',result7title='".mysqli_real_escape_string($conn, $obj->result7title)."',result8title='".mysqli_real_escape_string($conn, $obj->result8title)."',result9title='".mysqli_real_escape_string($conn, $obj->result9title)."',result10title='".mysqli_real_escape_string($conn, $obj->result10title)."',result1descr='".mysqli_real_escape_string($conn, $obj->result1descr)."',result2descr='".mysqli_real_escape_string($conn, $obj->result2descr)."',result3descr='".mysqli_real_escape_string($conn, $obj->result3descr)."',result4descr='".mysqli_real_escape_string($conn, $obj->result4descr)."',result5descr='".mysqli_real_escape_string($conn, $obj->result5descr)."',result6descr='".mysqli_real_escape_string($conn, $obj->result6descr)."',result7descr='".mysqli_real_escape_string($conn, $obj->result7descr)."',result8descr='".mysqli_real_escape_string($conn, $obj->result8descr)."',result9descr='".mysqli_real_escape_string($conn, $obj->result9descr)."',result10descr='".mysqli_real_escape_string($conn, $obj->result10descr)."',scraped='".mysqli_real_escape_string($conn, $obj->scraped)."', vidresult1='".mysqli_real_escape_string($conn, $obj->vidresult1)."',vidresult2='".mysqli_real_escape_string($conn, $obj->vidresult2)."',vidresult3='".mysqli_real_escape_string($conn, $obj->vidresult3)."',vidresult4='".mysqli_real_escape_string($conn, $obj->vidresult4)."',vidresult5='".mysqli_real_escape_string($conn, $obj->vidresult5)."',vidresult6='".mysqli_real_escape_string($conn, $obj->vidresult6)."',vidresult7='".mysqli_real_escape_string($conn, $obj->vidresult7)."',vidresult8='".mysqli_real_escape_string($conn, $obj->vidresult8)."',vidresult9='".mysqli_real_escape_string($conn, $obj->vidresult9)."',vidresult10='".mysqli_real_escape_string($conn, $obj->vidresult10)."', video='".mysqli_real_escape_string($conn, $obj->video)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
				//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`+1 WHERE `id`='".$obj->website."'");
				//
				///////////featured snippets//////////////////
				$snippets = 0;
				if(preg_match_all("/<div class=\"[^!!!]{0,}mod\"[^!!!]+>(.*)About Featured Snippets<\/a><\/span><\/div><\/div><\/div></ismU",$body,$match)){
					//debug($match);
					//echo "<hr>gasesc 1";
					//echo "<br><br><br><br><br><div><b></b>";
					foreach($match[0] as $element){
						$snippets++;
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
							$snippeturl = trim($html->find('div[class="rc"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="rc"] a h3', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							$snippetcontent = preg_replace("/<div\sclass=\"rvIhN\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/<div\sclass=\"Od5Jsd\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/<span\sclass=\"kX21rb\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/".$snippetdate."/", "", $snippetcontent);
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							//echo "Content: ".$html->find('div[class="LGOjhe"]', 0)->plaintext."<br>";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
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
							$snippeturl = trim($html->find('div[class="rc"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="rc"] a h3', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							//remove 'More rows'
							$snippetcontent = preg_replace("/<div class=\"ZGh7Vc\">.*<\/div>/sU", "", $snippetcontent);
							//
							$snippetcontent = preg_replace("/<div\sclass=\"rvIhN\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/<div\sclass=\"Od5Jsd\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/<span\sclass=\"kX21rb\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/".$snippetdate."/", "", $snippetcontent);
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							//echo "Content: ".$html->find('div[class="LGOjhe"]', 0)->plaintext."<br>";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`,`snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
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
							$snippeturl = trim($html->find('div[class="rc"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$snippeturltitle = trim($html->find('div[class="rc"] a h3', 0)->plaintext);
							$snippetdate = $html->find('span[class="kX21rb"]', 0)->plaintext;
							if(empty($snippetdate)){
								$snippetdate = $html->find('div[class="Od5Jsd"]', 0)->plaintext;
							}
							//remove 'More rows'
							$snippetcontent = preg_replace("/<div class=\"ZGh7Vc\">.*<\/div>/sU", "", $snippetcontent);
							//
							$snippetcontent = preg_replace("/<div\sclass=\"rvIhN\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/<div\sclass=\"Od5Jsd\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/<span\sclass=\"kX21rb\">.*<\/div>/sU", "", $snippetcontent);
							$snippetcontent = preg_replace("/".$snippetdate."/", "", $snippetcontent);
							echo "Date: ".$date."<br>";
							echo "Image: no such thing<br>";
							//echo "exista";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
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
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
						}
						
						//echo "<hr><div class=".$element;
						//echo "<textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						//echo "<hr><div ".$element;
					}
					//echo "</div><br><br><br><br><br>";
					if($snippets == 0){
						$chk = mysqli_query($conn, "SELECT * FROM `config`");
						while($chkd = mysqli_fetch_object($chk)){
							if($chkd->name == "EMAIL_SENT"){
								$EMAIL_SENT = $chkd->value_;
							}elseif($chkd->name == "FAILS_BEFORE_CRON_STOP"){
								$FAILS_BEFORE_CRON_STOP = $chkd->value_;
							}elseif($chkd->name == "CURRENT_FAILS"){
								$CURRENT_FAILS = $chkd->value_;
							}
						}
						if($CURRENT_FAILS < $FAILS_BEFORE_CRON_STOP){
							//mysqli_query($conn, "UPDATE `config` SET `value_`=`value_`+1 WHERE `name`='CURRENT_FAILS'");
						} elseif($CURRENT_FAILS >= $FAILS_BEFORE_CRON_STOP){
							if($EMAIL_SENT == 0){
								file_put_contents(__DIR__."/test_source_google_snippet.html", $body);
								$message = date('m-d-Y H:i:s')."\r\nPossible google template change, there is snippet text but none matching.\r\nPlease check.\r\nhttp://winhook.org/script_new/admin/test_source_google_snippet.html";
								mail('catalin_smecheru96@yahoo.com', 'template change ?! snippets missing', $message);
								//mysqli_query($conn, "UPDATE `config` SET `value_`='0' WHERE `name`='RUN_CRONJOBS'");
								//mysqli_query($conn, "UPDATE `config` SET `value_`='1' WHERE `name`='EMAIL_SENT'");
							}
						}
					}
				}
				///////////featured snippets end//////////////
				///////////people also ask additions//////////
				if(preg_match("/<h2[^>]*>People also ask/ism",$body)){
					//echo "are people also ask<br>";
				if(preg_match_all("/\\\\x3cdiv class\\\\x3d\\\\x22mod\\\\x22 data-md\\\\x3d(.*);\}\)\(\);\(function\(\)\{/ismU",$body,$match)){
					//echo "gasesc intrebari";
					//echo "<br><br><br><br><br><div>";
					$questions = 0;
					foreach($match[0] as $element){
						$questions++;
						
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
							$url = trim($html->find('div[class="rc"] a', 0)->href);
							//echo "Url title: ".$html->find('div[class="r"] a h3', 0)->plaintext."<br>";
							$urltitle = trim($html->find('div[class="rc"] a h3', 0)->plaintext);
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
								//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '5','".mysqli_real_escape_string($conn, $question1)."','".mysqli_real_escape_string($conn, $answer)."','','','')") or die(mysql_error());
							} else {
								//mysqli_query($conn, "INSERT INTO `qa`(`idkeywords`, `original`,`website`, `question`,`answer`,`url`,`urltitle`,`qdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $question1)."','".mysqli_real_escape_string($conn, $answer)."','".mysqli_real_escape_string($conn, $url)."','".mysqli_real_escape_string($conn, $urltitle)."','".mysqli_real_escape_string($conn, $date)."')") or die(mysql_error());
							}
						}
						//echo "<hr>".$element."<br>";
						//echo "<hr><textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						
						
					}
					//echo "</div><br><br>";
					
				}
				/*if($questions == 0){
						$chk = mysqli_query($conn, "SELECT `value_` FROM `config` WHERE `name`='EMAIL_SENT'");
						$chkd = mysqli_fetch_object($chk);
						if($chkd->value_ == 0){
							file_put_contents(__DIR__."/test_source_google_paa.html", $body);
							$message = date('m-d-Y H:i:s')."\r\nPossible google template change, there is PAA text but none matching.\r\nPlease check.\r\nhttp://winhook.org/script_new/admin/test_source_google_paa.html";
							mail('catalin_smecheru96@yahoo.com', 'template change ?! paa missing', $message);
							mysqli_query($conn, "UPDATE `config` SET `value_`='0' WHERE `name`='RUN_CRONJOBS'");
							mysqli_query($conn, "UPDATE `config` SET `value_`='1' WHERE `name`='EMAIL_SENT'");
						}
				}*/
				if($questions == 0){
						$chk = mysqli_query($conn, "SELECT * FROM `config`");
						while($chkd = mysqli_fetch_object($chk)){
							if($chkd->name == "EMAIL_SENT"){
								$EMAIL_SENT = $chkd->value_;
							}elseif($chkd->name == "FAILS_BEFORE_CRON_STOP"){
								$FAILS_BEFORE_CRON_STOP = $chkd->value_;
							}elseif($chkd->name == "CURRENT_FAILS"){
								$CURRENT_FAILS = $chkd->value_;
							}
						}
						if($CURRENT_FAILS < $FAILS_BEFORE_CRON_STOP){
							//mysqli_query($conn, "UPDATE `config` SET `value_`=`value_`+1 WHERE `name`='CURRENT_FAILS'");
						} elseif($CURRENT_FAILS >= $FAILS_BEFORE_CRON_STOP){
							if($EMAIL_SENT == 0){
								file_put_contents(__DIR__."/test_source_google_paa.html", $body);
								$message = date('m-d-Y H:i:s')."\r\nPossible google template change, there is PAA text but none matching.\r\nPlease check.\r\nhttp://winhook.org/script_new/admin/test_source_google_paa.html";
								mail('catalin_smecheru96@yahoo.com', 'template change ?! paa missing', $message);
								//mysqli_query($conn, "UPDATE `config` SET `value_`='0' WHERE `name`='RUN_CRONJOBS'");
								//mysqli_query($conn, "UPDATE `config` SET `value_`='1' WHERE `name`='EMAIL_SENT'");
							}
						}
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
			}elseif(preg_match("/\<div class\=\"med card-section\"\>/",$body,$matchh)){
				//if no results from google
					//mysqli_query($conn, "DELETE FROM keywords WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					debug($body);
					debug("No results from google...");
			}else{
				if(!$founds1){
					$obj->scraped=0;
					//$obj->update();
					//mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',scraped='".mysqli_real_escape_string($conn, $obj->scraped)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					if(preg_match("/Web results/i",$body)){
						//check for keyword including PDF
						if(preg_match("/\bpdf\b/i",$obj->original)){
							//mysqli_query($conn, "DELETE FROM keywords WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
						} else {
							$chk = mysqli_query($conn, "SELECT * FROM `config`");
							while($chkd = mysqli_fetch_object($chk)){
								if($chkd->name == "EMAIL_SENT"){
									$EMAIL_SENT = $chkd->value_;
								}elseif($chkd->name == "FAILS_BEFORE_CRON_STOP"){
									$FAILS_BEFORE_CRON_STOP = $chkd->value_;
								}elseif($chkd->name == "CURRENT_FAILS"){
									$CURRENT_FAILS = $chkd->value_;
								}
							}
							if($CURRENT_FAILS < $FAILS_BEFORE_CRON_STOP){
								//mysqli_query($conn, "UPDATE `config` SET `value_`=`value_`+1 WHERE `name`='CURRENT_FAILS'");
							} elseif($CURRENT_FAILS >= $FAILS_BEFORE_CRON_STOP){
								if($EMAIL_SENT == 0){
									file_put_contents(__DIR__."/test_source_google.html", $body);
									$message = date('m-d-Y H:i:s')."\r\nPossible google template change.\r\nPlease check.\r\nhttp://winhook.org/script_new/admin/test_source_google.html";
									mail('catalin_smecheru96@yahoo.com', 'template change ?!', $message);
									//mysqli_query($conn, "UPDATE `config` SET `value_`='0' WHERE `name`='RUN_CRONJOBS'");
									//mysqli_query($conn, "UPDATE `config` SET `value_`='1' WHERE `name`='EMAIL_SENT'");
									echo 'Template change';
								}
							}
						}
						
					}
					debug($body);
					debug("POSSIBLE GOOGLE BAN");
					if($obj->original !=''){
						logger($obj->idkeywords, $obj->original, 'Possible Google ban', $conn);
					}
				}
			}
			
			
			?>