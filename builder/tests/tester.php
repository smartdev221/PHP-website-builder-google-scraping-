<?php
session_start();
include("../scraper.php");
include("../replaceurl.php");

if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
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
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,600,700' rel='stylesheet' type='text/css'>

  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#"><img src="assets/img/logo.png" alt=""/></a>

          <div class="nav-collapse">

         <ul class="nav pull-right">

  <li class="dropdown">
    <a href="pages.htm" class="dropdown-toggle" data-toggle="dropdown">
      <span style="padding-right:10px; width:30px;"><img src="assets/img/user_thumb.jpg" style="width:30px;" alt=""/></span>Mr <?php echo $_SESSION['user']; ?>
      <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      <li>
        <a href="my.php"><i style="font-size:14px; padding-top:3px; padding-right:5px;" class="icon-user"></i>My Account</a>
      </li>
	  <li>
        <a href="settings.php"><i style="font-size:14px; padding-top:3px; padding-right:5px;" class="icon-cogs"></i>Settings</a>
      </li>
    </ul>
  </li>
       <li>
     <div class="btn-group" style="margin-top:7px;"><a href="logout.php" class="medium twitter button radius" style="text-decoration:none;"><i style="font-size:16px; padding-top:3px; padding-right:5px;" class="icon-off"></i>Leap out</a> </div>
      </li>
</ul>
          </div><!--/.nav-collapse -->
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
      <a href="topics.php"><i style="margin-top:7px;" class="icon-th icon-large"></i>Topics</a>
    </li>
	<li>
      <a href="urls.php"><i style="margin-top:7px;" class=" icon-file icon-large"></i>Urls</a>
    </li>
	<li>
      <a href="settings.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Settings</a>
    </li>
	<li>
      <a href="boxsettings.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Box Settings</a>
    </li>
	<li class="active">
      <a href="tester.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Tester</a>
    </li>
	<li>
      <a href="export.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Export</a>
    </li>
	<li>

  <form style="padding:5px;" class="navbar-search pull-left" action="topics1.php">
            <input type="text" name="search" class="search-query span3" placeholder="Search a topic">
          </form>

    </li>
  </ul>

</div>




<div class="container" style="margin-top:30px;">

<div class="row">

  <div class="span6"><div class="widget_heading"><h4>Select Website and click Test</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['topost'])){
	  
	echo '<font color="green" size="3">For each selected website there is a default topic url that is to be tested. The one for your selection will be shown below.</font><br><br>';
		
		if($_POST['scraper'] == "1"){
			
			$details = topicts("http://www.techspot.com/community/topics/shuttle-ak35gtr-kt266a-motherboard-review.13/");
			
			echo '------------------------------------------------<br>First post from this <a href="http://www.techspot.com/community/topics/shuttle-ak35gtr-kt266a-motherboard-review.13/" target="_blank">url</a><br>------------------------------------------------<br>'.replaces($details['fpost']).'<br>------------------------------------------------<br><font color="green" size="3">If there is nothing shown above please try several times, maybe forum updates, who knows, if nothing shows up then go to your browser and check the forum url to see if its up then please contact me ASAP and tell me that this scraper is not working anymore.</font>';
		}elseif($_POST['scraper'] == "2"){
			
			$details = topicbc("http://www.bleepingcomputer.com/forums/t/710/windows-xp-sp2-release-candidate-2/");
			
			echo '------------------------------------------------<br>First post from this <a href="http://www.bleepingcomputer.com/forums/t/710/windows-xp-sp2-release-candidate-2/" target="_blank">url</a><br>';
			echo '------------------------------------------------<br>';
					$update = "";
					$pc = "1";
					foreach($details['replies'] as $reply){
							if($pc == 1){
								$update.=mysql_real_escape_string($reply['post']);
							}
						$pc++;
						}
			
			echo replaces($update);
			echo '<br>------------------------------------------------<br><font color="green" size="3">If there is nothing shown above please try several times, maybe forum updates, who knows, if nothing shows up then go to your browser and check the forum url to see if its up then please contact me ASAP and tell me that this scraper is not working anymore.</font>';
		
		}elseif($_POST['scraper'] == "3"){
			
			$details = topictg("https://forums.techguy.org/threads/lightbulbs.31978/");
			
			echo '------------------------------------------------<br>First post from this <a href="https://forums.techguy.org/threads/lightbulbs.31978/" target="_blank">url</a><br>';
			echo '------------------------------------------------<br>';
					$update = "";
					$pc = "1";
					foreach($details['replies'] as $reply){
							if($pc == 1){
								$update.=mysql_real_escape_string($reply['post']);
							}
						$pc++;
						}
			
			echo replaces($update);
			echo '<br>------------------------------------------------<br><font color="green" size="3">If there is nothing shown above please try several times, maybe forum updates, who knows, if nothing shows up then go to your browser and check the forum url to see if its up then please contact me ASAP and tell me that this scraper is not working anymore.</font>';
		}elseif($_POST['scraper'] == "4"){
			
			$details = topictn("https://social.technet.microsoft.com/Forums/en-US/4aac2dd0-e51b-4a36-af66-35248382ede9/how-to-enable-and-configure-hyperv-on-windows-8?forum=w8itprovirt");
			
			echo '------------------------------------------------<br>First post from this <a href="https://social.technet.microsoft.com/Forums/en-US/4aac2dd0-e51b-4a36-af66-35248382ede9/how-to-enable-and-configure-hyperv-on-windows-8?forum=w8itprovirt" target="_blank">url</a><br>';
			echo '------------------------------------------------<br>';
			
			echo replaces($details['fpost']);
			echo '<br>------------------------------------------------<br><font color="green" size="3">If there is nothing shown above please try several times, maybe forum updates, who knows, if nothing shows up then go to your browser and check the forum url to see if its up then please contact me ASAP and tell me that this scraper is not working anymore.</font>';
		}elseif($_POST['scraper'] == "5"){
			
			$details = topictsf("http://www.techsupportforum.com/forums/f299/rc1-wont-install-shuts-down-during-install-125450.html");
			
			echo '------------------------------------------------<br>First post from this <a href="http://www.techsupportforum.com/forums/f299/rc1-wont-install-shuts-down-during-install-125450.html" target="_blank">url</a><br>';
			echo '------------------------------------------------<br>';
					$update = "";
					$pc = "1";
					foreach($details['replies'] as $reply){
							if($pc == 1){
								$update.=mysql_real_escape_string($reply['post']);
							}
						$pc++;
						}
			
			echo replaces($update);
			echo '<br>------------------------------------------------<br><font color="green" size="3">If there is nothing shown above please try several times, maybe forum updates, who knows, if nothing shows up then go to your browser and check the forum url to see if its up then please contact me ASAP and tell me that this scraper is not working anymore.</font>';
		}elseif($_POST['scraper'] == "6"){
			
			$details = topicsf("http://www.sevenforums.com/general-discussion/45-oems-use-all-one-mobs.html");
			
			echo '------------------------------------------------<br>First post from this <a href="http://www.sevenforums.com/general-discussion/45-oems-use-all-one-mobs.html" target="_blank">url</a><br>';
			echo '------------------------------------------------<br>';
					$update = "";
					$pc = "1";
					foreach($details['replies'] as $reply){
							if($pc == 1){
								$update.=mysql_real_escape_string($reply['post']);
							}
						$pc++;
						}
			
			echo replaces($update);
			echo '<br>------------------------------------------------<br><font color="green" size="3">If there is nothing shown above please try several times, maybe forum updates, who knows, if nothing shows up then go to your browser and check the forum url to see if its up then please contact me ASAP and tell me that this scraper is not working anymore.</font>';
		}
  }
  ?>
  <form action="#" method="post">
	<input type="hidden" name="topost" value="1">
  <label class="control-label" for="input01">Website</label>
  <div class="controls">
	<select name="scraper" class="input-xlarge span5">
		<option value="1">www.techspot.com</option>
		<option value="2">www.bleepingcomputer.com</option>
		<option value="3">forums.techguy.org</option>
		<option value="4">social.technet.microsoft.com</option>
		<option value="5">www.techsupportforum.com</option>
		<option value="6">www.sevenforums.com</option>
	</select>

</div>
   		
       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Test</button></p>

</div>
</div>
</form>
</div>
</div>
</div>


</div></div>



        <!--scripts-->

            <!--jQuery References-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>

<!--Theme-->
<link href="http://cdn.wijmo.com/themes/aristo/jquery-wijmo.css" rel="stylesheet" type="text/css" title="rocket-jqueryui" />

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