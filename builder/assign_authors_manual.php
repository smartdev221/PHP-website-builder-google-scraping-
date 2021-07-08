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
	
function authors($ftp, $ftpuser, $ftppassword, $website, $conn){
	
	$select = mysqli_query($conn, "SELECT * FROM `authors` WHERE `website`='0' ORDER BY RAND() LIMIT 10");

		$connect_it = ftp_connect($ftp);

		/* Login to FTP */
		$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
		
		/* Send $local_file to FTP */
		if (!@ftp_chdir($connect_it, "public_html/images")) {
			ftp_mkdir($connect_it, "public_html/images"); //create dir
			$remote = "public_html/images/";
		} else {
			$remote = "";
		}
		$i = 0;
		while($row = mysqli_fetch_object($select)){
			$local_file = __DIR__.'/avatars/'.$row->image;
			if ( ftp_put( $connect_it, $remote.url_slug($row->name).'.'.$row->image_extension, $local_file, FTP_BINARY ) ) {
				
				mysqli_query($conn, "UPDATE `authors` SET `website`='".$website."' WHERE `id`='".$row->id."'");
				//echo "Successfull transfer ".$name.'.'.$extension[0]."\n";
				$i++;
			} 
		}
		
		ftp_close( $connect_it );
	
		
	return $i;
}		

//$assign = authors($ftp[$row->website], $ftpuser[$row->website], $ftppassword[$row->website], $row->website, $conn);
$website = 4;
$assign = authors($ftp[$website], $ftpuser[$website], $ftppassword[$website], $website, $conn);

echo "there were ".$assign." authors added for website ".$website;				

?>