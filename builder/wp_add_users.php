<?php 
 // Load WordPress
require_once '../wp-load.php';
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
//require_once ABSPATH . '/wp-admin/includes/taxonomy.php';
require_once ABSPATH . '/wp-includes/user.php';
 // Set the timezone so times are calculated correctly
date_default_timezone_set('Europe/London');

if($_POST['pass'] == 'damnpassword123!@#'){
	
$name = mysqli_real_escape_string($conn, $_POST['name']);
$expl = explode(" ", $name);

$user_id = wp_insert_user( array(
  'user_login' => str_replace(' ', '', strtolower($name)),
  'user_pass' => $_POST['pass'].date("y-m-d h:i:s"),
  'first_name' => $expl[0],
  'last_name' => $expl[1],
  'display_name' => $name,
  'role' => 'editor'
));

echo $user_id;

}
?>