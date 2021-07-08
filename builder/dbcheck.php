<?php
session_start();
include("../config.php");

if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
	
	if(!empty($_GET['database']) && !empty($_GET['username']) && !empty($_GET['password'])){
		
		//echo $_GET['database'];
		$conn = mysqli_connect("localhost", htmlentities($_GET['username']), htmlentities($_GET['password']), htmlentities($_GET['database']));
		if($conn === false){
			die("ERROR: Could not connect. " . mysqli_connect_error());
		} else {
			echo "0";
			$sel = mysqli_query($conn, "SHOW TABLES LIKE 'pages'") or die(mysqli_error($conn));
			if(mysqli_num_rows($sel) < 1){
				$ins = mysqli_query($conn, "CREATE TABLE `pages` (  `id` int(255) NOT NULL, `idkeywords` INT(255) NULL, `language` VARCHAR(10) NOT NULL, `keyword` varchar(255) NOT NULL,  `identifier` varchar(255) NOT NULL, `author` INT(5) NOT NULL DEFAULT '0', `title` varchar(255) NOT NULL,  `html` text NOT NULL,  `excerpt` text NOT NULL,   `keywordtop1` varchar(255) NOT NULL,  `keywordtop2` varchar(255) NOT NULL,  `keywordtop3` varchar(255) NOT NULL,  `keywordtop4` varchar(255) NOT NULL,  `keywordtop5` varchar(255) NOT NULL,  `keywordtop6` varchar(255) NOT NULL,  `keywordtop7` varchar(255) NOT NULL,  `keywordtop8` varchar(255) NOT NULL,  `keywordtop9` varchar(255) NOT NULL,  `keywordtop10` varchar(255) NOT NULL,  `keywordtop11` varchar(255) NOT NULL,  `keywordtop12` varchar(255) NOT NULL,  `keywordtop13` varchar(255) NOT NULL,  `keywordtop14` varchar(255) NOT NULL,  `keywordtop15` varchar(255) NOT NULL,  `keywordtop16` varchar(255) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;") or die(mysqli_error($conn));
				$ins1 = mysqli_query($conn, "ALTER TABLE `pages` ADD PRIMARY KEY (`id`), ADD KEY `identifier` (`identifier`), ADD KEY `keyword` (`keyword`), ADD KEY `language` (`language`);") or die(mysqli_error($conn));
				$ins1 = mysqli_query($conn, "ALTER TABLE `pages` ADD FULLTEXT KEY `keyword_2` (`keyword`);") or die(mysqli_error($conn));
				$ins2 = mysqli_query($conn, "ALTER TABLE `pages` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;") or die(mysqli_error($conn));
			}
		}

	} else {
		
		echo "Please complete database, user and password.";
	}
	
	
}
?>