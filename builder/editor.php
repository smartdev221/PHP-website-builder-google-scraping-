<?php
session_start();
include("../config.php");

if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="build/jodit.min.css">
<link href="assets/css/bootstrap.css" rel="stylesheet">

    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
<script src="build/jodit.min.js"></script>
</head>
<body>
<?php
if(isset($_POST['editor'])){
	mysqli_query($conn, "UPDATE `windowsreport_fixes` SET `text`='".mysqli_real_escape_string($conn, $_POST['editor'])."', `edited`='1' WHERE `id`='".mysqli_real_escape_string($conn, $_GET['id'])."'");
	
}
if($_GET['delete'] == 1){
	mysqli_query($conn, "UPDATE `windowsreport_fixes` SET `deleted`='1' WHERE `id`='".mysqli_real_escape_string($conn, $_GET['id'])."'");
}
	$select = mysqli_query($conn, "SELECT * FROM `windowsreport_fixes` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['id'])."' and `deleted`='0'");
	
	if(mysqli_num_rows($select) > 0){
		$row = mysqli_fetch_object($select);

			echo "<b>Url:</b><a href=\"".$row->url."\" target=\"_blank\">".$row->url."</a><br>";
			echo "<b>Page title:</b>".$row->title."<br>";
			
?>
<form action="" method="post">
  <textarea id="editor" name="editor">
<?php
	echo $row->text;
?></textarea>
<br />
<button class="btn btn-medium btn-primary">Save</button>
<p class="pull-right">
<a class="btn btn-danger" href="editor.php?delete=1&id=<?php echo htmlentities($_GET['id']); ?>">Delete fix</a>
</p>
</form>
<script>var editor = new Jodit('#editor', {
    defaultMode: Jodit.MODE_SOURCE
});</script>
<?php
		
	} else {		
		echo "Subheading is no longer in the database.";
	}
	
	?>
</body>
</html>
	<?php
}
?>