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
	<li>
      <a href="tester.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Tester</a>
    </li>
	<li class="active">
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

  <div class="span6"><div class="widget_heading"><h4>Select desired website and click Export</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  
  
	function replace_carriage_return($replace, $string){
		
		return str_replace(array("\n\r", "\n", "\r", "\t"), $replace, $string);
	}

  if(!empty($_POST['topost'])){
	  
	echo '<font color="green" size="3">Please do not close this page until it finishes loading.</font><br>';
		
		if(is_numeric($_POST['website'])){
			//unlink($_POST['website'].'.csv');	
			$myfile = fopen($_POST['website'].".csv", "w") or die("Unable to open file!");
			
			$mysqll = mysql_query("SELECT `title`,`url`,`post`,`reply` FROM `topics` WHERE `scraper`='".$_POST['website']."' and `published`='1'") or die(mysql_error());
				while($row = mysql_fetch_object($mysqll)){
					
								$questiontext = replaces1($row->post);
								$char = "28000";
								$count_dispname=strlen($questiontext);
								if($count_dispname > $char){
								$post = substr($questiontext, 0, $char);
								} else { 
								$post= replaces1($row->post);
								}
					
					$txt = replaces1($row->title)."\t".replaces1($row->url)."\t".$post."\t".replaces1($row->reply);
					$text = array($txt);
					//$txt = $row->title."\n";
					fputcsv($myfile, $text, "\t");
					//echo $row->title."<br>";
				} 
				
				
			fclose($myfile);
			
			echo '<font color="green" size="3">------------------------------------------------<br>Click <a href="'.$_POST['website'].'.csv" target="_blank">here to download the export</a></font><br><br>Click <a href="export.php?del='.$_POST['website'].'">here to delete the export</a><br><br>';
			
		}
  } elseif(is_numeric($_GET['del'])){
			unlink($_GET['del'].'.csv');
			
			echo '<font color="green" size="3">The export was deleted.</font>';
  }
  ?>
  <form action="#" method="post">
	<input type="hidden" name="topost" value="1">
  <label class="control-label" for="input01">Export</label>
  <div class="controls">
	<select name="website" class="input-xlarge span5">
		<option value="1">www.techspot.com</option>
		<option value="2">www.bleepingcomputer.com</option>
		<option value="3">forums.techguy.org</option>
		<option value="4">social.technet.microsoft.com</option>
		<option value="5">www.techsupportforum.com</option>
		<option value="6">www.sevenforums.com</option>
		<option value="7">www.vistax64.com</option>
		<option value="8">www.eightforums.com</option>
		<option value="9">www.tenforums.com</option>
		<option value="10">www.pchelpforum.com</option>
		<option value="11">malwaretips.com</option>
		<option value="12">en.community.dell.com</option>
		<option value="13">community.acer.com</option>
		<option value="14">h30434.www3.hp.com</option>
		<option value="15">forums.lenovo.com/</option>
		<option value="16">forum.toshiba.eu</option>
	</select>

</div>
   		
       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Export</button></p>

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