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
  <h4>Custom Promo Boxes</h4>
</div>
 <div class="widget_container">


	<table class="table table-hover">
		 <thead>
			<tr>
				<th>#</th>
				<th>Promo Box</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tr>
			<td>0</td>
			<td>Default Promo Box</td>
			<td><a class="btn btn-success" href="boxsettings.php">Edit</a></td>
			<td></td>
		</tr>
<?php
$select = mysql_query("SELECT * FROM `boxes` ORDER BY `id` ASC") or die(mysql_error());
if(mysql_num_rows($select) > 0){
while($row = mysql_fetch_object($select)){

?>		
		<tr>
			<td><?php echo $row->id; ?></td>
			<td><?php echo substr($row->tag,0,35); ?></td>
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

  <div class="span6"><div class="widget_heading"><h4>Delete Promo Box</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['delete'])){
  $insert = mysql_query("DELETE FROM `boxes` WHERE `id`='".mysql_real_escape_string($_POST['chid'])."'") or die(mysql_error());
  if($insert){
  echo 'This promo box has been successfully deleted.<meta http-equiv="refresh" content="1; url=promo.php">';
  } else {
  echo "Error while updating.Please try again...";
  }
  } else {
  $selecta = mysql_query("SELECT * FROM `boxes` where `id`='".mysql_real_escape_string($_GET['delete'])."'") or die(mysql_error()); 
  if(mysql_num_rows($selecta) > 0){
  $row = mysql_fetch_object($selecta);
  ?>
  <form action="#" method="post">
	<input type="hidden" name="delete" value="yes">
	<input type="hidden" name="chid" value="<?php echo $row->id; ?>">
Are you sure you want to delete "<?php echo $row->tag; ?>" promo box?
<br />
<button class="btn btn-medium btn-primary">Delete</button>


</form>
<?php 
} else {
echo "This promo box does not exist on our database.";
}
} ?>
</div>
</div>
</div>
<?php
} 
if(empty($_GET['edit'])){
?>

  <div class="span6"><div class="widget_heading"><h4>Add custom promo box</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['tag'])){
  $insert = mysql_query("INSERT INTO `boxes` (`tag`,`cboxt`,`cboxto`,`cboxtc`,`cboxa`,`cboxac`,`cboxr`,`cboxrs`,`cboxbc`,`cboxmtc`,`cboxmt`,`priority`,`akeywords`,`keywords`,`source`) VALUES('".mysql_real_escape_string($_POST['tag'])."','".mysql_real_escape_string($_POST['cboxt'])."','".mysql_real_escape_string($_POST['cboxto'])."','".mysql_real_escape_string($_POST['cboxtc'])."','".mysql_real_escape_string($_POST['cboxa'])."','".mysql_real_escape_string($_POST['cboxac'])."','".mysql_real_escape_string($_POST['cboxr'])."','".mysql_real_escape_string($_POST['cboxrs'])."','".mysql_real_escape_string($_POST['cboxbc'])."','".mysql_real_escape_string($_POST['cboxmtc'])."','".mysql_real_escape_string($_POST['cboxmt'])."','".mysql_real_escape_string($_POST['priority'])."','".mysql_real_escape_string($_POST['akeywords'])."','".mysql_real_escape_string($_POST['keywords'])."','".mysql_real_escape_string($_POST['source'])."')") or die(mysql_error());
  if($insert){
  echo 'This promo box has been successfully added.<meta http-equiv="refresh" content="1; url=promo.php">';
  } else {
  echo "Error while entering on database.Please try again...";
  }
  }
  ?>
  <form action="#" method="post">
  <label class="control-label" for="input01">Custom Promobox Tag(so you recognise it in the list)</label>
<div class="controls">
	<input type="text" name="tag" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Title</label>
  <div class="controls">
    <input type="text" name="cboxt" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Original Topic Title</label>
  <div class="controls">
	<select name="cboxto" class="input-xlarge span5">
		<option value="1">On</option>
		<option value="0">Off</option>
	</select>

</div>
  <label class="control-label" for="input01">Title Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxtc" class="input-xlarge span5" >

</div>
  <label class="control-label" for="input01">Box Title Prefix(e.g. A:)</label>
  <div class="controls">
    <input type="text" name="cboxa" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Title Color(default: #90C3F2)</label>
  <div class="controls">
    <input type="text" name="cboxac" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Relevancy Score Status</label>
  <div class="controls">
	<select name="cboxr" class="input-xlarge span5">
		<option value="1">On</option>
		<option value="0">Off</option>
	</select>

</div>

  <label class="control-label" for="input01">Relevancy Score</label>
  <div class="controls">
    <input type="text" name="cboxrs" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxbc" class="input-xlarge span5" >

</div>
  <label class="control-label" for="input01">Main Text Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxmtc" class="input-xlarge span5" >

</div>
  
  <label class="control-label" for="input01">Main Text</label>
  <div class="controls">
    <textarea name="cboxmt" rows="15" class="input-xlarge span5"></textarea>

</div>

  <label class="control-label" for="input01">Priority(0.0-1) - 1 being highest</label>
  <div class="controls">
    <input type="text" name="priority" class="input-xlarge span5">

</div>

<label class="control-label" for="input01">Match all keywords?</label>
  <div class="controls">
	<select name="akeywords" class="input-xlarge span5">
		<option value="0">No</option>
		<option value="1">Yes</option>
	</select>

  <label class="control-label" for="input01">Keywords - format    keyword1|keyword2|keyword3  - !!! do not hit enter</label>
  <div class="controls">
    <textarea name="keywords" rows="1" class="input-xlarge span5"></textarea>

</div>
  
  <label class="control-label" for="input01">Only show to a specific source</label>
  <div class="controls">
	<select name="source" class="input-xlarge span5">
		<option value="0">None</option>
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

  <div class="span6"><div class="widget_heading"><h4>Edit Promo Box</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
  if(!empty($_POST['tag'])){
  $update = mysql_query("UPDATE `boxes` SET `tag`='".mysql_real_escape_string($_POST['tag'])."',`cboxt`='".mysql_real_escape_string($_POST['cboxt'])."',`cboxto`='".mysql_real_escape_string($_POST['cboxto'])."',`cboxtc`='".mysql_real_escape_string($_POST['cboxtc'])."',`cboxa`='".mysql_real_escape_string($_POST['cboxa'])."',`cboxac`='".mysql_real_escape_string($_POST['cboxac'])."',`cboxr`='".mysql_real_escape_string($_POST['cboxr'])."',`cboxrs`='".mysql_real_escape_string($_POST['cboxrs'])."',`cboxbc`='".mysql_real_escape_string($_POST['cboxbc'])."',`cboxmtc`='".mysql_real_escape_string($_POST['cboxmtc'])."',`cboxmt`='".mysql_real_escape_string($_POST['cboxmt'])."',`priority`='".mysql_real_escape_string($_POST['priority'])."',`akeywords`='".mysql_real_escape_string($_POST['akeywords'])."',`keywords`='".mysql_real_escape_string($_POST['keywords'])."',`source`='".mysql_real_escape_string($_POST['source'])."' WHERE `id`='".mysql_real_escape_string($_GET['edit'])."'") or die(mysql_error());
  if($update){
  echo "This promobox has been successfully updated.";
  } else {
  echo "Error while entering on database.Please try again...";
  }
  }
  $select = mysql_query("SELECT * FROM `boxes` WHERE `id`='".mysql_real_escape_string($_GET['edit'])."'") or die(mysql_error());
  if(mysql_num_rows($select) > 0){
  $row1 = mysql_fetch_object($select);
  ?>
  <form action="#" method="post">
  <label class="control-label" for="input01">Custom Promobox Tag(so you recognise it in the list)</label>
<div class="controls">
	<input type="text" name="tag" value="<?php echo $row1->tag; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Title</label>
  <div class="controls">
    <input type="text" name="cboxt" value="<?php echo $row1->cboxt; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Original Topic Title</label>
  <div class="controls">
	<select name="cboxto" class="input-xlarge span5">
		<option value="1"<?php if($row1->cboxto == "1"){ echo " selected"; } ?>>On</option>
		<option value="0"<?php if($row1->cboxto == "0"){ echo " selected"; } ?>>Off</option>
	</select>

</div>
  <label class="control-label" for="input01">Title Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxtc" value="<?php echo $row1->cboxtc; ?>" class="input-xlarge span5" >

</div>
  <label class="control-label" for="input01">Box Title Prefix(e.g. A:)</label>
  <div class="controls">
    <input type="text" name="cboxa" value="<?php echo $row1->cboxa; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Title Color(default: #90C3F2)</label>
  <div class="controls">
    <input type="text" name="cboxac" value="<?php echo $row1->cboxac; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Relevancy Score Status</label>
  <div class="controls">
	<select name="cboxr" class="input-xlarge span5">
		<option value="1"<?php if($row1->cboxr == "1"){ echo " selected"; } ?>>On</option>
		<option value="0"<?php if($row1->cboxr == "0"){ echo " selected"; } ?>>Off</option>
	</select>

</div>

  <label class="control-label" for="input01">Relevancy Score</label>
  <div class="controls">
    <input type="text" name="cboxrs" value="<?php echo $row1->cboxrs; ?>" class="input-xlarge span5" >

</div>

  <label class="control-label" for="input01">Box Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxbc" value="<?php echo $row1->cboxbc; ?>" class="input-xlarge span5" >

</div>
  <label class="control-label" for="input01">Main Text Color(format #XXXXXX)</label>
  <div class="controls">
    <input type="text" name="cboxmtc" value="<?php echo $row1->cboxmtc; ?>" class="input-xlarge span5" >

</div>
  
  <label class="control-label" for="input01">Main Text</label>
  <div class="controls">
    <textarea name="cboxmt" rows="15" class="input-xlarge span5"><?php echo $row1->cboxmt; ?></textarea>

</div>

  <label class="control-label" for="input01">Priority(0.0-1) - 1 being highest</label>
  <div class="controls">
    <input type="text" name="priority" value="<?php echo $row1->priority; ?>" class="input-xlarge span5">

</div>

<label class="control-label" for="input01">Match all keywords?</label>
  <div class="controls">
	<select name="akeywords" class="input-xlarge span5">
		<option value="0"<?php if($row1->akeywords == "0"){ echo " selected"; } ?>>No</option>
		<option value="1"<?php if($row1->akeywords == "1"){ echo " selected"; } ?>>Yes</option>
	</select>

  <label class="control-label" for="input01">Keywords - format    keyword1|keyword2|keyword3 - !!! do not hit enter</label>
  <div class="controls">
    <textarea name="keywords" rows="1" class="input-xlarge span5"><?php echo $row1->keywords; ?></textarea>

</div>
  
  <label class="control-label" for="input01">Only show to a specific source</label>
  <div class="controls">
	<select name="source" class="input-xlarge span5">
		<option value="0"<?php if($row1->source == "0"){ echo " selected"; } ?>>None</option>
		<option value="1"<?php if($row1->source == "1"){ echo " selected"; } ?>>www.techspot.com</option>
		<option value="2"<?php if($row1->source == "2"){ echo " selected"; } ?>>www.bleepingcomputer.com</option>
		<option value="3"<?php if($row1->source == "3"){ echo " selected"; } ?>>forums.techguy.org</option>
		<option value="4"<?php if($row1->source == "4"){ echo " selected"; } ?>>social.technet.microsoft.com</option>
		<option value="5"<?php if($row1->source == "5"){ echo " selected"; } ?>>www.techsupportforum.com</option>
		<option value="6"<?php if($row1->source == "6"){ echo " selected"; } ?>>www.sevenforums.com</option>
		<option value="7"<?php if($row1->source == "7"){ echo " selected"; } ?>>www.vistax64.com</option>
		<option value="8"<?php if($row1->source == "8"){ echo " selected"; } ?>>www.eightforums.com</option>
		<option value="9"<?php if($row1->source == "9"){ echo " selected"; } ?>>www.tenforums.com</option>
		<option value="10"<?php if($row1->source == "10"){ echo " selected"; } ?>>www.pchelpforum.com</option>
		<option value="11"<?php if($row1->source == "11"){ echo " selected"; } ?>>malwaretips.com</option>
		<option value="12"<?php if($row1->source == "12"){ echo " selected"; } ?>>en.community.dell.com</option>
		<option value="13"<?php if($row1->source == "13"){ echo " selected"; } ?>>community.acer.com</option>
		<option value="14"<?php if($row1->source == "14"){ echo " selected"; } ?>>h30434.www3.hp.com</option>
		<option value="15"<?php if($row1->source == "15"){ echo " selected"; } ?>>forums.lenovo.com/</option>
		<option value="16"<?php if($row1->source == "16"){ echo " selected"; } ?>>forum.toshiba.eu</option>
	</select>

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
echo "This promo box does not exist on our database.";
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