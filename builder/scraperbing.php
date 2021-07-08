<?php
	include("../config.php");
	//include "hm.php";
	include "class.scrape.bing.php";
	
	/*
	function __autoload($class_name) {
		require_once "class/class.".strtolower($class_name) . '.php';
	}
	*/
	header("Content-type: text/plain; charset=UTF-8");

	function debug($dat){
		if(is_array($dat)||is_object($dat)){
			print_r($dat);
		}else{
			echo $dat."\n";
		}
		flush();
	}
	///thread::cleanthreads(300);
	$sql="DELETE FROM thread WHERE DATE_ADD(hearbeat,INTERVAL 300 SECOND)<NOW()";
		mysqli_query($conn, $sql);
	
	///
		$getconf = mysqli_query($conn, "SELECT name,value_ FROM `config`");
			while($getconfig = mysqli_fetch_object($getconf)){
				if($getconfig->name == "MAX_THREADS"){
					$MAX_THREADS = $getconfig->value_;
				}elseif($getconfig->name == "PAUSED"){
					$PAUSED = $getconfig->value_;
				}elseif($getconfig->name == "MAX_KEYWORDS_X_THREAD"){
					$MAX_KEYWORDS_X_THREAD = $getconfig->value_;
				}elseif($getconfig->name == "SLEEP_THREAD_BETWEEN_KEYWORDS"){
					$SLEEP_THREAD_BETWEEN_KEYWORDS = $getconfig->value_;
				}elseif($getconfig->name == "PROXY"){
					$PROXY = $getconfig->value_;
				}elseif($getconfig->name == "PROXY_IP_PORT"){
					$PROXY_IP_PORT = $getconfig->value_;
				}elseif($getconfig->name == "PROXY_USER_PASSWORD"){
					$PROXY_USER_PASSWORD = $getconfig->value_;
				}
			}
$rand1 = rand(0,5);			
sleep($rand1);
				$rthreads=mysqli_query($conn, "SELECT COUNT(idthread) as thrds FROM thread");
					$rthreads1 = mysqli_fetch_object($rthreads);
	$running_threads= $rthreads1->thrds;


	if($running_threads<$MAX_THREADS){
		///
		$website_check = mysqli_query($conn, "SELECT `id` FROM `websites` WHERE `scraped_bing`<`dripfeed_current` AND `scraped`>=`dripfeed_current` AND `done_today`='0' AND `skip`='0' LIMIT 1");
		///
		if(mysqli_num_rows($website_check) > 0){
			$website_id = mysqli_fetch_object($website_check);
		$k=0;
		//new thread
			$now = date("Y-m-d H:i:s");
		$ins = mysqli_query($conn, "INSERT INTO `thread`(`init`,`hearbeat`,`status`,`randomlive`) VALUES('".$now."','".$now."','init new thread','0')");
			$idthread = mysqli_insert_id($conn);
			//instead of scraped=2
		$rand = rand(2,100);
		
		$sel = mysqli_query($conn, "SELECT idkeywords,website,original,related1,related2,related3,related4,related5,related6,related7,related8,word2,word3,result1,result2,result3,result4,result5,result6,result7,result8,result9,result10,scraped_bing FROM keywords WHERE scraped='1' and `scraped_bing`='0' and `website`='".$website_id->id."' LIMIT ".$MAX_KEYWORDS_X_THREAD." ");
			$rownum = mysqli_num_rows($sel);
			
			while($row = mysqli_fetch_object($sel)){
				if($PAUSED==0){
				///mysqli_query($conn, "UPDATE `keywords` SET `scraped_bing`='".$rand."' WHERE idkeywords='".$row->idkeywords."'");
				}
			}
			sleep(1);
			
		while($k<$rownum){
			

			$sel1 = mysqli_query($conn, "SELECT idkeywords,website,original,related1,related2,related3,related4,related5,related6,related7,related8,word2,word3,result1,result2,result3,result4,result5,result6,result7,result8,result9,result10,scraped_bing FROM keywords WHERE scraped_bing='".$rand."' and `website`='".$website_id->id."' LIMIT 1");
		
			$row1 = mysqli_fetch_array($sel1);
			echo $row1["idkeywords"];
				$kws = "";
				$objeto = "";
				$arreglo="";
				/* $kw = "";
				
				*/
				echo "keyword id: ";
				
				$objeto = (object)[];
				list($objeto->idkeywords,$objeto->website,$objeto->original,$objeto->related1,$objeto->related2,$objeto->related3,$objeto->related4,$objeto->related5,$objeto->related6,$objeto->related7,$objeto->related8,$objeto->word2,$objeto->word3,$objeto->result1,$objeto->result2,$objeto->result3,$objeto->result4,$objeto->result5,$objeto->result6,$objeto->result7,$objeto->result8,$objeto->result9,$objeto->result10,$objeto->scraped_bing)=$row1;
				$arreglo[]=$objeto;
				
				$kws = $arreglo;
			$kw=$kws[0];
			
				//$kw->original = 'types of dns servers in windows';
				$kw->original = '0xc000098 windows 10';
			debug($kw->idkeywords."<<<323");

					$getconf1 = mysqli_query($conn, "SELECT name,value_ FROM config WHERE `name`='PAUSED'");
						$getcfg = mysqli_fetch_object($getconf1);
					
			$PAUSED=$getcfg->value_;
			while($PAUSED==1){
							$now1 = date("Y-m-d H:i:s");
						$updth = mysqli_query($conn, "UPDATE thread SET `status`='paused: ".$kw->original."',`hearbeat`= '".$now1."' WHERE idthread='".$idthread."'");
				sleep(5);		
					$getconf2 = mysqli_query($conn, "SELECT name,value_ FROM config WHERE `name`='PAUSED'");
						$getcfg2 = mysqli_fetch_object($getconf2);
				$PAUSED=$getcfg2->value_;
			}
				$updth1 = mysqli_query($conn, "UPDATE thread SET `status`='".$kw->original."' WHERE idthread='".$idthread."'");
				
			
			$asd = mysqli_query($conn, "UPDATE `keywords` SET `scraped_bing`='1' WHERE idkeywords='".$kw->idkeywords."'") or die(mysqli_error($conn));
			if($asd){
				echo "updatat\n";
			} else {
				echo "failed\n";
			}
			//proxy login details
			$kw->proxy_ip_port = $PROXY_IP_PORT;
			$kw->proxy_user_pass = $PROXY_USER_PASSWORD;
			
			$scraper=new scrape();
			$scraper->scraper($kw,$PROXY, $conn);
			if($SLEEP_THREAD_BETWEEN_KEYWORDS>0){
					$now2 = date("Y-m-d H:i:s");
					$updth2 = mysqli_query($conn, "UPDATE thread SET `status`='sleeping ".$SLEEP_THREAD_BETWEEN_KEYWORDS." secs',`hearbeat`= '".$now2."' WHERE idthread='".$idthread."'");
				sleep($SLEEP_THREAD_BETWEEN_KEYWORDS);
			}
			
			   mysqli_free_result($sel1);
			
			
			sleep(rand(0,3));
			
			$k++;
			
		}		
		
			$thdel = mysqli_query($conn, "DELETE FROM `thread` WHERE `idthread`= '".$idthread."' LIMIT 1");
	}
	}else{
		debug("exiting: MAX THREADS REACHED!!!");
	}
?>
