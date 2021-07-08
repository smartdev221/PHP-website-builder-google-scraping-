<?php
session_start();
include("../config.php");

if(empty($_SESSION['loggedin'])){
	echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
	
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

function user($url, $name){
	  $ch = curl_init();
	  $fields = array( 'pass'=> 'damnpassword123!@#', 'name'=>$name);
	  $postvars = '';
	  foreach($fields as $key=>$value) {
		$postvars .= $key . "=" . $value . "&";
	  }
	  curl_setopt($ch,CURLOPT_URL, $url);
	  curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
	  curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 3);
	  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	  $response = curl_exec($ch);
	  curl_close ($ch);	
	  return $response;
}

function authors($ftp, $ftpuser, $ftppassword, $website, $conn, $wordpress, $wordpress_url){
	//$select = mysqli_query($conn, "SELECT * FROM `authors` WHERE `website`='0' ORDER BY RAND() LIMIT 10");
	$select = mysqli_query($conn, "SELECT DISTINCT(`image`), `id`, `name`, `image_extension` FROM `authors` WHERE `website`='0' ORDER BY RAND() LIMIT 10");
	$connect_it = ftp_connect($ftp);
	/* Login to FTP */
	$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
	if($wordpress == 0){
		/* Send $local_file to FTP */
		if (!@ftp_chdir($connect_it, "public_html/images")) {
			ftp_mkdir($connect_it, "public_html/images"); //create dir
			$remote = "public_html/images/";
		} else {
			$remote = "";
		}
	} else {
		$remote = "public_html/wp-content/";
		ftp_put( $connect_it, $remote.'add_users.php', __DIR__ . '/wp_add_users.php', FTP_BINARY );
		ftp_put( $connect_it, $remote.'add_posts.php', __DIR__ . '/wp_add_posts.php', FTP_BINARY );
	}
	$i = 0;
	while($row = mysqli_fetch_object($select)){
		if($wordpress == 0){
			$local_file = __DIR__.'/avatars/'.$row->image;
			if ( ftp_put( $connect_it, $remote.url_slug($row->name).'.'.$row->image_extension, $local_file, FTP_BINARY ) ) {
				
				mysqli_query($conn, "UPDATE `authors` SET `website`='".$website."' WHERE `id`='".$row->id."'");
				//echo "Successfull transfer ".$name.'.'.$extension[0]."\n";
				$i++;
			}
		} else {
			$user_id = user($wordpress_url.'/wp-content/add_users.php', $row->name); 
			mysqli_query($conn, "UPDATE `authors` SET `wordpress_user`='".mysqli_real_escape_string($conn, $user_id)."', `website`='".$website."' WHERE `id`='".$row->id."'");
			$i++;
		}
	}
	ftp_close( $connect_it );
	return $i;
}

function advert($ftp, $ftpuser, $ftppassword, $website, $conn, $file, $languages){
	$success = 0;
	$select = mysqli_query($conn, "SELECT * FROM `websites` WHERE `id`='".$website."'");
	$connect_it = ftp_connect($ftp);
	/* Login to FTP */
	$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
	while($row = mysqli_fetch_object($select)){
		$local_file = fopen('php://temp', 'r+');
		$content = "";
		if($file == 1){
			if(!empty($row->wordpress_url)){
				$advert = "topfix.php";
			} else {
			$advert = "top.php";
			}
			if($row->top_display == 1){
				$content = $row->top;
				$display = 1;
				$translated = 0;
			} else {					
				$display = 0;
				$translated = 1;
			}
			//for website translation
			mysqli_query($conn, "DELETE FROM `ads_translation` WHERE `website`='".$website."' and `location`='".$file."'");
			foreach($languages as $language){
				mysqli_query($conn, "INSERT INTO `ads_translation`(`website`, `language`, `location`, `display`, `translated`) VALUES('".$website."', '".$language."', '".$file."', '".$display."', '".$translated."')");
			}
		}elseif($file == 2){
			if(!empty($row->wordpress_url)){
				$advert = "middlefix.php";
			} else {
			$advert = "middle.php";
			}
			if($row->middle_display == 1){
				$content = $row->middle;
				$display = 1;
				$translated = 0;
			} else {
				$display = 0;
				$translated = 1;
			}
			//for website translation
			mysqli_query($conn, "DELETE FROM `ads_translation` WHERE `website`='".$website."' and `location`='".$file."'");
			foreach($languages as $language){
				mysqli_query($conn, "INSERT INTO `ads_translation`(`website`, `language`, `location`, `display`, `translated`) VALUES('".$website."', '".$language."', '".$file."', '".$display."', '".$translated."')");
			}
		}elseif($file == 3){
			if(!empty($row->wordpress_url)){
				$advert = "bottomfix.php";
			} else {
			$advert = "bottom.php";
			}
			if($row->bottom_display == 1){
				$content = $row->bottom;
				$display = 1;
				$translated = 0;
			} else {
				$display = 0;
				$translated = 1;
			}
			//for website translation
			mysqli_query($conn, "DELETE FROM `ads_translation` WHERE `website`='".$website."' and `location`='".$file."'");
			foreach($languages as $language){
				mysqli_query($conn, "INSERT INTO `ads_translation`(`website`, `language`, `location`, `display`, `translated`) VALUES('".$website."', '".$language."', '".$file."', '".$display."', '".$translated."')");
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

function preload($ftp, $ftpuser, $ftppassword){
	$success = 0;
	$connect_it = ftp_connect($ftp);
	/* Login to FTP */
	$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
	ftp_chdir($connect_it, "public_html");
	if ( ftp_fput( $connect_it, ".htaccess", __DIR__."/htaccess.deny", FTP_BINARY ) ) {
		$success = 1;
	}
	ftp_close( $connect_it );
	return $success;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Charts -->
    <link type="text/css" href="assets/css/custom-theme/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,600,700' rel='stylesheet'
        type='text/css'>
</head>

<body>
    <style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="#"><img src="assets/img/logo.png" alt="" /></a>
                <div class="nav-collapse">
                    <ul class="nav pull-right">
                        <li class="dropdown">
                            <a href="pages.htm" class="dropdown-toggle" data-toggle="dropdown">
                                <span style="padding-right:10px; width:30px;"><img src="assets/img/user_thumb.jpg"
                                        style="width:30px;" alt="" /></span>Mr <?php echo $_SESSION['user']; ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="my.php"><i style="font-size:14px; padding-top:3px; padding-right:5px;"
                                            class="icon-user"></i>My Account</a>
                                </li>
                                <li>
                                    <a href="settings.php"><i
                                            style="font-size:14px; padding-top:3px; padding-right:5px;"
                                            class="icon-cogs"></i>Settings</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <div class="btn-group" style="margin-top:7px;"><a href="logout.php"
                                    class="medium twitter button radius" style="text-decoration:none;"><i
                                        style="font-size:16px; padding-top:3px; padding-right:5px;"
                                        class="icon-off"></i>Leap out</a> </div>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </div>
    <div class="subnav subnav-fixed">
        <ul class="nav nav-pills">
            <li>
                <a href="index.php">
                    <i style="margin-top:7px;" class="icon-dashboard icon-large"></i>Dashboard</a>
            </li>
            <li>
                <a href="keywords.php"><i style="margin-top:7px;" class="icon-th icon-large"></i>Keywords</a>
            </li>
            <li class="active">
                <a href="websites.php"><i style="margin-top:7px;" class=" icon-file icon-large"></i>Websites</a>
            </li>
            <li>
                <a href="settings.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Settings</a>
            </li>
            <li>
                <form style="padding:5px;" class="navbar-search pull-left" action="keywords.php">
                    <input type="text" name="search" class="search-query span3" placeholder="Search a topic">
                </form>
            </li>
        </ul>
    </div>
    <div class="container" style="margin-top:30px;">
        <div class="row">
            <?php
				if(empty($_GET['edit'])){
			?>
            <div class="span6">
                <div class="widget_heading">
                    <h4>Add Website</h4>
                </div>
                <div class="widget_container">
                    <div class="control-group">
                        <?php
							if(!empty($_POST['name'])){

								if($_POST['dripfeed'] == "0"){
									$current = $_POST['perday'];
									$order = '999';
								} else {
									$current = 1; 
									$order = 1;
								}

								if($_POST['step_1'] == "3"){
									$step_1 = "3|".$_POST['lang1'].";".$_POST['lang2'].";".$_POST['lang3'];
								} else {
									$step_1 = $_POST['step_1']."|".$_POST['step_1_option'];
								}

								if($_POST['step_2'] == "3"){
									$step_2 = "3|".$_POST['lang1'].";".$_POST['lang2'].";".$_POST['lang3'];
								} else {
									$step_2 = $_POST['step_2']."|".$_POST['step_2_option'];
								}

								if($_POST['step_3'] == "3"){
									$step_3 = "3|".$_POST['lang1'].";".$_POST['lang2'].";".$_POST['lang3'];
								} else {
									$step_3 = $_POST['step_3']."|".$_POST['step_3_option'];
								}

								$insert = mysqli_query($conn, "INSERT INTO `websites` (`name`,`ftp`,`ftpuser`,`ftppassword`,`wordpress_url`,`wordpress_categories`, `wordpress_post_tags`,`db`,`user`,`pass`,`serp_not_matched`,`words`,`perday`,`spin`,`nosnippets`,`dripfeed`,`dripfeed_current`,`dripfeed_order`,`serptitle`,`prefix`,`rewrite_step_1`,`rewrite_step_2`,`rewrite_step_3`,`title1`,`title2`,`title3`,`title4`,`title5`,`template1`,`template2`,`template3`,`template4`,`template5`,`find_and_replace`,`paa_shadow`,`preload`,`languages`,`languages_spin`,`exclude_paa_title`) VALUES('".mysqli_real_escape_string($conn, $_POST['name'])."', '".mysqli_real_escape_string($conn, trim($_POST['ftp']))."', '".mysqli_real_escape_string($conn, trim($_POST['ftpuser']))."', '".mysqli_real_escape_string($conn, trim($_POST['ftppassword']))."', '".mysqli_real_escape_string($conn, trim($_POST['wordpress_url']))."', '".mysqli_real_escape_string($conn, trim($_POST['wordpress_categories']))."', '".mysqli_real_escape_string($conn, trim($_POST['post_tags']))."', '".mysqli_real_escape_string($conn, $_POST['db'])."', '".mysqli_real_escape_string($conn, $_POST['user'])."', '".mysqli_real_escape_string($conn, $_POST['pass'])."', '".mysqli_real_escape_string($conn, $_POST['not_matching'])."', '".mysqli_real_escape_string($conn, $_POST['words'])."', '".mysqli_real_escape_string($conn, $_POST['perday'])."', '".mysqli_real_escape_string($conn, $_POST['spin'])."', '".mysqli_real_escape_string($conn, $_POST['nosnippets'])."', '".mysqli_real_escape_string($conn, $_POST['dripfeed'])."', '".$current."', '".$order."','".mysqli_real_escape_string($conn, $_POST['serptitle'])."', '".mysqli_real_escape_string($conn, $_POST['prefix'])."', '".mysqli_real_escape_string($conn, $step_1)."', '".mysqli_real_escape_string($conn, $step_2)."', '".mysqli_real_escape_string($conn, $step_3)."', '".mysqli_real_escape_string($conn, $_POST['title1'])."', '".mysqli_real_escape_string($conn, $_POST['title2'])."', '".mysqli_real_escape_string($conn, $_POST['title3'])."', '".mysqli_real_escape_string($conn, $_POST['title4'])."', '".mysqli_real_escape_string($conn, $_POST['title5'])."', '".mysqli_real_escape_string($conn, $_POST['template1'])."', '".mysqli_real_escape_string($conn, $_POST['template2'])."', '".mysqli_real_escape_string($conn, $_POST['template3'])."', '".mysqli_real_escape_string($conn, $_POST['template4'])."', '".mysqli_real_escape_string($conn, $_POST['template5'])."', '".mysqli_real_escape_string($conn, $_POST['find_and_replace'])."', '".rand(1,4)."', '".mysqli_real_escape_string($conn, $_POST['preload'])."', '".mysqli_real_escape_string($conn, $_POST['langs'])."', '".mysqli_real_escape_string($conn, $_POST['languages_spin'])."', '".mysqli_real_escape_string($conn, $_POST['exclude_paa_title'])."')") or die(mysqli_error($conn));
								if($insert){

									$insertid = mysqli_insert_id($conn);
									echo "<span style=\"color: green;font-size:15px;\">This website has been successfully added.</span><br><br>";
									
									//csv upload
									if($_FILES['file']['name']!=""){
										echo "<span style=\"color: green;font-size:15px;\">Please do not close this page as the keywords are being uploaded.</span><br><br>";
											$body=fopen($_FILES['file']['tmp_name'],"r");
											do {
												if ($data[0]) {
													$line=trim($data[0]);
													if($line!=""){
														mysqli_query($conn, "INSERT INTO `keywords`(`original`, `website`) VALUES('".mysqli_real_escape_string($conn, $line)."', '".$insertid."')");
													}
												}
											} while ($data = fgetcsv($body,1000,",","\""));
										echo "<span style=\"color: green;font-size:15px;\">Keywords uploaded!</span><br><br>";
										$assign = authors(trim($_POST['ftp']), trim($_POST['ftpuser']), trim($_POST['ftppassword']), $insertid, $conn, trim($_POST['wordpress']), trim($_POST['wordpress_url']));
										echo "<span style=\"color: green;font-size:15px;\">There were ".$assign." authors assigned to this website!</span><br><br>";
										
										if($_POST['preload'] != 0){
											$preload = preload(trim($_POST['ftp']), trim($_POST['ftpuser']), trim($_POST['ftppassword']));
											echo "<span style=\"color: green;font-size:15px;\">The website is in 'Preload mode'!</span><br><br>";
										}
									}
									//
								} else {
									echo "<span style=\"color: red;font-size:15px;\">Error while entering on database.Please try again...</span><br><br>";
								}
							}
						?>

                        <form action="#" method="post" enctype="multipart/form-data">
                            <div class="loader" id="loader" style="display:none; "></div>

                            <label class="control-label" for="input01">Name (allowed a-Z, 0-9, -, _) - asd_com; asdCOM;
                                asd; asd-com etc</label>
                            <div class="controls">
                                <input type="text" name="name" class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">FTP IP(needed for post image upload)</label>
                            <div class="controls">
                                <input type="text" name="ftp" class="input-xlarge span5"
                                    value="<?php echo $_SERVER['SERVER_ADDR']; ?>">
                            </div>

                            <label class="control-label" for="input01">FTP user(needed for post image upload)</label>
                            <div class="controls">
                                <input type="text" name="ftpuser" class="input-xlarge span5">
                                <!-- <input type="hidden" name="ftp" value="<?php echo $_SERVER['SERVER_ADDR']; ?>"> -->
                            </div>

                            <label class="control-label" for="input01">FTP password(needed for post image
                                upload)</label>
                            <div class="controls">
                                <input type="text" name="ftppassword" class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Wordpress?</label>
                            <div class="controls">
                                <select name="wordpress">
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>

                            <button type="button" id="button1">Check ftp!</button>

                            <div id="result1" style="display:none; ">
                                <label class="control-label" for="input01">Database table</label>
                                <div class="controls">
                                    <input type="text" name="db" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Database user</label>
                                <div class="controls">
                                    <input type="text" name="user" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Database password</label>
                                <div class="controls">
                                    <input type="text" name="pass" class="input-xlarge span5">
                                </div>
                                <button type="button" id="button">Check connection!</button>
                            </div>

                            <div id="result" style="display:none; ">
                                <div id="wordpress_url" style="display:none; ">
                                    <label class="control-label" for="input01">Wordpress url (e.g. http://domain.com/) -
                                        needed for post and user add</label>
                                    <div class="controls">
                                        <input type="text" name="wordpress_url" class="input-xlarge span5">
                                    </div>

                                    <label class="control-label" for="input01"><img
                                            src="https://icons.iconarchive.com/icons/sicons/basic-round-social/512/wordpress-icon.png"
                                            width="32"> Post category(comma delimited)</label>
                                    <div class="controls">
                                        <input type="text" name="wordpress_categories" class="input-xlarge span5">
                                    </div>

                                    <label class="control-label" for="input01"><img
                                            src="https://icons.iconarchive.com/icons/sicons/basic-round-social/512/wordpress-icon.png"
                                            width="32"> Image chiclets as post_tags</label>
                                    <div class="controls">
                                        <select name="post_tags">
                                            <option value="1" selected>Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="loader" id="loader1" style="display:none; "></div>

                                <label class="control-label" for="input01"><b>Copy template & other info from
                                        website</b></label>
                                <div class="controls">
                                    <select name="copy">
                                        <?php 
											$select_websites = mysqli_query($conn, "SELECT `id`,`name` FROM `websites`");
											while ($row_website = mysqli_fetch_object($select_websites)){
												echo '<option value="'.$row_website->id.'">'.$row_website->name.'</option>';
											} 
										?>
                                    </select>
                                    <button type="button" id="button2">Copy!</button>
                                    <br>
                                </div>
                                <label class="control-label" for="input01">Posts preload</label>
                                <div class="controls">
                                    <select name="preload">
                                        <option value="0" selected>No preload</option>
                                        <option value="100">100 posts</option>
                                        <option value="200">200 posts</option>
                                    </select>
                                </div>
                                <label class="control-label" for="input01">Serp content not matching intro, cause &
                                    fix</label>
                                <div class="controls">
                                    <select name="not_matching">
                                        <option value="1" selected>Skip keyword</option>
                                        <option value="2">Use OLD method</option>
                                        <option value="3">Use Random Subheadings method</option>
                                    </select>
                                </div>
                                <hr>
                                <h4>Rewrite process</h4>
                                <label class="control-label" for="input01">First step</label>
                                <div class="controls">
                                    <select name="step_1" id="step_1">
                                        <option value="0" selected>None</option>
                                        <option value="1">Wordtune</option>
                                        <option value="2">Spin</option>
                                        <option value="3">GTR Google Multi-translation</option>
                                    </select>
                                    <div name="step_1_options">
                                    </div>
                                </div>
                                <label class="control-label" for="input01">Second step</label>
                                <div class="controls">
                                    <select name="step_2" id="step_2">
                                        <option value="0" selected>None</option>
                                        <option value="1">Wordtune</option>
                                        <option value="2">Spin</option>
                                        <option value="3">GTR Google Multi-translation</option>
                                    </select>
                                    <div name="step_2_options">
                                    </div>
                                </div>
                                <label class="control-label" for="input01">Third step</label>
                                <div class="controls">
                                    <select name="step_3" id="step_3">
                                        <option value="0" selected>None</option>
                                        <option value="1">Wordtune</option>
                                        <option value="2">Spin</option>
                                        <option value="3">GTR Google Multi-translation</option>
                                    </select>
                                    <div name="step_3_options">
                                    </div>
                                </div>
                                <hr>

                                <label class="control-label" for="input01">Serp minimum word count</label>
                                <div class="controls">
                                    <input type="text" name="words" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Posts per day</label>
                                <div class="controls">
                                    <input type="text" name="perday" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Spin scraped serp content before
                                    translation</label>
                                <div class="controls">
                                    <select name="spin">
                                        <option value="0" selected>No spinning</option>
                                        <option value="1">Every word</option>
                                        <option value="2">Every 2 words</option>
                                        <option value="3">Every 3 words</option>
                                        <option value="4">Every 4 words</option>
                                        <option value="5">Every 5 words</option>
                                        <option value="6">Every 6 words</option>
                                        <option value="7">Every 7 words</option>
                                        <option value="8">Every 8 words</option>
                                        <option value="9">Every 9 words</option>
                                        <option value="10">Every 10 words</option>
                                    </select>
                                </div>

                                <label class="control-label" for="input01">Exclude question title from PAA</label>
                                <div class="controls">
                                    <select name="exclude_paa_title">
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <label class="control-label" for="input01">Build keywords having 0 snippets</label>
                                <div class="controls">
                                    <select name="nosnippets">
                                        <option value="0">No</option>
                                        <option value="1" selected>Yes</option>
                                    </select>
                                </div>

                                <label class="control-label" for="input01">Dripfeed posts</label>
                                <div class="controls">
                                    <select name="dripfeed">
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <label class="control-label" for="input01">Use Serp Scraped title (below inputs for
                                    title are ignored if Yes)</label>
                                <div class="controls">
                                    <select name="serptitle">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <label class="control-label" for="input01">Add Prefixes to the below titles</label>
                                <div class="controls">
                                    <select name="prefix">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <label class="control-label" for="input01"><b>Keywords CSV (it can't be uploaded later)
                                        **********</b></label>
                                <div class="controls">
                                    <input name="file" type="file" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Title 1</label>
                                <div class="controls">
                                    <input type="text" name="title1" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Title 2</label>
                                <div class="controls">
                                    <input type="text" name="title2" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Title 3</label>
                                <div class="controls">
                                    <input type="text" name="title3" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Title 4</label>
                                <div class="controls">
                                    <input type="text" name="title4" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Title 5</label>
                                <div class="controls">
                                    <input type="text" name="title5" class="input-xlarge span5">
                                </div>

                                <label class="control-label" for="input01">Template 1</label>
                                <div class="controls">
                                    <textarea name="template1" class="input-xlarge span5" rows="10"
                                        cols="100"></textarea>
                                </div>

                                <label class="control-label" for="input01">Template 2</label>
                                <div class="controls">
                                    <textarea name="template2" class="input-xlarge span5" rows="10"
                                        cols="100"></textarea>
                                </div>

                                <label class="control-label" for="input01">Template 3</label>
                                <div class="controls">
                                    <textarea name="template3" class="input-xlarge span5" rows="10"
                                        cols="100"></textarea>
                                </div>

                                <label class="control-label" for="input01">Template 4</label>
                                <div class="controls">
                                    <textarea name="template4" class="input-xlarge span5" rows="10"
                                        cols="100"></textarea>
                                </div>

                                <label class="control-label" for="input01">Template 5</label>
                                <div class="controls">
                                    <textarea name="template5" class="input-xlarge span5" rows="10"
                                        cols="100"></textarea>
                                </div>

                                <label class="control-label" for="input01">Find and replace. Use as
                                    sitename|siteurl</label>
                                <div class="controls">
                                    <input type="text" name="find_and_replace" value="" class="input-xlarge span5">
                                </div>

                                <hr>
                                <h4>Website translation</h4>

                                <label class="control-label" for="input01">Languages (<a href="langs.txt"
                                        target="_blank">see here</a>) - PLEASE LEAVE EMPTY if you do not wish to
                                    translate to different languages</label>
                                <div class="controls">
                                    <textarea name="langs" class="input-xlarge span5" rows="10" cols="100">de
										it
										nl
										fr
										ko
										pt
										sv
										ru
										pl
										es
									</textarea>
                                </div>

                                <label class="control-label" for="input01">Spin content before translation</label>
                                <div class="controls">
                                    <select name="languages_spin">
                                        <option value="0" selected>No spinning</option>
                                        <option value="1">Every word</option>
                                        <option value="2">Every 2 words</option>
                                        <option value="3">Every 3 words</option>
                                        <option value="4">Every 4 words</option>
                                        <option value="5">Every 5 words</option>
                                        <option value="6">Every 6 words</option>
                                        <option value="7">Every 7 words</option>
                                        <option value="8">Every 8 words</option>
                                        <option value="9">Every 9 words</option>
                                        <option value="10">Every 10 words</option>
                                    </select>
                                </div>

                                <div class="insert-actions">
                                    <div class="btn-toolbar">
                                        <p class="pull-right">
                                            <button class="btn btn-medium btn-primary">Submit</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
				} else {
			?>
            <div class="span6">
                <div class="widget_heading">
                    <h4>Edit Website</h4>
                </div>
                <div class="widget_container">
                    <div class="control-group">
                        <?php
							if(!empty($_POST['words'])){
								//rewrite
								if($_POST['step_1'] == "3"){
									$step_1 = "3|".$_POST['lang1'].";".$_POST['lang2'].";".$_POST['lang3'];
								} else {
									$step_1 = $_POST['step_1']."|".$_POST['step_1_option'];
								}
								if($_POST['step_2'] == "3"){
									$step_2 = "3|".$_POST['lang1'].";".$_POST['lang2'].";".$_POST['lang3'];
								} else {
									$step_2 = $_POST['step_2']."|".$_POST['step_2_option'];
								}
								if($_POST['step_3'] == "3"){
									$step_3 = "3|".$_POST['lang1'].";".$_POST['lang2'].";".$_POST['lang3'];
								} else {
									$step_3 = $_POST['step_3']."|".$_POST['step_3_option'];
								}
								//rewrite
							//$update = mysql_query("UPDATE `websites` SET `ftpuser`='".mysqli_real_escape_string($conn, trim($_POST['ftpuser']))."',`ftppassword`='".mysqli_real_escape_string($conn, trim($_POST['ftppassword']))."', `db`='".mysqli_real_escape_string($conn, $_POST['db'])."', `user`='".mysqli_real_escape_string($conn, $_POST['user'])."', `pass`='".mysqli_real_escape_string($conn, $_POST['pass'])."', `words`='".mysqli_real_escape_string($conn, $_POST['words'])."', `perday`='".mysqli_real_escape_string($conn, $_POST['perday'])."', `title1`='".mysqli_real_escape_string($conn, $_POST['title1'])."', `title2`='".mysqli_real_escape_string($conn, $_POST['title2'])."', `title3`='".mysqli_real_escape_string($conn, $_POST['title3'])."', `title4`='".mysqli_real_escape_string($conn, $_POST['title4'])."', `title5`='".mysqli_real_escape_string($conn, $_POST['title5'])."', `template1`='".mysqli_real_escape_string($conn, $_POST['template1'])."', `template2`='".mysqli_real_escape_string($conn, $_POST['template2'])."', `template3`='".mysqli_real_escape_string($conn, $_POST['template3'])."', `template4`='".mysqli_real_escape_string($conn, $_POST['template4'])."', `template5`='".mysqli_real_escape_string($conn, $_POST['template5'])."'  WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
								$update = mysql_query("UPDATE `websites` SET `serp_not_matched`='".mysqli_real_escape_string($conn, $_POST['not_matching'])."',`words`='".mysqli_real_escape_string($conn, $_POST['words'])."', `perday`='".mysqli_real_escape_string($conn, $_POST['perday'])."', `wordpress_categories`='".mysqli_real_escape_string($conn, $_POST['wordpress_categories'])."', `wordpress_post_tags`='".mysqli_real_escape_string($conn, $_POST['post_tags'])."', `spin`='".mysqli_real_escape_string($conn, $_POST['spin'])."', `nosnippets`='".mysqli_real_escape_string($conn, $_POST['nosnippets'])."', `serptitle`='".mysqli_real_escape_string($conn, $_POST['serptitle'])."', `prefix`='".mysqli_real_escape_string($conn, $_POST['prefix'])."', `rewrite_step_1`='".mysqli_real_escape_string($conn, $step_1)."', `rewrite_step_2`='".mysqli_real_escape_string($conn, $step_2)."', `rewrite_step_3`='".mysqli_real_escape_string($conn, $step_3)."', `title1`='".mysqli_real_escape_string($conn, $_POST['title1'])."', `title2`='".mysqli_real_escape_string($conn, $_POST['title2'])."', `title3`='".mysqli_real_escape_string($conn, $_POST['title3'])."', `title4`='".mysqli_real_escape_string($conn, $_POST['title4'])."', `title5`='".mysqli_real_escape_string($conn, $_POST['title5'])."', `template1`='".mysqli_real_escape_string($conn, $_POST['template1'])."', `template2`='".mysqli_real_escape_string($conn, $_POST['template2'])."', `template3`='".mysqli_real_escape_string($conn, $_POST['template3'])."', `template4`='".mysqli_real_escape_string($conn, $_POST['template4'])."', `template5`='".mysqli_real_escape_string($conn, $_POST['template5'])."', `find_and_replace`='".mysqli_real_escape_string($conn, $_POST['find_and_replace'])."', `exclude_paa_title`='".mysqli_real_escape_string($conn, $_POST['exclude_paa_title'])."'  WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
								if($update){
									echo "<span style=\"color: green;font-size:15px;\">This website has been successfully updated.</span><br><br>";
								} else {
									echo "<span style=\"color: green;font-size:15px;\">Error while entering on database.Please try again...</span><br><br>";
								}
							}
							$select = mysqli_query($conn, "SELECT * FROM `websites` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
							if(mysqli_num_rows($select) > 0){
							$row1 = mysqli_fetch_object($select);
						?>
                        <form action="#" method="post">
                            <label class="control-label" for="input01">Name (allowed a-Z, 0-9, -, _) - asd_com; asdCOM;
                                asd; asd-com etc</label>
                            <div class="controls">
                                <input type="text" name="name" value="<?php echo $row1->name; ?>" disabled
                                    class="input-xlarge span5">
                            </div>
                            <!--  <label class="control-label" for="input01">FTP user(needed for post image upload)</label>
							<div class="controls">
								<input type="text" name="ftpuser" value="<?php echo $row1->ftpuser; ?>" class="input-xlarge span5" >

							</div>
							<label class="control-label" for="input01">FTP password(needed for post image upload)</label>
							<div class="controls">
								<input type="text" name="ftppassword" value="<?php echo $row1->ftppassword; ?>" class="input-xlarge span5" >

							</div>
							<label class="control-label" for="input01">Database table</label>
							<div class="controls">
								<input type="text" name="db" value="<?php echo $row1->db; ?>" class="input-xlarge span5" >

							</div>
							<label class="control-label" for="input01">Database user</label>
							<div class="controls">
								<input type="text" name="user" value="<?php echo $row1->user; ?>" class="input-xlarge span5" >

							</div>
							<label class="control-label" for="input01">Database password</label>
							<div class="controls">
								<input type="text" name="pass" value="<?php echo $row1->pass; ?>" class="input-xlarge span5" >

							</div>-->
                            <label class="control-label" for="input01">Serp content not matching intro, cause &
                                fix</label>
                            <div class="controls">
                                <select name="not_matching">
                                    <option value="1" <?php if($row1->serp_not_matched == 1){ echo "selected"; } ?>>Skip
                                        keyword</option>
                                    <option value="2" <?php if($row1->serp_not_matched == 2){ echo "selected"; } ?>>Use
                                        OLD method</option>
                                    <option value="3" <?php if($row1->serp_not_matched == 3){ echo "selected"; } ?>>Use
                                        Random Subheadings method</option>
                                </select>
                            </div>
                            <hr>
                            <h4>Rewrite process</h4>
                            <label class="control-label" for="input01">First step</label>
                            <div class="controls">
                                <?php 
									$step_1_setting = explode('|', $row1->rewrite_step_1);
									$step_1 = $step_1_setting[0];
									$step_1_options = $step_1_setting[1];
									$step_1_langs = explode(';', $step_1_options);
								?>
                                <select name="step_1" id="step_1">
                                    <option value="0" <?php if($step_1 == 0){ echo "selected"; } ?>>None</option>
                                    <option value="1" <?php if($step_1 == 1){ echo "selected"; } ?>>Wordtune</option>
                                    <option value="2" <?php if($step_1 == 2){ echo "selected"; } ?>>Spin</option>
                                    <option value="3" <?php if($step_1 == 3){ echo "selected"; } ?>>GTR Google
                                        Multi-translation</option>
                                </select>
                                <script>
                                setTimeout(function() {
                                    rewrite_edit('step_1', '<?php echo $step_1_options; ?>',
                                        '<?php echo $step_1_langs[0]; ?>',
                                        '<?php echo $step_1_langs[1]; ?>', '<?php echo $step_1_langs[2]; ?>'
                                    );
                                }, 1000);
                                </script>
                                <div name="step_1_options">
                                </div>
                            </div>
                            <label class="control-label" for="input01">Second step</label>
                            <div class="controls">
                                <?php 
									$step_2_setting = explode('|', $row1->rewrite_step_2);
									$step_2 = $step_2_setting[0];
									$step_2_options = $step_2_setting[1];
									$step_2_langs = explode(';', $step_2_options);
								?>
                                <select name="step_2" id="step_2">
                                    <option value="0" <?php if($step_2 == 0){ echo "selected"; } ?>>None</option>
                                    <option value="1" <?php if($step_2 == 1){ echo "selected"; } ?>>Wordtune</option>
                                    <option value="2" <?php if($step_2 == 2){ echo "selected"; } ?>>Spin</option>
                                    <option value="3" <?php if($step_2 == 3){ echo "selected"; } ?>>GTR Google
                                        Multi-translation</option>
                                </select>
                                <script>
                                setTimeout(function() {
                                    rewrite_edit('step_2', '<?php echo $step_2_options; ?>',
                                        '<?php echo $step_2_langs[0]; ?>',
                                        '<?php echo $step_2_langs[1]; ?>', '<?php echo $step_2_langs[2]; ?>'
                                    );
                                }, 1000);
                                </script>
                                <div name="step_2_options">
                                </div>
                            </div>
                            <label class="control-label" for="input01">Third step</label>
                            <div class="controls">
                                <?php 
									$step_3_setting = explode('|', $row1->rewrite_step_3);
									$step_3 = $step_3_setting[0];
									$step_3_options = $step_3_setting[1];
									$step_3_langs = explode(';', $step_3_options);
								?>
                                <select name="step_3" id="step_3">
                                    <option value="0" <?php if($step_3 == 0){ echo "selected"; } ?>>None</option>
                                    <option value="1" <?php if($step_3 == 1){ echo "selected"; } ?>>Wordtune</option>
                                    <option value="2" <?php if($step_3 == 2){ echo "selected"; } ?>>Spin</option>
                                    <option value="3" <?php if($step_3 == 3){ echo "selected"; } ?>>GTR Google
                                        Multi-translation</option>
                                </select>
                                <script>
                                setTimeout(function() {
                                    rewrite_edit('step_3', '<?php echo $step_3_options; ?>',
                                        '<?php echo $step_3_langs[0]; ?>',
                                        '<?php echo $step_3_langs[1]; ?>', '<?php echo $step_3_langs[2]; ?>'
                                    );
                                }, 1000);
                                </script>
                                <div name="step_3_options">
                                </div>
                            </div>
                            <hr>

                            <label class="control-label" for="input01">Serp minimum word count</label>
                            <div class="controls">
                                <input type="text" name="words" value="<?php echo $row1->words; ?>"
                                    class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Posts per day</label>
                            <div class="controls">
                                <input type="text" name="perday" value="<?php echo $row1->perday; ?>"
                                    class="input-xlarge span5">
                            </div>
                            <?php if(!empty($row1->wordpress_url)){?>

                            <label class="control-label" for="input01"><img
                                    src="https://icons.iconarchive.com/icons/sicons/basic-round-social/512/wordpress-icon.png"
                                    width="32"> Post category(comma delimited)</label>
                            <div class="controls">
                                <input type="text" name="wordpress_categories"
                                    value="<?php echo $row1->wordpress_categories; ?>" class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01"><img
                                    src="https://icons.iconarchive.com/icons/sicons/basic-round-social/512/wordpress-icon.png"
                                    width="32"> Image chiclets as post_tags</label>
                            <div class="controls">
                                <select name="post_tags">
                                    <option value="1" <?php if($row1->wordpress_post_tags == 1){ echo "selected"; } ?>>
                                        Yes</option>
                                    <option value="0" <?php if($row1->wordpress_post_tags == 0){ echo "selected"; } ?>>
                                        No</option>
                                </select>
                            </div>
                            <?php } ?>
                            <label class="control-label" for="input01">Spin scraped serp content before
                                translation</label>
                            <div class="controls">
                                <select name="spin">
                                    <option value="0" <?php if($row1->spin == 0){ echo "selected"; } ?>>No spinning
                                    </option>
                                    <option value="1" <?php if($row1->spin == 1){ echo "selected"; } ?>>Every word
                                    </option>
                                    <option value="2" <?php if($row1->spin == 2){ echo "selected"; } ?>>Every 2 words
                                    </option>
                                    <option value="3" <?php if($row1->spin == 3){ echo "selected"; } ?>>Every 3 words
                                    </option>
                                    <option value="4" <?php if($row1->spin == 4){ echo "selected"; } ?>>Every 4 words
                                    </option>
                                    <option value="5" <?php if($row1->spin == 5){ echo "selected"; } ?>>Every 5 words
                                    </option>
                                    <option value="6" <?php if($row1->spin == 6){ echo "selected"; } ?>>Every 6 words
                                    </option>
                                    <option value="7" <?php if($row1->spin == 7){ echo "selected"; } ?>>Every 7 words
                                    </option>
                                    <option value="8" <?php if($row1->spin == 8){ echo "selected"; } ?>>Every 8 words
                                    </option>
                                    <option value="9" <?php if($row1->spin == 9){ echo "selected"; } ?>>Every 9 words
                                    </option>
                                    <option value="10" <?php if($row1->spin == 10){ echo "selected"; } ?>>Every 10 words
                                    </option>
                                </select>
                            </div>

                            <label class="control-label" for="input01">Exclude question title from PAA</label>
                            <div class="controls">
                                <select name="exclude_paa_title">
                                    <option value="0" <?php if($row1->exclude_paa_title == 0){ echo "selected"; } ?>>No
                                    </option>
                                    <option value="1" <?php if($row1->exclude_paa_title == 1){ echo "selected"; } ?>>Yes
                                    </option>
                                </select>
                            </div>

                            <label class="control-label" for="input01">Build keywords having 0 snippets</label>
                            <div class="controls">
                                <select name="nosnippets">
                                    <option value="0" <?php if($row1->nosnippets == 0){ echo "selected"; } ?>>No
                                    </option>
                                    <option value="1" <?php if($row1->nosnippets == 1){ echo "selected"; } ?>>Yes
                                    </option>
                                </select>
                            </div>

                            <label class="control-label" for="input01">Use Serp Scraped title (below inputs for title
                                are ignored if Yes)</label>
                            <div class="controls">
                                <select name="serptitle">
                                    <option value="0" <?php if($row1->serptitle == 0){ echo "selected"; } ?>>No</option>
                                    <option value="1" <?php if($row1->serptitle == 1){ echo "selected"; } ?>>Yes
                                    </option>
                                </select>
                            </div>

                            <label class="control-label" for="input01">Add Prefixes to the below titles</label>
                            <div class="controls">
                                <select name="prefix">
                                    <option value="0" <?php if($row1->prefix == 0){ echo "selected"; } ?>>No</option>
                                    <option value="1" <?php if($row1->prefix == 1){ echo "selected"; } ?>>Yes</option>
                                </select>
                            </div>

                            <label class="control-label" for="input01">Title 1</label>
                            <div class="controls">
                                <input type="text" name="title1" value="<?php echo $row1->title1; ?>"
                                    class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Title 2</label>
                            <div class="controls">
                                <input type="text" name="title2" value="<?php echo $row1->title2; ?>"
                                    class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Title 3</label>
                            <div class="controls">
                                <input type="text" name="title3" value="<?php echo $row1->title3; ?>"
                                    class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Title 4</label>
                            <div class="controls">
                                <input type="text" name="title4" value="<?php echo $row1->title4; ?>"
                                    class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Title 5</label>
                            <div class="controls">
                                <input type="text" name="title5" value="<?php echo $row1->title5; ?>"
                                    class="input-xlarge span5">
                            </div>

                            <label class="control-label" for="input01">Template 1</label>
                            <div class="controls">
                                <textarea name="template1" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->template1; ?></textarea>
                            </div>

                            <label class="control-label" for="input01">Template 2</label>
                            <div class="controls">
                                <textarea name="template2" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->template2; ?></textarea>
                            </div>

                            <label class="control-label" for="input01">Template 3</label>
                            <div class="controls">
                                <textarea name="template3" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->template3; ?></textarea>
                            </div>

                            <label class="control-label" for="input01">Template 4</label>
                            <div class="controls">
                                <textarea name="template4" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->template4; ?></textarea>
                            </div>

                            <label class="control-label" for="input01">Template 5</label>
                            <div class="controls">
                                <textarea name="template5" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->template5; ?></textarea>
                            </div>

                            <label class="control-label" for="input01">Find and replace. Use as sitename|siteurl</label>
                            <div class="controls">
                                <input type="text" name="find_and_replace"
                                    value="<?php echo $row1->find_and_replace; ?>" class="input-xlarge span5">
                            </div>

                            <div class="insert-actions">
                                <div class="btn-toolbar">
                                    <p class="pull-right">
                                        <button class="btn btn-medium btn-primary">Save</button>
                                    </p>
                                </div>
                            </div>
                        </form>
                        <?php
							} else {
								echo "This url does not exist on our database.";
							}
						?>
                    </div>
                </div>
            </div>
            <?php
				}
				if(!empty($_GET['delete'])){
			?>
            <div class="span6">
                <div class="widget_heading">
                    <h4>Delete Url</h4>
                </div>
                <div class="widget_container">
                    <div class="control-group">
                        <?php
							if(!empty($_POST['delete'])){
								$insert = mysql_query("DELETE FROM `urls` WHERE `id`='".mysql_real_escape_string($_POST['chid'])."'") or die(mysql_error());
								if($insert){
									echo "This url has been successfully deleted.";
								} else {
									echo "Error while updating.Please try again...";
								}
							} else {
								$selecta = mysql_query("SELECT * FROM `urls` where `id`='".mysql_real_escape_string($_GET['delete'])."'") or die(mysql_error()); 
								if(mysql_num_rows($selecta) > 0){
									$row = mysql_fetch_object($selecta);
							?>
                        <form action="#" method="post">
                            <input type="hidden" name="delete" value="yes">
                            <input type="hidden" name="chid" value="<?php echo $row->id; ?>">
                            Are you sure you want to delete "<?php echo $row->url; ?>" url?
                            <br />
                            <button class="btn btn-medium btn-primary">Delete</button>
                        </form>
                        <?php 
							} else {
								echo "This url does not exist on our database.";
							}
							} 
						?>
                    </div>
                </div>
            </div>
            <?php
				}
				if(!empty($_GET['rebuild'])){
			?>

            <div class="span6">
                <div class="widget_heading">
                    <h4>Rebuild posts</h4>
                </div>
                <div class="widget_container">
                    <div class="control-group">
                        <?php
							if(!empty($_POST['rebuild'])){
								$insert = mysqli_query($conn, "UPDATE `keywords` SET `built`='0' WHERE `website`='".mysqli_real_escape_string($conn, $_POST['chid'])."'") or die(mysqli_error($conn));
							if($insert){
								$get_templates = mysqli_query($conn, "SELECT * FROM `websites` where `id`='".mysqli_real_escape_string($conn, $_POST['chid'])."'");
								$website = mysqli_fetch_object($get_templates);
									
								//connect to remote database
								$conn1 = mysqli_connect("localhost", htmlentities($website->user), htmlentities($website->pass), htmlentities($website->db));
								if($conn1 === false){
									//if failed to connect
									echo "Can't connect to remote database.";
								} else {
									//delete from remote
									$upd = mysqli_query($conn1, "DELETE FROM `pages`");
									if($upd){
										//if success to remote -> make deletions
										mysqli_query($conn, "DELETE FROM `built` WHERE `website`='".$website->id."'") or die(mysqli_error($conn));
										mysqli_query($conn, "DELETE FROM `featuredsnippets` WHERE `website`='".$website->id."'") or die(mysqli_error($conn));
										mysqli_query($conn, "DELETE FROM `keywordsqa` WHERE `website`='".$website->id."'") or die(mysqli_error($conn));
										mysqli_query($conn, "DELETE FROM `qa` WHERE `website`='".$website->id."'") or die(mysqli_error($conn));
										$selectkeywords = mysqli_query($conn, "SELECT `idkeywords` FROM `keywords` WHERE `website`='".$website->id."' and `url_scraped`!='0'") or die(mysqli_error($conn));
											if(mysqli_num_rows($selectkeywords) > 0){
												while($rkeyword = mysqli_fetch_object($selectkeywords)){
													mysqli_query($conn, "DELETE FROM `scraped_content_serp` WHERE `idkeywords`='".$rkeyword->idkeywords."'") or die(mysqli_error($conn));
													mysqli_query($conn, "DELETE FROM `serp_titles` WHERE `idkeywords`='".$rkeyword->idkeywords."'") or die(mysqli_error($conn));
													mysqli_query($conn, "DELETE FROM `intros` WHERE `idkeywords`='".$rkeyword->idkeywords."'") or die(mysqli_error($conn));
													mysqli_query($conn, "DELETE FROM `headlines` WHERE `idkeywords`='".$rkeyword->idkeywords."'") or die(mysqli_error($conn));
													//mark all keywords as not scraped
													mysqli_query($conn, "UPDATE `keywords` set `result1`='', `scraped`='0', `video`='0', `images`='0', `url_scraped`='0', `related_scraped`='0', `translated_qa`='0', `translated_snippet`='0', `translated_snippet`='0', `translated_serp`='0', `translated_serp_title`='0', `built`='0', `reason`='' WHERE `idkeywords`='".$rkeyword->idkeywords."'") or die(mysqli_error($conn));
												}
											}
										echo "<font color=\"red\">If no errors were shown, the below text is accurate</font><br>";
										echo "All posts for the selected website were marked for rebuild.";
										//reset the day
										mysqli_query($conn, "UPDATE `websites` SET `scraped`='0',`scraped_img`='0', `built`='0'  WHERE `id`='".$website->id."'") or die(mysqli_error($conn));
										//restart dripfeed
											if($website->dripfeed == "1"){
												mysqli_query($conn, "UPDATE `websites` SET `dripfeed_current`='1',`dripfeed_order`='1' WHERE `id`='".$website->id."'") or die(mysqli_error($conn));
											} else {
												mysqli_query($conn, "UPDATE `websites` SET `dripfeed_current`='".$website->perday."',`dripfeed_order`='999' WHERE `id`='".$website->id."'") or die(mysqli_error($conn));
											}
										} else {
											//if fail to remote
											echo "Can't delete posts in remote database.";
										}
									}
								} else {
									echo "Error while updating.Please try again...";
								}
							} else {
							$selecta = mysqli_query($conn, "SELECT * FROM `websites` where `id`='".mysql_real_escape_string($_GET['rebuild'])."'") or die(mysqli_error($conn)); 
							if(mysqli_num_rows($selecta) > 0){
								$row = mysqli_fetch_object($selecta);
						?>
                        <form action="#" method="post">
                            <input type="hidden" name="rebuild" value="yes">
                            <input type="hidden" name="chid" value="<?php echo $row->id; ?>">
                            Are you sure you want to rebuild posts for website "<?php echo $row->name; ?>"?
                            <br />
                            <button class="btn btn-medium btn-primary">Rebuild</button>
                        </form>
                        <?php 
							} else {
							echo "This website does not exist on our database.";
							}
							} 
						?>
                    </div>
                </div>
            </div>
            <?php
				}
			?>
            <?php if(!empty($_GET['edit'])){
			?>

            <script type="text/javascript">
            function new_window(x) {
                var myWindow = window.open("edit_ad.php?id=" + x, "info", "width=700,height=700");
            }
            </script>

            <div class="span6">
                <div class="widget_heading">
                    <h4>Edit Website</h4>
                </div>
                <div class="widget_container">
                    <div class="control-group">
                        <?php
							//
							$langs = mysqli_query($conn, "SELECT `languages` FROM `websites` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'");
							$langs = mysqli_fetch_object($langs);
							$languages = explode("\r\n", $langs->languages);
							//
							if(!empty($_POST['adverts'])){
							$update = mysql_query("UPDATE `websites` SET `top`='".mysqli_real_escape_string($conn, $_POST['top'])."', `top_display`='".mysqli_real_escape_string($conn, $_POST['top_display'])."', `middle`='".mysqli_real_escape_string($conn, $_POST['middle'])."', `middle_display`='".mysqli_real_escape_string($conn, $_POST['middle_display'])."', `bottom`='".mysqli_real_escape_string($conn, $_POST['bottom'])."', `bottom_display`='".mysqli_real_escape_string($conn, $_POST['bottom_display'])."' WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
							if($update){
								echo "<span style=\"color: green;font-size:15px;\">This website has been successfully updated.</span><br><br>";
								$website = $_GET['edit'];

								$top = advert($ftp[$website], $ftpuser[$website], $ftppassword[$website], $website, $conn, 1, $languages);
								$middle = advert($ftp[$website], $ftpuser[$website], $ftppassword[$website], $website, $conn, 2, $languages);
								$bottom = advert($ftp[$website], $ftpuser[$website], $ftppassword[$website], $website, $conn, 3, $languages);
								if($top == 0 or $middle == 0 or $bottom == 0){
									echo "<span style=\"color: red;font-size:15px;\">Error updating to remote files.Please try again...</span><br><br>";
								}
							} else {
								echo "<span style=\"color: green;font-size:15px;\">Error while entering on database.Please try again...</span><br><br>";
							}
							}
							$select = mysqli_query($conn, "SELECT * FROM `websites` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
							if(mysqli_num_rows($select) > 0){
							$row1 = mysqli_fetch_object($select);
						?>

                        <form action="#" method="post">
                            <input type="hidden" name="adverts" value="yes">
                            <label class="control-label" for="input01">Top advert</label>
                            <div class="controls">
                                <textarea name="top" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->top; ?></textarea>
                            </div>
                            <label class="control-label" for="input01">Display top?</label>
                            <div class="controls">
                                <select name="top_display">
                                    <option value="0" <?php if($row1->top_display == 0){ echo "selected"; } ?>>No
                                    </option>
                                    <option value="1" <?php if($row1->top_display == 1){ echo "selected"; } ?>>Yes
                                    </option>
                                </select>
                            </div>
                            <label class="control-label" for="input01">Middle advert</label>
                            <div class="controls">
                                <textarea name="middle" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->middle; ?></textarea>
                            </div>
                            <label class="control-label" for="input01">Display middle?</label>
                            <div class="controls">
                                <select name="middle_display">
                                    <option value="0" <?php if($row1->middle_display == 0){ echo "selected"; } ?>>No
                                    </option>
                                    <option value="1" <?php if($row1->middle_display == 1){ echo "selected"; } ?>>Yes
                                    </option>
                                </select>
                            </div>
                            <label class="control-label" for="input01">Bottom advert</label>
                            <div class="controls">
                                <textarea name="bottom" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->bottom; ?></textarea>
                            </div>
                            <label class="control-label" for="input01">Display bottom?</label>
                            <div class="controls">
                                <select name="bottom_display">
                                    <option value="0" <?php if($row1->bottom_display == 0){ echo "selected"; } ?>>No
                                    </option>
                                    <option value="1" <?php if($row1->bottom_display == 1){ echo "selected"; } ?>>Yes
                                    </option>
                                </select>
                            </div>
                            <div class="insert-actions">
                                <div class="btn-toolbar">
                                    <p class="pull-right">
                                        <button class="btn btn-medium btn-primary">Save</button>
                                    </p>
                                </div>
                            </div>
                        </form>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Lang</th>
                                    <th>Location</th>
                                    <th>Display</th>
                                    <th>Translated</th>
                                    <th>FTP</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <?php
								function progress($value, $none = false){
									if($value == 0){
										$img = "progress";
									}elseif($value == 1){
										$img = "success";
									}elseif($value == 2){
										$img = "failed";
									}
									if($none == 1 && $value == 0){
										$img = "failed";
									}
									$img = '<img src="ico/'.$img.'.png">';
									return $img;
								}
								$select_ads = mysqli_query($conn, "SELECT * from `ads_translation` WHERE `website`='".mysqli_real_escape_string($conn, $_GET['edit'])."' order by `id` asc");
								while($info_ads = mysqli_fetch_object($select_ads)){
								//echo "<b>".location($info_ads->location)."<br>".$info_ads->content."<br>";
							?>
                            <tr>
                                <td><?php echo $info_ads->id; ?></td>
                                <td><?php echo $info_ads->language;// echo '<img src="https://icons.iconarchive.com/icons/sicons/basic-round-social/512/wordpress-icon.png" width="32">'; } ?>
                                </td>
                                <td><?php if($info_ads->location == "1"){ echo "top"; } elseif($info_ads->location == "2"){ echo "middle"; } elseif($info_ads->location == "3"){ echo "bottom"; } ?>
                                </td>
                                <td><?php echo progress($info_ads->display, 1); ?></td>
                                <td><?php echo progress($info_ads->translated); ?></td>
                                <td><?php echo progress($info_ads->uploaded); ?></td>
                                <td><a class="btn btn-success"
                                        onclick="new_window(<?php echo $info_ads->id; ?>);">Edit</a></td>
                            </tr>
                            <?php
								}
							?>
                        </table>
                        <?php
							} else {
								echo "This url does not exist on our database.";
							}
						?>
                    </div>
                </div>
            </div>

            <div class="span6">
                <div class="widget_heading">
                    <h4>Translation</h4>
                </div>
                <div class="widget_container">
                    <div class="control-group">
                        <?php
							if(!empty($_POST['languages'])){
								$update = mysql_query("UPDATE `websites` SET `languages_spin`='".mysqli_real_escape_string($conn, $_POST['languages_spin'])."',`languages`='".mysqli_real_escape_string($conn, $_POST['langs'])."' WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
							if($update){
								echo "<span style=\"color: green;font-size:15px;\">This website has been successfully updated.</span><br><br>";
							} else {
								echo "<span style=\"color: green;font-size:15px;\">Error while entering on database.Please try again...</span><br><br>";
							}
							}
							$select = mysqli_query($conn, "SELECT * FROM `websites` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['edit'])."'") or die(mysqli_error($conn));
							if(mysqli_num_rows($select) > 0){
							$row1 = mysqli_fetch_object($select);
						?>
                        <form action="#" method="post">
                            <input type="hidden" name="languages" value="yes">
                            <label class="control-label" for="input01">Languages (<a href="langs.txt"
                                    target="_blank">see here</a>)</label>
                            <div class="controls">
                                <textarea name="langs" class="input-xlarge span5" rows="10"
                                    cols="100"><?php echo $row1->languages; ?></textarea>
                            </div>

                            <label class="control-label" for="input01">Spin content before translation</label>
                            <div class="controls">
                                <select name="languages_spin">
                                    <option value="0" <?php if($row1->languages_spin == 0){ echo "selected"; } ?>>No
                                        spinning</option>
                                    <option value="1" <?php if($row1->languages_spin == 1){ echo "selected"; } ?>>Every
                                        word</option>
                                    <option value="2" <?php if($row1->languages_spin == 2){ echo "selected"; } ?>>Every
                                        2 words</option>
                                    <option value="3" <?php if($row1->languages_spin == 3){ echo "selected"; } ?>>Every
                                        3 words</option>
                                    <option value="4" <?php if($row1->languages_spin == 4){ echo "selected"; } ?>>Every
                                        4 words</option>
                                    <option value="5" <?php if($row1->languages_spin == 5){ echo "selected"; } ?>>Every
                                        5 words</option>
                                    <option value="6" <?php if($row1->languages_spin == 6){ echo "selected"; } ?>>Every
                                        6 words</option>
                                    <option value="7" <?php if($row1->languages_spin == 7){ echo "selected"; } ?>>Every
                                        7 words</option>
                                    <option value="8" <?php if($row1->languages_spin == 8){ echo "selected"; } ?>>Every
                                        8 words</option>
                                    <option value="9" <?php if($row1->languages_spin == 9){ echo "selected"; } ?>>Every
                                        9 words</option>
                                    <option value="10" <?php if($row1->languages_spin == 10){ echo "selected"; } ?>>
                                        Every 10 words</option>
                                </select>
                            </div>
                            <div class="insert-actions">
                                <div class="btn-toolbar">
                                    <p class="pull-right">
                                        <button class="btn btn-medium btn-primary">Save</button>
                                    </p>
                                </div>
                            </div>
                        </form>
                        <?php
							} else {
								echo "This website does not exist on our database.";
							}
						?>
                    </div>
                </div>
            </div>
            <?php
				}
			?>
            <div class="span6">
                <div class="widget_heading">
                    <h4>Websites</h4>
                </div>
                <div class="widget_container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Website</th>
                                <th>Words</th>
                                <th>Posts/day</th>
                                <th>Edit</th>
                                <th>Rebuild</th>
                                <!--<th>Delete</th>-->
                            </tr>
                        </thead>
                        <?php
							$select = mysqli_query($conn, "SELECT * FROM `websites`") or die(mysql_error());
							if(mysqli_num_rows($select) > 0){
							while($row = mysqli_fetch_object($select)){
						?>
                        <tr>
                            <td><?php echo $row->id; ?></td>
                            <td><?php echo $row->name; if(!empty($row->wordpress_url)){ echo '<img src="https://icons.iconarchive.com/icons/sicons/basic-round-social/512/wordpress-icon.png" width="32">'; } ?>
                            </td>
                            <td><?php echo $row->words; ?></td>
                            <td><?php echo $row->perday; ?></td>
                            <td><a class="btn btn-success" href="websites.php?edit=<?php echo $row->id; ?>">Edit</a>
                            </td>
                            <td><a class="btn btn-danger"
                                    href="websites.php?rebuild=<?php echo $row->id; ?>">Rebuild</a></td>
                            <!--<td><a class="btn btn-danger" href="urls.php?delete=<?php echo $row->id; ?>">Delete</a></td>-->
                        </tr>
                        <?php
							}
							}
						?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--scripts-->
    <!--jQuery References-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript">
    </script>

    <script>
    function rewrite_edit(step, highlight, lang1, lang2, lang3) {
        var val1 = $('#' + step + ' option:selected').val();
        $.ajax({
            type: "GET",
            url: "rewrite_options.php?option=" + val1 + "&step=" + step,
            cache: false,
            success: function(data) {
                $("div[name=" + step + "_options]").html(data);
                $('#' + step + '_option option[value="' + highlight + '"]').prop('selected', true);
                if (lang1 != '') {
                    $('#lang1 option[value="' + lang1 + '"]').prop('selected', true);
                    $('#lang2 option[value="' + lang2 + '"]').prop('selected', true);
                    $('#lang3 option[value="' + lang3 + '"]').prop('selected', true);
                }
            }
        });
    }

    $(document).ready(function() {
        $("#button").click(function() {
            // Get value from input element on the page
            var db = $("input[name=db]").val();
            var user = $("input[name=user]").val();
            var pass = $("input[name=pass]").val();

            // Send the input data to the server using get
            $.get("dbcheck.php", {
                database: db,
                username: user,
                password: pass
            }, function(data) {
                // Display the returned data in browser
                if (data != "0") {
                    alert(data);
                    $('#result').hide();
                } else {
                    //alert("ii success");
                    $('#result').show();
                }
                //$("#result").html(data);
            });
        });

        $("#button1").click(function() {
            // Get value from input element on the page
            var ftp = $("input[name=ftp]").val();
            var ftpuser = $("input[name=ftpuser]").val();
            var ftppassword = $("input[name=ftppassword]").val();
            var wordpress = $("select[name=wordpress]").val();
            $('#loader').show();
            // Send the input data to the server using get
            $.get("ftpcheck.php", {
                user: ftpuser,
                password: ftppassword,
                ip: ftp
            }, function(data) {
                // Display the returned data in browser
                console.log("data: ", data);
                if (data != "0") {
                    alert(data);
                    $('#result1').hide();
                    $('#loader').hide();
                } else {
                    //alert("ii success");
                    $('#loader').hide();
                    if (wordpress == 1) {
                        $('#result').show();
                        $('#wordpress_url').show();
                    } else {
                        $('#result1').show();
                    }
                }
            });
        });

        $("#button2").click(function() {
            $('#loader1').show();
            var website = $("select[name=copy]").val();
            $.ajax({
                type: "GET",
                url: "copy_website.php?id=" + website,
                cache: false,
                dataType: "json",
                success: function(data) {
                    $('#loader1').hide();
                    $("select[name=not_matching]").val(data.not_matching);
                    $("input[name=words]").val(data.words);
                    $("input[name=perday]").val(data.perday);
                    $("select[name=spin]").val(data.spin);
                    $("select[name=exclude_paa_title]").val(data.exclude_paa_title);
                    $("select[name=nosnippets]").val(data.nosnippets);
                    $("select[name=dripfeed]").val(data.dripfeed);
                    $("select[name=serptitle]").val(data.serptitle);
                    $("select[name=prefix]").val(data.prefix);
                    $("select[name=post_tags]").val(data.wordpress_post_tags);
                    $("input[name=title1]").val(data.title1);
                    $("input[name=title2]").val(data.title2);
                    $("input[name=title3]").val(data.title3);
                    $("input[name=title4]").val(data.title4);
                    $("input[name=title5]").val(data.title5);
                    $("textarea[name=template1]").val(data.template1);
                    $("textarea[name=template2]").val(data.template2);
                    $("textarea[name=template3]").val(data.template3);
                    $("textarea[name=template4]").val(data.template4);
                    $("textarea[name=template5]").val(data.template5);
                    $("input[name=find_and_replace]").val(data.find_and_replace);
                }
            });
        });
        // rewrite process
        $('#step_1').change(
            /*function() {
			var val1 = $('#step_1 option:selected').val();
			$('#step_2 [value='+val1+']').attr('disabled', 'true');
			$('#step_3 [value='+val1+']').attr('disabled', 'true');
			
		}*/
            function() {
                var val1 = $('#step_1 option:selected').val();
                $.ajax({
                    type: "GET",
                    url: "rewrite_options.php?option=" + val1 + "&step=step_1",
                    cache: false,
                    success: function(data) {
                        $("div[name=step_1_options]").html(data);
                    }
                });
            }
        );

        $('#step_2').change(
            function() {
                var val1 = $('#step_2 option:selected').val();
                $.ajax({
                    type: "GET",
                    url: "rewrite_options.php?option=" + val1 + "&step=step_2",
                    cache: false,
                    success: function(data) {
                        $("div[name=step_2_options]").html(data);
                    }
                });
            }
        );

        $('#step_3').change(
            function() {
                var val1 = $('#step_3 option:selected').val();
                $.ajax({
                    type: "GET",
                    url: "rewrite_options.php?option=" + val1 + "&step=step_3",
                    cache: false,
                    success: function(data) {
                        $("div[name=step_3_options]").html(data);
                    }
                });
            }
        );

        $('#result').on('change', '#lang1',
            function() {
                var val1 = $('#lang1 option:selected').val();
                $('#lang2 [value=' + val1 + ']').attr('disabled', 'true');
                if (val1 == "Random") {
                    var $options = $('#lang1').find('option'),
                        random = ~~(Math.random() * $options.length);

                    $options.eq(random).prop('selected', true);
                    var val1 = $('#lang1 option:selected').val();
                    $('#lang2 [value=' + val1 + ']').remove();
                    $('#lang3 [value=' + val1 + ']').remove();
                    var $options = $('#lang2').find('option'),
                        random = ~~(Math.random() * $options.length);

                    $options.eq(random).prop('selected', true);
                    var val1 = $('#lang2 option:selected').val();
                    $('#lang3 [value=' + val1 + ']').remove();
                    var $options = $('#lang3').find('option'),
                        random = ~~(Math.random() * $options.length);

                    $options.eq(random).prop('selected', true);
                }
            }
        );

        $('#result').on('change', '#lang2',
            function() {
                var val1 = $('#lang2 option:selected').val();
                $('#lang3 [value=' + val1 + ']').attr('disabled', 'true');
            }
        );
        // rewrite end
    });
    </script>

    <!--Theme-->
    <link href="http://cdn.wijmo.com/themes/aristo/jquery-wijmo.css" rel="stylesheet" type="text/css"
        title="rocket-jqueryui" />

    <!--Wijmo Widgets CSS-->
    <link href="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.1.4.min.css" rel="stylesheet" type="text/css" />

    <!--Wijmo Widgets JavaScript-->
    <script src="http://cdn.wijmo.com/jquery.wijmo-open.all.2.1.4.min.js" type="text/javascript"></script>
    <script src="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.1.4.min.js" type="text/javascript"></script>

    <script src="assets/js/scriptdash.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
</body>

</html>
<?php
}
?>