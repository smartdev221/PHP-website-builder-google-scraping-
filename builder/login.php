<?php
session_start();
include('../config.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Administrator Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link type="text/css" href="assets/css/custom-theme/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">


    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">

    </style>
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



  <div class="container" style="margin-top:90px;">



   <div style="width:350px; margin-left:auto; margin-right:auto;">
<div class="widget_login"><div class="page-header">
  <h3>
   Adminstrator Login
    <small>into your account</small>
  </h3>
  <font color="red" size="3"><?php
  if($_POST['user']){
$login=mysqli_query($conn, "SELECT * from login where user='".mysqli_real_escape_string($conn, $_POST['user'])."' and pass='".mysqli_real_escape_string($conn, md5($_POST['pass']))."'") or die(mysql_error());
if(mysqli_num_rows($login) > 0){
$_SESSION['loggedin'] = '1';
$_SESSION['user'] = $_POST['user'];
echo 'Redirecting....
<meta http-equiv="refresh" content="3; url=index.php">';
} else {
echo 'Wrong username or password.';
}
}
?></font>
</div>
<form action="#" method="POST">
<div class="control-group">
  <label class="control-label" for="input01">Username</label>
  <div class="controls">
    <input type="text" class="input-xlarge" name="user" style="width:285px; padding:10px;" id="input01">
  </div>
   <label class="control-label" for="input01">Password</label>
  <div class="controls">
    <input type="password" class="input-xlarge" name="pass" style="width:285px; padding:10px;" id="input01">

  <button type="submit" name="submit" style="width:100%;" class="large green button radius" >Login</button>
  </div></div>
</form>  

</div>
 </div>




</div>





        <!--LOAD JQUERY/JAVASCRIPT ASSETS-->

        <script type="text/javascript" src="assets/js/jquery-1.6.2.min.js"></script>
       <script type="text/javascript" src="assets/js/bootstrap.js"></script>



  </body>
</html>