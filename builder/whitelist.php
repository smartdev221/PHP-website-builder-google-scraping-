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


	<li class="active">
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


  <div class="span6">
<div class="widget_heading">
  <h4>Whitelist</h4>
</div>
 <div class="widget_container">


	<table class="table table-hover">
		 <thead>
			<tr>
				<th>#</th>
				<th>Website</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>
<?php
$select = mysql_query("SELECT * FROM `whitelist` ORDER BY `id` ASC") or die(mysql_error());
if(mysql_num_rows($select) > 0){
while($row = mysql_fetch_object($select)){

?>		
		<tr>
			<td><?php echo $row->id; ?></td>
			<td><?php echo substr($row->website,0,35); ?></td>
			<td><a class="btn btn-success" href="?edit=<?php echo $row->id; ?>">Edit</a></td>
			<td><a class="btn btn-danger" href="?delete=<?php echo $row->id; ?>">Delete</a></td>
		</tr>
<?php
}
}
?>		
	</table>




 </div>  </div>
 
 
 
 
<?php
if(!empty($_GET['delete'])){
?>

  <div class="span6"><div class="widget_heading"><h4>Delete Whitelist Website</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['delete'])){
  $insert = mysql_query("DELETE FROM `whitelist` WHERE `id`='".mysql_real_escape_string($_POST['chid'])."'") or die(mysql_error());
  if($insert){
  echo 'This website has been successfully deleted.<meta http-equiv="refresh" content="1; url=whitelist.php">';
  } else {
  echo "Error while updating.Please try again...";
  }
  } else {
  $selecta = mysql_query("SELECT * FROM `whitelist` where `id`='".mysql_real_escape_string($_GET['delete'])."'") or die(mysql_error()); 
  if(mysql_num_rows($selecta) > 0){
  $row = mysql_fetch_object($selecta);
  ?>
  <form action="#" method="post">
	<input type="hidden" name="delete" value="yes">
	<input type="hidden" name="chid" value="<?php echo $row->id; ?>">
Are you sure you want to delete <?php echo $row->tag; ?>'s whitelist?
<br />
<button class="btn btn-medium btn-primary">Delete</button>


</form>
<?php 
} else {
echo "This website does not exist on our database.";
}
} ?>
</div>
</div>
</div>
<?php
} 
?>
<div class="span6"><div class="widget_heading"><h4>Live links</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
 if(isset($_POST['showlinks'])){
    $update1 = mysql_query("UPDATE `settings` SET `showlinks`='".mysql_real_escape_string($_POST['showlinks'])."' WHERE id = '1'") or die(mysql_error());
  if($update1){
		echo "Successfully updated the live links status.";
  } else {
  echo "Couldn't update the live links status.Please try again...";
  }
}
	$selcookies = mysql_query("SELECT `showlinks` FROM `settings` WHERE `id`='1'") or die(mysql_error());
		$stats = mysql_fetch_object($selcookies);
  ?>
  <form action="#" method="post">
  <label class="control-label" for="input01">Show live links?</label>
  <div class="controls">
    <select name="showlinks" class="input-xlarge span5">
		<option value="1"<?php if($stats->showlinks == "1"){ echo " selected"; } ?>>Active</option>
		<option value="0"<?php if($stats->showlinks == "0"){ echo " selected"; } ?>>Disabled</option>

	</select>

</div>

       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Save</button></p>

</div>
</div>
</form>

</div>
</div>
</div>
<?php
if(empty($_GET['edit'])){
?>

  <div class="span6"><div class="widget_heading"><h4>Add website to whitelist</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['website'])){
  $insert = mysql_query("INSERT INTO `whitelist` (`website`) VALUES('".mysql_real_escape_string($_POST['website'])."')") or die(mysql_error());
  if($insert){
  echo 'This website has been successfully added to the whitelist.<meta http-equiv="refresh" content="1; url=whitelist.php">';
  } else {
  echo "Error while entering on database.Please try again...";
  }
  }
  ?>
  <form action="#" method="post">
  <label class="control-label" for="input01">Website</label>
<div class="controls">
	<input type="text" name="website" class="input-xlarge span5" >

</div>

       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Submit</button></p>

</div>
</div>
</form>
</div>
</div>
</div>
<?php
} else {
?>

  <div class="span6"><div class="widget_heading"><h4>Edit Website</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['website'])){
  $update = mysql_query("UPDATE `whitelist` SET `website`='".mysql_real_escape_string($_POST['website'])."' WHERE `id`='".mysql_real_escape_string($_GET['edit'])."'") or die(mysql_error());
  if($update){
  echo "This website has been successfully updated.";
  } else {
  echo "Error while entering on database.Please try again...";
  }
  }
  $select = mysql_query("SELECT * FROM `whitelist` WHERE `id`='".mysql_real_escape_string($_GET['edit'])."'") or die(mysql_error());
  if(mysql_num_rows($select) > 0){
  $row1 = mysql_fetch_object($select);
  ?>
  <form action="#" method="post">
  <label class="control-label" for="input01">Website</label>
<div class="controls">
	<input type="text" name="website" value="<?php echo $row1->website; ?>" class="input-xlarge span5" >

</div>
		
       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Save</button></p>

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