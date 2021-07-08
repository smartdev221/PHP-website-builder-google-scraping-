<?php
set_time_limit(0);
include("../config.php");
include_once("src/worker.php");

$worker = new worker($conn);

?>