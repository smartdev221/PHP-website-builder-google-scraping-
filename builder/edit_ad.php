<?php
session_start();
include("../config.php");

if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
	
	$select = mysqli_query($conn, "SELECT * FROM `ads_translation` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['id'])."'");
	
	if(mysqli_num_rows($select) > 0){
		if(isset($_POST['submit'])){
			mysqli_query($conn, "UPDATE `ads_translation` set `content`='".mysqli_real_escape_string($conn, $_POST['content'])."',`display`='".mysqli_real_escape_string($conn, $_POST['display'])."',`uploaded`='0' WHERE `id`='".mysqli_real_escape_string($conn, $_GET['id'])."'") or die(mysqli_error());
		echo "<h3>Saved</h3>";
		}
		$select = mysqli_query($conn, "SELECT * FROM `ads_translation` WHERE `id`='".mysqli_real_escape_string($conn, $_GET['id'])."'");
		$row = mysqli_fetch_object($select);

?>
	<form action="#" method="post">
		<b>Language:</b><?php echo $row->language; ?><br>
		<b>Location:</b><?php echo $row->location; ?><br>
		Display ad:<select name="display">
		Translated:
			<option value="0" <?php if($row->display == 0){ echo "selected"; } ?>>No</option>
			<option value="1" <?php if($row->display == 1){ echo "selected"; } ?>>Yes</option>
		</select>
		<textarea name="content" rows="20" cols="80"><?php echo $row->content; ?></textarea>
	<input type="submit" name="submit" value="Save!"/>
	</form>
<?php	
	} else {
		
		echo "Ad is no longer in the database.";
	}
	
	
}
?>