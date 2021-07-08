<?php

include("../config.php");


$sel = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `id`='435'");
$data = mysqli_fetch_object($sel);

echo $data->content."<br><br><br><br>";
echo "<b>TRANSLATED:</b><br>";
echo $data->content_de;

?>