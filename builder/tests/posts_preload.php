<?php

function preload($ftp, $ftpuser, $ftppassword){
	$success = 0;
	
	$connect_it = ftp_connect($ftp);

	/* Login to FTP */
	$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
	ftp_chdir($connect_it, "public_html1");
	

	
	if(file_exists(__DIR__ . "/htaccess.deny")){
		echo "File is here"; 
	} else {
		echo "file is missing"; 
	}
	if ( ftp_put( $connect_it, ".htaccess", __DIR__ . "/htaccess.deny", FTP_BINARY ) ) {
								
		$success = 1;
	}
	ftp_close( $connect_it );
		
	return $success;
}
function end_preload($ftp, $ftpuser, $ftppassword){

	$connect_it = ftp_connect($ftp);

	/* Login to FTP */
	$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
					
	ftp_chdir($connect_it, "public_html1");
	if ( ftp_rename( $connect_it, ".htaccess_original", ".htaccess") ) {
		echo "preload removed";				
	}
	ftp_close( $connect_it );

}
preload("23.111.167.194", "itnewstoday", "aIrAAFpOa7ws");

sleep(20);

end_preload("23.111.167.194", "itnewstoday", "aIrAAFpOa7ws");


?>