<?php

$urlinfo = parse_url("https://support.content.office.net/en-us/media/2fa69e49.2c73.4a25.b010.47dd834b581f.png");

print_r($urlinfo);

$imgname = explode("/", $urlinfo['path']);
$imgname = array_reverse($imgname);
$extension = explode(".", $imgname[0]);
$extension = array_reverse($extension);

print_r($imgname);
print_r($extension);

?>