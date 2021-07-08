<?php

//Database Details
$username = 'fixcompu_thesaur';
$password = 'damnpassword123!@#';
$database = 'fixcompu_thesaurus';
$host	  = 'localhost';

$conn = mysqli_connect($host, $username, $password, $database);
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>