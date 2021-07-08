<?php
include("../config.php");

$get_templates = mysqli_query($conn, "SELECT * FROM `websites`");
	$templates = array();
	while($website = mysqli_fetch_object($get_templates)){
		for($i = 1; $i < 6; $i++){
			if(!empty($website->{'template'.$i})){
				$templates[$website->id][] = $website->{'template'.$i};
			}
			if(!empty($website->{'title'.$i})){
				$titles[$website->id][] = $website->{'title'.$i};
			}
			$serpt[$website->id] = $website->serptitle;
			$pref[$website->id] = $website->prefix;
			$wsnippets[$website->id] = $website->nosnippets;
			$ftp[$website->id] = $website->ftp;
			$ftpuser[$website->id] = $website->ftpuser;
			$ftppassword[$website->id] = $website->ftppassword;
			$mysql[$website->id] = array($website->db, $website->user, $website->pass);
		}
	}
	
function advert($ftp, $ftpuser, $ftppassword, $website, $conn, $file, $content){
	$success = 0;
	
	$select = mysqli_query($conn, "SELECT * FROM `websites` WHERE `id`='".$website."'");

		$connect_it = ftp_connect($ftp);

		/* Login to FTP */
		$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
		

		while($row = mysqli_fetch_object($select)){
			$local_file = fopen('php://temp', 'r+');
			
			$content = "";
			if($file == 1){
				$advert = "top.php";
				if($row->top_display == 1){
					$content = $row->top;
				}
			}elseif($file == 2){
				$advert = "middle.php";
				if($row->middle_display == 1){
					$content = $row->middle;
				}
			}elseif($file == 3){
				$advert = "bottom.php";
				if($row->bottom_display == 1){
					$content = $row->bottom;
				}
			}
			
			fwrite($local_file, $content);
			rewind($local_file);
			ftp_chdir($connect_it, "public_html");
			if ( ftp_fput( $connect_it, $advert, $local_file, FTP_BINARY ) ) {
								
				//echo "Successfull transfer ".$advert."\n";
				$success = 1;
			}
		}
		fclose($local_file);
		ftp_close( $connect_it );
	
		
	return $success;
}		

//$assign = authors($ftp[$row->website], $ftpuser[$row->website], $ftppassword[$row->website], $row->website, $conn);
$website = 1;
$advert = advert($ftp[$website], $ftpuser[$website], $ftppassword[$website], $website, $conn, 1, "<b>testing</b>");

echo "status:".$advert." edited for website ".$website;				

?>