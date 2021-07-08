<?php
session_start();
include("../config.php");

set_time_limit(0);

function url_slug($str, $options = array()) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
	
	$defaults = array(
		'delimiter' => '-',
		'limit' => 150,
		'lowercase' => false,
		'replacements' => array(),
		'transliterate' => false,
	);
	
	// Merge options
	$options = array_merge($defaults, $options);
	
	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
		'ß' => 'ss', 
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
		'ÿ' => 'y',
		// Latin symbols
		'©' => '(c)',
		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',
		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
		'Ž' => 'Z', 
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z', 
		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
		'Ż' => 'Z', 
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',
		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);
	
	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
	
	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}
	
	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
	
	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
	
	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
	
	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);
	
	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}



if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">


      <!-- Charts -->



    <link type="text/css" href="assets/css/custom-theme/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">











    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

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

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#"><img src="assets/img/logo.png" alt=""/></a>

          <div class="nav-collapse">

          <ul class="nav pull-right">

  <li class="dropdown">
    <a href="pages.htm" class="dropdown-toggle" data-toggle="dropdown">
      <span style="padding-right:10px; width:30px;"><img src="assets/img/user_thumb.jpg" style="width:30px;" alt=""/></span>Mr <?php echo $_SESSION['user']; ?>
      <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      <li>
        <a href="my.php"><i style="font-size:14px; padding-top:3px; padding-right:5px;" class="icon-user"></i>My Account</a>
      </li>
	  <li>
        <a href="settings.php"><i style="font-size:14px; padding-top:3px; padding-right:5px;" class="icon-cogs"></i>Settings</a>
      </li>
    </ul>
  </li>
       <li>
     <div class="btn-group" style="margin-top:7px;"><a href="logout.php" class="medium twitter button radius" style="text-decoration:none;"><i style="font-size:16px; padding-top:3px; padding-right:5px;" class="icon-off"></i>Leap out</a> </div>
      </li>
</ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
      <div class="subnav subnav-fixed">
  <ul class="nav nav-pills">


	<li class="active">
      <a href="index.php">
  <i style="margin-top:7px;" class="icon-dashboard icon-large"></i>Dashboard</a>
    </li>
    <li>
      <a href="topics.php"><i style="margin-top:7px;" class="icon-th icon-large"></i>Topics</a>
    </li>
	<li>
      <a href="urls.php"><i style="margin-top:7px;" class=" icon-file icon-large"></i>Urls</a>
    </li>
	<li>
      <a href="settings.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Settings</a>
    </li>
	<li>
      <a href="boxsettings.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Box Settings</a>
    </li>
	<li>
      <a href="tester.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Tester</a>
    </li>
	<li>
      <a href="export.php"><i style="margin-top:7px;" class="icon-cogs icon-large"></i>Export</a>
    </li>
     <li>

  <form style="padding:5px;" class="navbar-search pull-left" action="topics1.php">
            <input type="text" name="search" class="search-query span3" placeholder="Search a topic">
          </form>

    </li>
  </ul>

</div>




<div class="container" style="margin-top:30px;">

<div class="row">


  <div class="span6"><div class="widget_heading"><h4>Sitemap Creation</h4></div><div class="widget_container">
  <div class="control-group">
  <?php
 if(!empty($_POST['sitemap'])){
		
		if(is_numeric($_POST['sitemap'])){
			$outputsitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$outputsitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			
		
		for($i = 1; $i <= $_POST['sitemap']; $i++){
$outputsitemap .='  <sitemap>
    <loc>http://wikipost.org/sitemaps/sitemap'.$i.'.xml</loc>
  </sitemap>' . "\n";			
			
			
			$no = $i-1;
			$no1 = $no * 48000;
			
			$select = mysql_query("SELECT `id1`,`title` FROM `topics` WHERE `published`='1' LIMIT ".$no1.",48000");
			
			  $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			  $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			  
			  while($row = mysql_fetch_object($select)){
				  
$output .='    <url>
        <loc>http://wikipost.org/topic/'.$row->id1."/".url_slug($row->title).'.html</loc>
		<changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>'."\n";  
				  
			  }
			  $output .='</urlset>';
			
			$filename = "../sitemaps/sitemap".$i.".xml";
			
			$filehandle = fopen($filename,"w") or die("Can't open the file");
			fwrite($filehandle,$output);
			fclose($filehandle);
			

			
		}
$outputsitemap .='</sitemapindex>';
			$filename = "../sitemap.xml";
			
			$filehandle = fopen($filename,"w") or die("Can't open the file");
			fwrite($filehandle,$outputsitemap);
			fclose($filehandle);
			
		echo '<font color="green" size="3">All Done. Sitemap url: <a href="../sitemap.xml">Here</a></font><br><br>';
		} else {
			echo '<font color="red" size="3">Not a valid number.</font>';
		}
		
  }
  $select = mysql_query("SELECT `published` FROM topics WHERE published='1'") or die(mysql_error());

	$topics = mysql_num_rows($select);
	$number = number_format($topics/48000, 2);
	
	echo "<font color=\"green\" size=\"3\">".$topics." topics published - you can create ".$number." sitemaps</font><br><br>";
	echo "<font color=\"red\" size=\"3\">Please allow up to 15 seconds/sitemap, about ".($number*15)." seconds</font><br><br>";
?>
  <form action="#" method="post">

  <label class="control-label" for="input01">How many sitemaps you want to create(48k for each sitemap)?</label>
  <div class="controls">
    <input type="text" name="sitemap" value="<?php echo round($number, 0, PHP_ROUND_HALF_UP); ?>" class="input-xlarge span5" >

</div>

       <div class="insert-actions">

 <div class="btn-toolbar">

<p class="pull-right">
<button class="btn btn-medium btn-primary">Create</button></p>

</div>
</div>
</form>

</div>
</div>
</div>


</div></div>



        <!--scripts-->

            <!--jQuery References-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>

<!--Theme-->
<link href="http://cdn.wijmo.com/themes/aristo/jquery-wijmo.css" rel="stylesheet" type="text/css" title="rocket-jqueryui" />

<!--Wijmo Widgets CSS-->
<link href="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.1.4.min.css" rel="stylesheet" type="text/css" />

<!--Wijmo Widgets JavaScript-->
<script src="http://cdn.wijmo.com/jquery.wijmo-open.all.2.1.4.min.js" type="text/javascript"></script>
<script src="http://cdn.wijmo.com/jquery.wijmo-complete.all.2.1.4.min.js" type="text/javascript"></script>

 <script src="assets/js/scriptdash.js" type="text/javascript"></script>
 <script type="text/javascript" src="assets/js/bootstrap.js"></script>



  </body>
</html>
<?php
}
?>