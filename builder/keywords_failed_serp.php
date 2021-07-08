<?php
session_start();
include("../config.php");
include("../replaceurl.php");

function yesno($input, $id, $type){
	if($input == 1){ 
		$out = '<a onclick="new_window('.$id.','.$type.');">Yes</a>';
	} else {
		$out = "No";
	}
	
	return $out;
}
if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
	
	if($_GET['export'] == 1){
		unlink('failed_serp.csv');
		$fp = fopen('failed_serp.csv', 'w');
		
		$select = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `reason`!='' and `built`='4'") or die(mysqli_error($conn));
			while($ex = mysqli_fetch_object($select)){
				fputcsv($fp, array($ex->original, $ex->reason));
			}

		fclose($fp);
		echo "Download it here:<a href=\"failed_serp.csv\">Here</a>";
		break;
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
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
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
    <li class="active">
      <a href="keywords.php"><i style="margin-top:7px;" class="icon-th icon-large"></i>Keywords</a>
    </li>
	<li>
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
  <div class="container" style="margin-top:70px;">


<script type="text/javascript">
    function do_this(){

        var checkboxes = document.getElementsByName('topic[]');
        var button = document.getElementById('toggle');

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
	
function new_window(x, y) {
  var myWindow = window.open("info.php?id="+x+"&type="+y, "info", "width=700,height=700");
}
</script>


<div class="container">
  <div class="row">
  <div class="span12">
<div class="widget_heading">
  <h4>Keywords failed</h4>
</div>
 <div class="widget_container">
<a href="?export=1" class="btn btn-medium btn-primary">Export list</a>
<?php


?>
 
<form action="#" method="post"> 
	<table class="table table-hover">
		 <thead>
			<tr>
				<th>#</th>
				<th>Keyword</th>
				<th>Reason</th>
			</tr>
		</thead>
<?php
$select = mysql_query("SELECT idkeywords FROM `keywords` WHERE `reason`!='' and `built`='4'") or die(mysql_error());
$total_entries = mysql_num_rows($select);

$perpage_topics = 50;
    $page = @$_GET['page'];
    if (empty($page))
        $page = @$_POST['page'];

    if (!is_numeric($page))
        $page = 1;

    $total = $total_entries;

    $seiten = ceil($total / $perpage_topics);
    $anfangsseite = $seiten - $seiten + 1;

    if ($page > 1)
    {
        $first = ($perpage_topics * $page) - $perpage_topics;
        $last = $perpage_topics;
    }
    else
    {
        $first = 0;
        $last = $perpage_topics;
        $page = 1;
    }
$select1 = mysql_query("SELECT idkeywords,original,reason FROM `keywords` WHERE `reason`!='' and `built`='4' ORDER BY `idkeywords` DESC LIMIT ".$first.",".$last."") or die(mysql_error());
while($row = mysql_fetch_object($select1)){

	$class = ' style="background-color: rgb(242, 222, 222);"';

?>		
		<tr<?php echo $class; ?>>
			<td><?php echo $row->idkeywords; ?></td>
			<td><a onclick="new_window(<?php echo $row->idkeywords; ?>,1);"><?php echo $row->original; ?></a></td>
			<td><?php echo $row->reason; ?></td>
		</tr>
<?php
}

if($page > 1)
	{
		echo "&nbsp;<a href='keywords_failed_serp.php?page=1'>First</a>";
	}
	
	$k = 0;
	for ($i = ($page - 2); $i <= ($page + 2); $i++)
	{
		if($i > 0 && $i <= $seiten)
		{
			$k++;
			if($k > 1)
			{
				echo " ";
			}


			if ($page == $i)
			{
				echo "<strong>&nbsp;-&nbsp;".$i."</strong>";
			}
			else
			{
			echo "&nbsp;-&nbsp;<a href=keywords_failed_serp.php?page=".$i.">";
				echo "".$i."";
			}
			echo "</a>";
		}
	}

	if($page == $seiten)
	{
		echo "";
	}
	else
	{
		echo "&nbsp;-&nbsp;<a href=keywords_failed_serp.php?page=".$seiten.">Last</a>";
	}
	

?>		
	</table>
	<!--<input type="submit" name="submit" value="Delete selected">-->
</form>



 </div>  </div>
</div>
</div>



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