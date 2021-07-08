<?php
include("../config.php");

function remove($content){
	$content = preg_replace('/<i class="[A-z-_+\s]+"><\/i>/', "", $content);
	$content = preg_replace('/ class(?:\s)?=(?:\s)?"[A-z0-9-_+\s]+"/', "", $content);
	$content = preg_replace('/ style(?:\s)?=(?:\s)?"[A-z0-9-_+\s:;#.,\(\)]+"/sU', "", $content);
	$content = preg_replace('/ align="[A-z]+"/', "", $content);
	$content = preg_replace('/ id="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ lang="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ width="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ height="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ alt="[A-z0-9-_+\s]+"/sU', "", $content);
	$content = preg_replace('/ title=".*"/sU', "", $content);
	$content = preg_replace('/ onclick(?:\s)?=(?:\s)?"[A-z0-9-_+\s:;.\(\)=\',]+"/', "", $content);
	$content = preg_replace('/ data-[a-z-]+=[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s]+/', "", $content);	
	$content = preg_replace('/<textarea.*>(.*)<\/textarea>/sU', "$1", $content);
	$content = preg_replace('/<time.*<\/time>/sU', "", $content);
	$content = preg_replace('/<script>[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s]+<\/script>/', "", $content);	
	$content = preg_replace('/<svg[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s\>\<]+<\/svg>/', "", $content);
		//bad stopping point
	//$content = preg_replace('/<svg[A-z0-9-_+\{\}\(\)\"\:\;,\.\/\?\'\=\s\>\<\#%]+<\/svg>/', "", $content);	
	$content = preg_replace('/<p\s+>/', "<p>", $content);		
	$content = preg_replace('/\sdir="\w+"/', "", $content);
	$content = preg_replace('/<span(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/span>/', "", $content);
	//remove bold, italic, links and images
	$content = preg_replace('/<b(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/b>/', "", $content);
	$content = preg_replace('/<i(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/i>/', "", $content);
	$content = preg_replace('/<em(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/em>/', "", $content);
	$content = preg_replace('/<strong(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/strong>/', "", $content);
	$content = preg_replace('/<figure(?:\s+)?>/', "", $content);	
	$content = preg_replace('/<\/figure>/', "", $content);
	$content = preg_replace('/\sdir="\w+"/', "", $content);
	$content = preg_replace('/\starget(?:\s)?=(?:\s)?"_\w+"/', "", $content);
	$content = preg_replace('/\srel(?:\s)?=(?:\s)?"[\w\s]+"/', "", $content);
	$content = preg_replace('/<a\s+href(?:\s)?=(?:\s)?"([A-z0-9:\/.,\-_&;\?=!#()%]+)"(?:.+)?>(.+)<\/a>/U', "$2", $content);	
	$content = preg_replace('/<img\s+src(?:\s)?=(?:\s)?"([A-z0-9:\/.,\-_&;\?=!#()%]+)"(?:.+)?>/U', "", $content);
	
	return $content;
}


$select = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` where `id`='2516'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
			
			echo remove($row->content);
		}
	}
	

?>