<?php
include("simple_html_dom.php");

$body = file_get_contents("test_source_google.html");



if(preg_match_all("/<div class=\"mod\"[^!!!]+>(.*)About Featured Snippets<\/a><\/span><\/div><\/div><\/div></ismU",$body,$match)){
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
							echo "Found: <b>Paragraph</b><br>";
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
							
							echo $snippetcontent."<hr>".$snippeturl."<br>".$snippeturltitle."<br>".$snippetdate."<br><br>";
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							//echo "Content: ".$html->find('div[class="LGOjhe"]', 0)->plaintext."<br>";
							//mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
							//echo "exista";
						}
						//list
						if($html->find('div[class="di3YZe"]', 0)){
							echo "Found: <b>List</b><br>";
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
							echo $snippetcontent."<hr>".$snippeturl."<br>".$snippeturltitle."<br>".$snippetdate."<br><br>";
							//echo "Date: ".$date."<br>";
							//echo "Image: impossible as src is data/<br>";
							//echo "Content: ".$html->find('div[class="LGOjhe"]', 0)->plaintext."<br>";
							mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`,`snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
							//echo "exista";
						}
						//table
						if($html->find('div[class="webanswers-webanswers_table__webanswers-table"]', 0)){
							echo "Found: <b>Table</b><br>";
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
							echo $snippetcontent."<hr>".$snippeturl."<br>".$snippeturltitle."<br>".$snippetdate."<br><br>";
							
							echo "Date: ".$date."<br>";
							echo "Image: no such thing<br>";
							//echo "exista";
							mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
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
							mysqli_query($conn, "INSERT INTO `featuredsnippets`(`idkeywords`, `original`, `website`, `snippettype`,`snippettitle`,`snippetcontent`,`snippeturl`,`snippeturltitle`,`snippetdate`) VALUES('".$obj->idkeywords."', '".mysqli_real_escape_string($conn, $obj->original)."', '".$obj->website."', '".mysqli_real_escape_string($conn, $snippettype)."','".mysqli_real_escape_string($conn, $snippettitle)."','".mysqli_real_escape_string($conn, $snippetcontent)."','".mysqli_real_escape_string($conn, $snippeturl)."','".mysqli_real_escape_string($conn, $snippeturltitle)."','".mysqli_real_escape_string($conn, $snippetdate)."')") or die(mysql_error());
						
						}
						
						//echo "<hr><div class=".$element;
						//echo "<textarea rows=\"15\" cols=\"70\">".$element."</textarea><br>";
						//echo "<hr><div ".$element;
					}
					//echo "</div><br><br><br><br><br>";
					
				}
				///////////featured snippets end//////////////
				///////////people also ask additions//////////
				if(preg_match("/<h2[^>]*>People also ask/ism",$body)){
					//echo "are people also ask<br>";
				if(preg_match_all("/\\\\x3cdiv class\\\\x3d\\\\x22mod\\\\x22 data-md\\\\x3d(.*);\}\)\(\);\(function\(\)\{/ismU",$body,$match)){
					echo "gasesc intrebari";
					//echo "<br><br><br><br><br><div>";
					print_r($match);
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
						echo "INTREBARE:";
						echo $element."\n\n\n\n\n\n";
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
							
							echo $question1."<hr>".$answer."<br>".$url."<br>".$urltitle."<br>".$date."<br><br>";
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
			}
			
			
			?>