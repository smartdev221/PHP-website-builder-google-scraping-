<?php
  session_start();
  session_destroy();
  unset($_SESSION['user']);
  unset($_SESSION['loggedin']);
  echo '<meta http-equiv="refresh" content="0; url=login.php">';
?>
