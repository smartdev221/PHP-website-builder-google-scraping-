<?php
session_start();
include("../config.php");

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
	<li class="active">
      <a href="boxsettings.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Box Settings</a>
    </li>
	<li>
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


  <div class="span6"><div class="widget_heading"><h4>Settings</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
 if(!empty($_POST['cboxt'])){
		
    $update = mysql_query("UPDATE settings SET hide='".mysql_real_escape_string($_POST['hide'])."',cboxs='".mysql_real_escape_string($_POST['cboxs'])."',cboxr='".mysql_real_escape_string($_POST['cboxr'])."',cboxrs='".mysql_real_escape_string($_POST['cboxrs'])."',cboxbc='".mysql_real_escape_string($_POST['cboxbc'])."',cboxt='".mysql_real_escape_string($_POST['cboxt'])."',cboxto='".mysql_real_escape_string($_POST['cboxto'])."',cboxtc='".mysql_real_escape_string($_POST['cboxtc'])."',cboxa='".mysql_real_escape_string($_POST['cboxa'])."',cboxac='".mysql_real_escape_string($_POST['cboxac'])."',cboxmt='".mysql_real_escape_string($_POST['cboxmt'])."',cboxmtc='".mysql_real_escape_string($_POST['cboxmtc'])."' WHERE id = '1'") or die(mysql_error());
  if($update){
		echo "Successfully saved.";
  } else {
  echo "Error while updating.Please try again...";
  }
  }
  $select = mysql_query("SELECT * FROM settings WHERE id='1'") or die(mysql_error());
	if(mysql_num_rows($select) > 0){
	$row = mysql_fetch_object($select);
  ?>
  <form action="#" method="post">
  <label class="control-label" for="input01">Hide Content?</label>
  <div class="controls">
	<select name="hide" class="input-xlarge span5">
		<option value="1"<?php if($row->hide == "1"){ echo " selected"; } ?>>Yes</option>
		<option value="0"<?php if($row->hide == "0"){ echo " selected"; } ?>>No</option>
	</select>

</div>
  <label class="control-label" for="input01">Custom Box Status</label>
  <div class="controls">
	<select name="cboxs" class="input-xlarge span5">
		<option value="1"<?php if($row->cboxs == "1"){ echo " selected"; } ?>>On</option>
		<option value="0"<?php if($row->cboxs == "0"){ echo " selected"; } ?>>Off</option>
	</select>

</div>

  <label class="control-label" for="input01">Box Title</label>
  <div class="controls">
    <input type="text" name="cboxt" value="<?php echo $row->cboxt; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Original Topic Title</label>
  <div class="controls">
	<select name="cboxto" class="input-xlarge span5">
		<option value="1"<?php if($row->cboxto == "1"){ echo " selected"; } ?>>On</option>
		<option value="0"<?php if($row->cboxto == "0"){ echo " selected"; } ?>>Off</option>
	</select>

</div>
  <label class="control-label" for="input01">Title Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxtc" value="<?php echo $row->cboxtc; ?>" class="input-xlarge span5" >

</div>
  <label class="control-label" for="input01">Box Title Prefix(e.g. A:)</label>
  <div class="controls">
    <input type="text" name="cboxa" value="<?php echo $row->cboxa; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Title Color(default: #90C3F2)</label>
  <div class="controls">
    <input type="text" name="cboxac" value="<?php echo $row->cboxac; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Relevancy Score Status</label>
  <div class="controls">
	<select name="cboxr" class="input-xlarge span5">
		<option value="1"<?php if($row->cboxr == "1"){ echo " selected"; } ?>>On</option>
		<option value="0"<?php if($row->cboxr == "0"){ echo " selected"; } ?>>Off</option>
	</select>

</div>

  <label class="control-label" for="input01">Relevancy Score</label>
  <div class="controls">
    <input type="text" name="cboxrs" value="<?php echo $row->cboxrs; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxbc" value="<?php echo $row->cboxbc; ?>" class="input-xlarge span5" >

</div>
  <label class="control-label" for="input01">Main Text Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxmtc" value="<?php echo $row->cboxmtc; ?>" class="input-xlarge span5" >

</div>
  
  <label class="control-label" for="input01">Main Text</label>
  <div class="controls">
    <textarea name="cboxmt" rows="15" class="input-xlarge span5"><?php echo $row->cboxmt; ?></textarea>

</div>

       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Save</button></p>

</div>
</div>
</form>
<?php 

}
?>
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