<?php

session_start();
include('../config.php');
// include('../replaceurl.php');

if(empty($_SESSION['loggedin'])){
  echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- LOAD CSS ASSETS -->
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!--LOAD GOOGLE WEBFONTS -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,600,700' rel='stylesheet'
        type='text/css'>

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
</head>

<body>
    <!--START NAVBAR -->
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
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
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
    <!--START SUB-NAVBAR -->
    <div class="subnav subnav-fixed">
        <ul class="nav nav-pills">
            <li class="active">
                <a href="index.php">
                    <i style="margin-top:7px;" class="icon-dashboard icon-large"></i>Dashboard</a>
            </li>
            <li>
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
    <!--END NAVBAR -->

    <!--START MAIN-CONTENT -->
    <div class="container" style="margin-top:30px;">
        <!--START STATS-WIDGET -->
        <div class="row">
            <div class="span7">
                <div class="widget_heading">
                    <h4>Statistics</h4>
                </div>
                <div class="widget_container">
                    <?php
                    //STATISTICS
                      $selecttt = mysqli_query($conn, "SELECT * FROM `websites`");
                      $stats = mysqli_num_rows($selecttt);
                    ?>
                    <ul id="sortable" class="unstyled" style="padding-left:20px;">
                        <li class="span2 ui-state-default" onclick="window.location.assign('websites.php');">
                            <div class="infoblock shadow">
                                <h1 style="color:#0099FF;"><?php echo $stats; ?></h1>
                                <p>Websites added</p>
                            </div>
                        </li>
                        <?php while($row = mysqli_fetch_object($selecttt)){
                          $sel_cache = mysqli_query($conn, "SELECT * FROM `cache` WHERE `website_id`='".$row->id."'");
                          $data = mysqli_fetch_object($sel_cache);
                        ?>
                        <li class="span2 ui-state-default">
                            <div class="infoblock shadow">
                                <h1 style="color:#0099FF;"><?php echo $data->total; ?></h1>
                                <p>Keywords</p>
                                <p><?php echo $row->name; ?></p>
                            </div>
                        </li>
                        <li class="span2 ui-state-default">
                            <div class="infoblock shadow">
                                <h1 style="color:#27ff00;"><?php echo $data->built; ?></h1>
                                <p>built</p>
                                <p><?php echo $row->name; ?></p>
                            </div>
                        </li>
                        <li class="span2 ui-state-default">
                            <div class="infoblock shadow">
                                <h1 style="color:#ff9900;"><?php echo $data->failed; ?></h1>
                                <p>failed</p>
                                <p><?php echo $row->name; ?></p>
                            </div>
                        </li>
                        <?php } ?>
                        <!--<li class="span2 ui-state-default" onclick="window.location.assign('sitemap.php');"><div class="infoblock shadow"><h1 style="color:#ff9900;">~123</h1>
       <p><a href="sitemap.php">Sitemaps</a></p></div>
    </li>-->
                    </ul>
                </div>
            </div>
            <!--END STATS-WIDGET -->

            <!--START -RECENT-POSTS-WIDGET -->
            <div class="span5">
                <div class="widget_heading">
                    <h4>Recent Keywords</h4>
                </div>
                <div class="widget_container">
                    <ul class="unstyled">
                        <?php
                          $select = mysqli_query($conn, "SELECT idkeywords,original FROM `keywords` WHERE `scraped`='1' ORDER BY idkeywords DESC LIMIT 10") or die(mysql_error());  
                          if(mysqli_num_rows($select) < 1){
                            echo "Sorry...there are no recent scraped keywords.";
                          } else {
                            while($row = mysqli_fetch_object($select)){
                        ?>
                        <li class="widget_recent_posts">
                            <div class="widget_rp_des">
                                <!--<h4><a href="/topic/<?php echo $row->id1."/".url_slug($row->title); ?>.html" target="_blank"><?php 
                                echo $row->original; ?></a>-->
                                <h4><a href="#" target="_blank"><?php 
                                echo $row->original; ?></a>
                            </div>
                        </li>
                        <?php
                            }
                          }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <!--END-RECENT-POSTS-WIDGET -->
    </div>
    </div>

    <footer>
        <div class="footer_container">
            <div class="container">
                <p style="margin-left:10px;">froggy - the awesome admin panel</p>
            </div>
        </div>
    </footer>
    <!--LOAD JQUERY UI ASSETS-->

    <!--jQuery References-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript">
    </script>

    <!--Wijmo Widgets JavaScript-->
    <!--Wijmo Widgets JavaScript-->
    <script src="http://cdn.wijmo.com/jquery.wijmo-open.all.2.1.4.min.js" type="text/javascript"></script>
    <script src="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.1.4.min.js" type="text/javascript"></script>

    <!--LOAD JQUERY/JAVASCRIPT ASSETS-->
    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
    <script src="assets/js/scriptdash.js" type="text/javascript"></script>
</body>

</html>
<?php
}
?>