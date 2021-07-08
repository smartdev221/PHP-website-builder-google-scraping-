<?php
include("../config.php");
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
						echo $CURRENT_FAILS."-current fails<br>";
						echo $FAILS_BEFORE_CRON_STOP."-fails before cron<br>";
						if($CURRENT_FAILS < $FAILS_BEFORE_CRON_STOP){
							mysqli_query($conn, "UPDATE `config` SET `value_`=`value_`+1 WHERE `name`='CURRENT_FAILS'");
						} elseif($CURRENT_FAILS >= $FAILS_BEFORE_CRON_STOP){
							if($EMAIL_SENT == 0){
								file_put_contents(__DIR__."/test_source_google_snippet.html", $body);
								$message = date('m-d-Y H:i:s')."\r\nPossible google template change, there is snippet text but none matching.\r\nPlease check.\r\nhttp://winhook.org/script_new/admin/test_source_google_snippet.html";
								mail('catalin_smecheru96@yahoo.com', 'template change ?! snippets missing', $message);
								mysqli_query($conn, "UPDATE `config` SET `value_`='0' WHERE `name`='RUN_CRONJOBS'");
								mysqli_query($conn, "UPDATE `config` SET `value_`='1' WHERE `name`='EMAIL_SENT'");
							}
						}

?>