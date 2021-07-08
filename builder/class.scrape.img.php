<?php
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
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 35);//20
			curl_setopt($ch, CURLOPT_TIMEOUT, 35);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array ('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36',
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
			$proxyrand = rand(1,1);// rand(0,1);
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
		$url="https://".$google_lookup."/search?start=0&num=10&q=".urlencode($obj->original)."&tbm=isch&client=google-csbe&hl=".$google_lang;
		
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
			$phrases=array();
			$phrases[]=$obj->original;
			$rels=array();
			$founds1=false;
			if(preg_match("/<h3[^>]*>Searches related to/ism",$body)){
				$founds1=true;
				if(preg_match_all("/<p class=\"nVcaUb\"><a href=\"\/search\?[^>]+>(.*)<\/a>/ismU",$body,$match)){
					debug($match);
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
					$obj->related2=nohtml($match[1][1]);
					$obj->related3=nohtml($match[1][2]);
					$obj->related4=nohtml($match[1][3]);
					$obj->related5=nohtml($match[1][4]);
					$obj->related6=nohtml($match[1][5]);
					$obj->related7=nohtml($match[1][6]);
					$obj->related8=nohtml($match[1][7]);
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
			mysqli_query($conn, "UPDATE keywords SET images='1' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
			//
			//if(preg_match_all("/\"ou\":\"(.*)\",\"/ismU",$body,$match)){
			if(preg_match_all("/,\[\"(.+\.\w{2,3})\",\d{3,4},\d{3,4}]/imU",$body,$match) or preg_match_all("/\"ou\":\"(.*)\",\"/ismU",$body,$match)){
				//debug($match);
				$mat=$match[1];
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
				$obj->result11=$mat[10];
				$obj->result12=$mat[11];
				$obj->result13=$mat[12];
				$obj->result14=$mat[13];
				$obj->result15=$mat[14];
				$obj->result16=$mat[15];
				$obj->result17=$mat[16];
				$obj->result18=$mat[17];
				$obj->result19=$mat[18];
				$obj->result20=$mat[19];
				
				//preg_match_all("/<span class=\"dtviD\">(.*)<\/span>/ismU",$body,$match1);
				preg_match_all("/<span class=\"hIOe2\">(.*)<\/span>/ismU",$body,$match1);
				$obj->keywordtop1=nohtml($match1[1][0]);
				$obj->keywordtop2=nohtml($match1[1][1]);
				$obj->keywordtop3=nohtml($match1[1][2]);
				$obj->keywordtop4=nohtml($match1[1][3]);
				$obj->keywordtop5=nohtml($match1[1][4]);
				$obj->keywordtop6=nohtml($match1[1][5]);
				$obj->keywordtop7=nohtml($match1[1][6]);
				$obj->keywordtop8=nohtml($match1[1][7]);
				$obj->keywordtop9=nohtml($match1[1][8]);
				$obj->keywordtop10=nohtml($match1[1][9]);
				$obj->keywordtop11=nohtml($match1[1][10]);
				$obj->keywordtop12=nohtml($match1[1][11]);
				$obj->keywordtop13=nohtml($match1[1][12]);
				$obj->keywordtop14=nohtml($match1[1][13]);
				$obj->keywordtop15=nohtml($match1[1][14]);
				$obj->keywordtop16=nohtml($match1[1][15]);
				$obj->images=1;
				//$obj->update();
				//debug($body);
				logger($obj->idkeywords, $obj->original, 'Google images scraped', $conn);
				mysqli_query($conn, "UPDATE keywords SET imgresult1='".mysqli_real_escape_string($conn, $obj->result1)."',imgresult2='".mysqli_real_escape_string($conn, $obj->result2)."',imgresult3='".mysqli_real_escape_string($conn, $obj->result3)."',imgresult4='".mysqli_real_escape_string($conn, $obj->result4)."',imgresult5='".mysqli_real_escape_string($conn, $obj->result5)."',imgresult6='".mysqli_real_escape_string($conn, $obj->result6)."',imgresult7='".mysqli_real_escape_string($conn, $obj->result7)."',imgresult8='".mysqli_real_escape_string($conn, $obj->result8)."',imgresult9='".mysqli_real_escape_string($conn, $obj->result9)."',imgresult10='".mysqli_real_escape_string($conn, $obj->result10)."',imgresult11='".mysqli_real_escape_string($conn, $obj->result11)."',imgresult12='".mysqli_real_escape_string($conn, $obj->result12)."',imgresult13='".mysqli_real_escape_string($conn, $obj->result13)."',imgresult14='".mysqli_real_escape_string($conn, $obj->result14)."',imgresult15='".mysqli_real_escape_string($conn, $obj->result15)."',imgresult16='".mysqli_real_escape_string($conn, $obj->result16)."',imgresult17='".mysqli_real_escape_string($conn, $obj->result17)."',imgresult18='".mysqli_real_escape_string($conn, $obj->result18)."',imgresult19='".mysqli_real_escape_string($conn, $obj->result19)."',imgresult20='".mysqli_real_escape_string($conn, $obj->result20)."',keywordtop1='".mysqli_real_escape_string($conn, $obj->keywordtop1)."',keywordtop2='".mysqli_real_escape_string($conn, $obj->keywordtop2)."',keywordtop3='".mysqli_real_escape_string($conn, $obj->keywordtop3)."',keywordtop4='".mysqli_real_escape_string($conn, $obj->keywordtop4)."',keywordtop5='".mysqli_real_escape_string($conn, $obj->keywordtop5)."',keywordtop6='".mysqli_real_escape_string($conn, $obj->keywordtop6)."',keywordtop7='".mysqli_real_escape_string($conn, $obj->keywordtop7)."',keywordtop8='".mysqli_real_escape_string($conn, $obj->keywordtop8)."',keywordtop9='".mysqli_real_escape_string($conn, $obj->keywordtop9)."',keywordtop10='".mysqli_real_escape_string($conn, $obj->keywordtop10)."',keywordtop11='".mysqli_real_escape_string($conn, $obj->keywordtop11)."',keywordtop12='".mysqli_real_escape_string($conn, $obj->keywordtop12)."',keywordtop13='".mysqli_real_escape_string($conn, $obj->keywordtop13)."',keywordtop14='".mysqli_real_escape_string($conn, $obj->keywordtop14)."',keywordtop15='".mysqli_real_escape_string($conn, $obj->keywordtop15)."',keywordtop16='".mysqli_real_escape_string($conn, $obj->keywordtop16)."',images='".mysqli_real_escape_string($conn, $obj->images)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
				mysqli_query($conn, "UPDATE `websites` SET `scraped_img`=`scraped_img`+1 WHERE `id`='".$obj->website."'");
				//
				
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
				$obj->proxy_ip_port = "hidden";
				$obj->proxy_user_pass = "hidden";
				debug($obj);
			}elseif(preg_match("/did not match any image results/",$body,$matchh)){
				//if no results from google
					mysqli_query($conn, "DELETE FROM keywords WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					debug($body);
					debug("No results from google...");
			}else{
				if(!$founds1){
					$obj->images=0;
					//$obj->update();
					mysqli_query($conn, "UPDATE keywords SET images='".mysqli_real_escape_string($conn, $obj->images)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
					//
					debug($body);
					debug("POSSIBLE GOOGLE BAN");
					if($obj->original !=''){
						logger($obj->idkeywords, $obj->original, 'Possible Google images ban', $conn);
					}
				}
			}
		}else{
			$obj->images=0;
			//$obj->update();
			mysqli_query($conn, "UPDATE keywords SET original='".mysqli_real_escape_string($conn, $obj->original)."',related1='".mysqli_real_escape_string($conn, $obj->related1)."',related2='".mysqli_real_escape_string($conn, $obj->related2)."',related3='".mysqli_real_escape_string($conn, $obj->related3)."',related4='".mysqli_real_escape_string($conn, $obj->related4)."',related5='".mysqli_real_escape_string($conn, $obj->related5)."',related6='".mysqli_real_escape_string($conn, $obj->related6)."',related7='".mysqli_real_escape_string($conn, $obj->related7)."',related8='".mysqli_real_escape_string($conn, $obj->related8)."',word2='".mysqli_real_escape_string($conn, $obj->word2)."',word3='".mysqli_real_escape_string($conn, $obj->word3)."',result1='".mysqli_real_escape_string($conn, $obj->result1)."',result2='".mysqli_real_escape_string($conn, $obj->result2)."',result3='".mysqli_real_escape_string($conn, $obj->result3)."',result4='".mysqli_real_escape_string($conn, $obj->result4)."',result5='".mysqli_real_escape_string($conn, $obj->result5)."',result6='".mysqli_real_escape_string($conn, $obj->result6)."',result7='".mysqli_real_escape_string($conn, $obj->result7)."',result8='".mysqli_real_escape_string($conn, $obj->result8)."',result9='".mysqli_real_escape_string($conn, $obj->result9)."',result10='".mysqli_real_escape_string($conn, $obj->result10)."',images='".mysqli_real_escape_string($conn, $obj->images)."' WHERE idkeywords= '".$obj->idkeywords."' LIMIT 1");
			//
			if($use_proxy==1){
				echo "<br>".curl_error($ch)."<br>";
				echo $curlerror."<br>";
				logger($obj->idkeywords, $obj->original, 'curl error:'.curl_error($ch), $conn);
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
