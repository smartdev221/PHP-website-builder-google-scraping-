<?php
include("../../config.php");
	$getconf = mysqli_query($conn, "SELECT name,value_ FROM `config`");
			while($getconfig = mysqli_fetch_object($getconf)){
				if($getconfig->name == "MAX_THREADS"){
					$MAX_THREADS = $getconfig->value_;
				}elseif($getconfig->name == "PAUSED"){
					$PAUSED = $getconfig->value_;
				}elseif($getconfig->name == "MAX_KEYWORDS_X_THREAD"){
					$MAX_KEYWORDS_X_THREAD = $getconfig->value_;
				}elseif($getconfig->name == "SLEEP_THREAD_BETWEEN_KEYWORDS"){
					$SLEEP_THREAD_BETWEEN_KEYWORDS = $getconfig->value_;
				}elseif($getconfig->name == "PROXY"){
					$PROXY = $getconfig->value_;
				}elseif($getconfig->name == "PROXY_IP_PORT"){
					$PROXY_IP_PORT = $getconfig->value_;
				}elseif($getconfig->name == "PROXY_USER_PASSWORD"){
					$PROXY_USER_PASSWORD = $getconfig->value_;
				}elseif($getconfig->name == "RUN_CRONJOBS"){
					$RUN_CRONJOBS = $getconfig->value_;
				}
			}
			if($RUN_CRONJOBS == 0){
				if(getenv('cron') == 1) {
					  echo "The script was run from the crontab entry";
					  die();
				} else {
				   echo "The script was run from a webserver, or something else";
				}
			}
			
			
	$get_templates = mysqli_query($conn, "SELECT * FROM `websites`");
	$templates = array();
	while($website = mysqli_fetch_object($get_templates)){
		for($i = 1; $i < 6; $i++){
			if(!empty($website->{'template'.$i})){
				$templates[$website->id][] = $website->{'template'.$i};
			}
			if(!empty($website->{'title'.$i})){
				$titles[$website->id][] = $website->{'title'.$i};
			}
			$serpt[$website->id] = $website->serptitle;
			$pref[$website->id] = $website->prefix;
			$wsnippets[$website->id] = $website->nosnippets;
			$ftp[$website->id] = $website->ftp;
			$ftpuser[$website->id] = $website->ftpuser;
			$ftppassword[$website->id] = $website->ftppassword;
			$wordpress[$website->id] = $website->wordpress_url;
			$wordpress_categories[$website->id] = $website->wordpress_categories;
			$mysql[$website->id] = array($website->db, $website->user, $website->pass);
			$preload[$website->id] = $website->preload;
			$total_built[$website->id] = $website->total_built;
		}
	}
	
		$box_shadows = array(1=>'box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;',
		2=>'box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;',
		3=>'box-shadow: rgba(67, 71, 85, 0.27) 0px 0px 0.25em, rgba(90, 125, 188, 0.05) 0px 0.25em 1em;',
		4=>'box-shadow: rgba(0, 0, 0, 0.18) 0px 2px 4px;');
	
function end_preload($ftp, $ftpuser, $ftppassword){

	$connect_it = ftp_connect($ftp);

	/* Login to FTP */
	$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
					
	ftp_chdir($connect_it, "public_html");
	if ( ftp_rename( $connect_it, ".htaccess_original", ".htaccess") ) {
		echo "preload removed";
	}
	ftp_close( $connect_it );

}

function add_post($url, $ptitle, $content, $user_id, $slug, $post_tags, $categories, $file){
	  $ch = curl_init();
	  $fields = array( 'pass'=> 'damnpassword123!@#', 'ptitle'=>$ptitle, 'content'=>$content, 'userid'=>$user_id, 'slug'=>$slug, 'tags'=>str_replace('-', ' ', $post_tags), 'categories'=>$categories, 'file'=>$file);
	  $postvars = '';
	  foreach($fields as $key=>$value) {
		$postvars .= $key . "=" . $value . "&";
	  }
	  curl_setopt($ch,CURLOPT_URL, $url);
	  curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
	  curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 3);
	  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	  $response = curl_exec($ch);
	  curl_close ($ch);	
	  
	  return $response;
}

function prefixes($ptitle, $conn){
	
	$prefix_found = 0;
	
	$select = mysqli_query($conn, "SELECT * FROM `config_global`") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
			if($row->name == "LOOK_FOR"){
				$look = explode("\r\n", $row->value);
				foreach($look as $pm){
					if(preg_match("/".$pm."/i", $ptitle)){
						$prefix_found = 1;
					}
				}
			}
			if($row->name == "PREFIX_LIST"){
				$prefixes = explode("\r\n", $row->value);
				shuffle($prefixes);
			}
		}
		if($prefix_found == 1){
			//return original
			return $ptitle;
		} else {
			//if blank prefix
			if($prefixes[0] == "<blank>"){
				return $ptitle;
			} else {
				//return prefix + original page title
				return $prefixes[0]." ".$ptitle;
			}
		}
	} else {
		//no prefixes found so original
		return $ptitle;
	}
}
function domainmatch($url){
	$info = parse_url($url);
	$host = $info['host'];
	$host_names = explode(".", $host);
	$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
	
	return $bottom_host_name;
}
function ignore_url($url, $conn){
	
	$blacklist_found = 0;
	
	$select = mysqli_query($conn, "SELECT * FROM `config_global` WHERE `name`='IMAGE_DOWNLOAD_IGNORE'") or die(mysqli_error($conn));
	if(mysqli_num_rows($select) > 0){
		while($row = mysqli_fetch_object($select)){
				$look = explode("\r\n", $row->value);
				foreach($look as $pm){
					if(preg_match("/".$pm."/i", domainmatch($url))){
						$blacklist_found = 1;
					}
				}
		}
		if($blacklist_found == 1){
			return "";
		} else {
			//if its not blacklisted
			return $url;
		}
	} else {
		//no settings found so return original
		return $url;
	}
}
function download($url, $name, $ftp, $ftpuser, $ftppassword, $website){

	$urlinfo = parse_url($url);
	
	//explode path to get image
	$imgname = explode("/", $urlinfo['path']);
	$imgname = array_reverse($imgname);
	//extracting image from path and looking for extension
	$extension = explode(".", $imgname[0]);
	$extension = array_reverse($extension);

	$ch = curl_init();
	$headers = array('Host: '.$urlinfo['host'],'Referer: '.$urlinfo['scheme'].'://'.$urlinfo['host'], 'User-Agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//$fp = fopen('progr/'.$name.'.png', 'wb');
	//curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch,  CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);

	$result = curl_exec ($ch); 
	
	if(!file_exists(__DIR__.'/images_website_'.$website.'/')){
		mkdir(__DIR__.'/images_website_'.$website.'/');
	}
	//check if image was downloaded
	if(!empty($result)){
		//file_put_contents($location.'/'.$name.'.'.$extension[0], $result);
		file_put_contents(__DIR__.'/images_website_'.$website.'/'.$name.'.'.$extension[0], $result);


		$connect_it = ftp_connect($ftp);

		/* Login to FTP */
		$login_result = ftp_login( $connect_it, $ftpuser, $ftppassword );
		$local_file = __DIR__.'/images_website_'.$website.'/'.$name.'.'.$extension[0];
		/* Send $local_file to FTP */
		if (!@ftp_chdir($connect_it, "public_html/posts")) {
			ftp_mkdir($connect_it, "public_html/posts"); //create dir
			$remote = "public_html/posts/";
		} else {
			$remote = "";
		}
		if ( ftp_put( $connect_it, $remote.$name.'.'.$extension[0], $local_file, FTP_BINARY ) ) {
			$filename = '/posts/'.$name.'.'.$extension[0];
			//echo "Successfull transfer ".$name.'.'.$extension[0]."\n";
		} else {
			//echo "There was a problem\n";
			$filename = "";
		}
		ftp_close( $connect_it );
	
	} else {
		$filename = '';
	}
	
	
	curl_close ($ch);
	
	return $filename;
}	
///check if it built under set number
		$website_check = mysqli_query($conn, "SELECT * FROM `websites` WHERE `built`<`dripfeed_current` order by rand() LIMIT 1");
		if(mysqli_num_rows($website_check) > 0){
			$website_id = mysqli_fetch_object($website_check);
			
	//$select = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1' and `images`='1' and `url_scraped`='1' and `translated_qa`='1' and `translated_snippet`='1' and `translated_serp`='1' and `translated_serp_title`='1' and `translated_headline`='1' and `built`='0' and `website`='".$website_id->id."' LIMIT 5");//1//3
	$select = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `idkeywords`='100579'");//1//3
	
	while($row = mysqli_fetch_object($select)){
		
			//logger($row->idkeywords, $row->original, 'Build start', $conn);
		$template = $templates[$row->website];
		$title = $titles[$row->website];
		shuffle($template);
		shuffle($title);
		
		$content = $template[0];
		$ptitle = $title[0];
		$ptitle = str_replace('%keyword%', $row->original, $ptitle);
		$ptitle = str_replace('%keywordinitialised%', ucwords($row->original), $ptitle);
		$ptitle = str_replace('%keywordinitialized%', ucwords($row->original), $ptitle);
		
		//serp content 
		$serpsel = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `idkeywords`='".$row->idkeywords."'");
		
		if(mysqli_num_rows($serpsel) > 0){
			$serp = mysqli_fetch_object($serpsel);
			$content_en = preg_replace_callback("/(<\/[A-z0-9]+>)/", function ($matches) {
				return strtolower($matches[0]);
			}, $serp->content_en);
			$content_en = preg_replace_callback("/(<[A-z0-9]+>)/", function ($matches) {
				return strtolower($matches[0]);
			}, $content_en);
			//replace h1 in serp to h2
			$content_en = preg_replace('/<h1>(.*)<\/h1>/sU', "<h2>$1</h2>", $content_en);
			//
			//$content = str_replace('%serpcontent%', "[serpcontent]%top% %middle% %qas% %images1%[/serpcontent]", $content);
			
			preg_match('/\[serpcontent\]([A-z0-9%\s]{1,})\[\/serpcontent\]/', $content, $matches);
			$paragraphs1 = array();
			//echo "MATCHES:";
			//print_r($matches);
			//echo "END matches<br>";
			if(count($matches) > 0){
				
				$paragraphs = explode("<p", $content_en);
				$paragraphsc = count($paragraphs);
				
				$tags = explode(" ", $matches[1]);
				$tagsc = count($tags);
				for($ip = 1; $ip<= ($paragraphsc - $tagsc); $ip++){
					shuffle($tags);
					$tags[] = "";
				}
				//echo "TAGS:";
				//print_r($tags);
				//echo "END Tags</br>";
				shuffle($tags);
				
				$it = 0;
				foreach($paragraphs as $par){
					if(!empty($tags[$it])){
						$paragraphs1[] = $par."<br>".$tags[$it]."<br>";
					} else {
						$paragraphs1[] = $par;
					}
					$it++;
				}
				//print_r($paragraphs);
				$content = str_replace($matches[0], "%serpcontent%", $content);
				$content_en = implode("<p", $paragraphs1);
				foreach($tags as $match){
					$content_en = str_replace("<p<br>".$match, $match, $content_en);
				}
			}
			
			$content = str_replace('%serpcontent%', $content_en, $content);
			$content = str_replace('%source%', $serp->source, $content);
			$serpcontent = $serp->content_en;
			
			//for homepage excerpt
			$forexcerpt = explode(" ", preg_replace('/<(?:\/)?p>/i', '', $serpcontent));
			$excerpt = "";
			for($ie = 0; $ie <= 99; $ie++){
				$excerpt.= $forexcerpt[$ie]." ";
				if($ie == 99){
					$excerpt.= "...";
				}
			}
			///
		} else {
			$content = str_replace('%serpcontent%', '', $content);
		}
		
		//headline text
		$headlinesel = mysqli_query($conn, "SELECT * FROM `headlines` WHERE `idkeywords`='".$row->idkeywords."'");
		
		if(mysqli_num_rows($headlinesel) > 0){
			$headline = mysqli_fetch_object($headlinesel);
			if(!empty($headline->content_en)){
				//$content = str_replace('%headline%', '<h1>'.$headline->content_en.'</h1>', $content);
				$content = str_replace('%headline%', $headline->content_en, $content);
				$ptitle = str_replace('%headline%', $headline->content_en, $ptitle);
			} else {
				$content = str_replace('%headline%', '', $content);
				$ptitle = str_replace('%headline%', '', $ptitle);
			}
		} else {
			$content = str_replace('%headline%', '', $content);
			$ptitle = str_replace('%headline%', '', $ptitle);
		}
		//end headline
		//add prefixes set to YES
		if($pref[$row->website] == 1){
			$ptitle = prefixes($ptitle, $conn);
		}
		//use serp titles instead of text boxes
		if($serpt[$row->website] == 1){
			$get_title = mysqli_query($conn, "SELECT * FROM `serp_titles` WHERE `idkeywords`='".$row->idkeywords."' and `translated`='1'");
			if(mysqli_num_rows($get_title) > 0){
				//use serp title if it has serp title, otherwise keep title as set above
				$title = mysqli_fetch_object($get_title);
				
				$ptitle = $title->title_en;
			}
		}
		//echo $content;
		//original keyword
			$content = str_replace('%keyword%', "<h1>".$row->original."</h1>", $content);
			/* //before with h1 tags
			$content = str_replace('%keywordinitialised%', "<h1>".ucwords($row->original)."</h1>", $content);
			$content = str_replace('%keywordinitialized%', "<h1>".ucwords($row->original)."</h1>", $content);*/
			$content = str_replace('%keywordinitialised%', ucwords($row->original), $content);
			$content = str_replace('%keywordinitialized%', ucwords($row->original), $content);
		//add %serptitle%
			$content = str_replace('%serptitle%', $ptitle, $content);
		//replace %date%
			//$content = str_replace('%date%', date('F Y'), $content);
		//intro text
		$introsel = mysqli_query($conn, "SELECT * FROM `intros` WHERE `idkeywords`='".$row->idkeywords."'");
		
		if(mysqli_num_rows($introsel) > 0){
			$intro = mysqli_fetch_object($introsel);
			if(!empty($intro->content_en)){
				$content = str_replace('%intro%', '<p>'.$intro->content_en.'</p>', $content);
				//set intro for %snippetswithintro%
				$introtext = $intro->content_en;
			} else {
				$content = str_replace('%intro%', '', $content);
				$introtext = "";
			}
			
		} else {
			$content = str_replace('%intro%', '', $content);
		}
		//end intro
		
		//videos 3 max
		for($i = 1; $i < 3; $i++){
			$embed = str_replace('watch?v=', 'embed/', $row->{vidresult.$i});
			if(!empty($embed)){
				$content = str_replace('%video'.$i.'%', '<iframe style="margin-top:20px; margin-bottom:20px; display: block; margin: 0 auto;" width="560" height="315" src="'.$embed.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $content);
			}
		}
		//images 20 max
		$images = array();
		for($i = 1; $i < 21; $i++){
			$check_blacklist = ignore_url($row->{'imgresult'.$i}, $conn);
				if(!empty($check_blacklist)){
					$images[] = $row->{'imgresult'.$i};
				}
		}
		$i = 1;
		foreach($images as $image){
			if($i == 1){
				//$filename = download($row->{imgresult.$i}, url_slug($row->original), "/home/".$ftpuser[$row->website]."/public_html/posts", $row->website);
				/*$filename = download($image, url_slug($row->original), $ftp[$row->website], $ftpuser[$row->website], $ftppassword[$row->website], $row->website);
				
				list($width, $height) = getimagesize(__DIR__.(str_replace('/posts', '/images_website_'.$row->website, $filename)));
				//echo $width." -> ".__DIR__.(str_replace('/posts', '/images_website_'.$row->website, $filename));
				
				if($width > 700){
					$content = str_replace('%image1%', '<img src="'.$filename.'" style="margin-top:20px; margin-bottom:20px; width: 50%; height: auto; display: block; margin: 0 auto;" alt="'.$row->original.'">', $content);
				} else {
					$content = str_replace('%image1%', '<img src="'.$filename.'" style="margin-top:20px; margin-bottom:20px; display: block; margin: 0 auto;" alt="'.$row->original.'">', $content);
				}*/
			} else {
				$content = str_replace('%image'.$i.'%', '<img src="'.$image.'" style="margin-top:20px; margin-bottom:20px; display: block; margin: 0 auto;" alt="'.$row->original.'">', $content);
			}
			$i++;
		}
		//keyword top 16 max
		for($i = 1; $i < 17; $i++){
			$content = str_replace('%imagekeyword'.$i.'%', $row->{keywordtop.$i}, $content);
		}
		//google serp
		for($i = 1; $i < 11; $i++){
			$content = str_replace('%result'.$i.'%', $row->{result.$i}, $content);
		}
		//related keywords 10 max
		for($i = 1; $i < 11; $i++){
			$content = str_replace('%related'.$i.'%', $row->{related.$i}, $content);
		}
						
		//q&a's
		$toc = array();
		$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#updd">%date% Update</a></li>';
		//get h's from serpcontent
		preg_match_all('/<h([2-3])>(.*)<\/h[2-3]>/sU', $content, $hs);
		if(count($hs[0]) > 0){
				for($i =0; $i<count($hs[0]); $i++){
					if(strlen($hs[2][$i]) > 2){
						$content = str_replace("<h".$hs[1][$i].">".$hs[2][$i]."</h".$hs[1][$i].">", "<h".$hs[1][$i]." id=\"".($i+10)."\">".ucwords($hs[2][$i])."</h".$hs[1][$i].">", $content);
						$subheadings[$hs[2][$i]] = "<h".$hs[1][$i]." id=\"".($i+10)."\">".ucwords($hs[2][$i])."</h".$hs[1][$i].">";
						//$toc[]= '<li><a href="#'.($i+10).'">'.$hs[2][$i].'</a></li>';
						$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#'.($i+10).'">'.ucwords($hs[2][$i]).'</a></li>';
					}
				}
		}
		///
		//print_r($subheadings);
		
		$qasel = mysqli_query($conn, "SELECT * FROM `qa` WHERE `original`='".mysqli_real_escape_string($conn, $row->original)."' LIMIT 10");
		$qas = array();
		$i = 1;
		if(mysqli_num_rows($qasel) > 0){
			while($qa = mysqli_fetch_object($qasel)){
				if(preg_match('/qa'.$i.'/', $content)){
					$inserted = 0;
					foreach($subheadings as $subheading=>$html){
						$similarity = similar_text($qa->question, $subheading, $percent);
						echo "<br>";
						echo 'characters:'.$similarity.' -> '.$percent."% match || ".$qa->question."|-|".$subheading." <br>";
						if($percent > 30 && $inserted == 0){
							//echo "inserted";
							$content = str_replace('%qa'.$i.'%', '', $content);
							$content = str_replace($html, $html."%qa".$i."%", $content);
							$inserted = 1;
						}
						unset($subheadings[$subheading]);
					}
					//$toc[]= '<li><a href="#'.$i.'" title="'.$qa->question.'">'.$qa->question.'</a></li>';
					$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#'.$i.'" title="'.$qa->question.'">'.$qa->question.'</a></li>';
				}
				$exclude_paa_title = 0;
				if($exclude_paa_title == 1){
					$content = str_replace('%qa'.$i.'%', "<div style=\"".$box_shadows[rand(1,4)]."padding:20px 10px 20px 10px;\"><p id=\"".$i."\">".$qa->answer_en."</p></div><br>", $content);
					//put all questions and answers
					$qas[] = "<div style=\"".$box_shadows[rand(1,4)]."padding:20px 10px 20px 10px;\"><p id=\"".$i."\">".$qa->answer_en."</p></div><br>";
				} else {
					$content = str_replace('%qa'.$i.'%', "<div style=\"".$box_shadows[rand(1,4)]."padding:20px 10px 20px 10px;\"><p><h2 id=\"".$i."\">".$qa->question."</h2>".$qa->answer_en."</p></div><br>", $content);
					//put all questions and answers
					$qas[] = "<div style=\"".$box_shadows[rand(1,4)]."padding:20px 10px 20px 10px;\"><p><h2 id=\"".$i."\">".$qa->question."</h2>".$qa->answer_en."</p></div><br>";
				}
				
				$i++;
			}
		}
			$content = str_replace('%qas%', implode(" ", $qas), $content);
		//featuredsnippets
		$fnsel = mysqli_query($conn, "SELECT * FROM `featuredsnippets` WHERE `original`='".mysqli_real_escape_string($conn, $row->original)."' LIMIT 10");
		$snippets = array();
		$i = 0;
		$nosnippets = 0;
		//check for %snippetswithintro%
		$addintro = 0;
		if(preg_match('/%snippetswithintro%/', $content)){
			$addintro = 1;
		}
		if(mysqli_num_rows($fnsel) > 0){
			while($snippet = mysqli_fetch_object($fnsel)){
				$content = str_replace('%snippet'.$i.'%', "<p>".$snippet->snippetcontent_en."</p>", $content);
				
				if(!empty($snippet->snippettitle)){
					$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#'.($i+30).'" title="'.$snippet->snippettitle.'">'.$snippet->snippettitle.'</a></li>';
				}
				//put all snippets
				if($i == 0){
					//if %snippetswithintro% add introtext to first snippet
					if($addintro == 1){
						$snippets[] = "<p id=\"".($i+30)."\">".$introtext." ".$snippet->snippetcontent_en."</p>";
					} else {
						$snippets[] = "<p id=\"".($i+30)."\">".$snippet->snippetcontent_en."</p>";
					}
				} else {
					$snippets[] = "<p id=\"".($i+30)."\">".$snippet->snippetcontent_en."</p>";
				}
				$i++;
			}
		} else {
			$nosnippets = 1;
		}
		if(count($snippets) > 0){
			$content = str_replace('%snippetswithintro%', implode(" ", $snippets), $content);
		} else {
			$content = str_replace('%snippetswithintro%', "<p>".$introtext."</p>", $content);
		}
			$content = str_replace('%snippets%', implode(" ", $snippets), $content);
		//toc
		if(count($toc) > 0){
			/*$content = str_replace('%toc%', '<table id="toc">
		  <tbody>
				<tr><td>
					<div id="toctitle"><h2>Contents</h2></div>
					<ul>
						'.implode("\n", $toc).'     
					</ul>
					</td>
				</tr>
		  </tbody>
		  </table>', $content);
		  */
		  if(empty($wordpress[$row->website])){
		  $content = str_replace('%toc%', '<div id="ez-toc-container" class="ez-toc-v2_0_4 counter-hierarchy counter-decimal ez-toc-grey">
			<div class="ez-toc-title-container">
			<p class="ez-toc-title">Contents</p>
			<span class="ez-toc-title-toggle">
			<a class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle"><i class="ez-toc-glyphicon ez-toc-icon-toggle"></i></a>
			</span>
			</div>
			<nav>
			<ul class="ez-toc-list ez-toc-list-level-1">
									'.implode("\n", $toc).'     
			</ul>
			</nav>
			</div>', $content);
		  }
		}
		$content = str_replace('%toc%', '', $content);
		//posted by 
		$posted = "";
		$biobox = "";
		$authorssel = mysqli_query($conn, "SELECT * FROM `authors` WHERE `website`='".$row->website."' order by RAND() LIMIT 1");
		if(mysqli_num_rows($authorssel) > 0){
			$author = mysqli_fetch_object($authorssel);
			
			$posted = '<span style="font-size: 85%">'.date("F d, Y").' by '.$author->name.'</span>';
			$authorid = $author->id;
			$authorid_wordpress = $author->wordpress_user;
			$biobox = '<div class="saboxplugin-wrap" itemtype="http://schema.org/Person" itemscope="" itemprop="author"><div class="saboxplugin-gravatar"><img src="/images/'.url_slug($author->name).'.'.$author->image_extension.'" width="100" height="100" alt="'.$author->name.'" class="avatar avatar-100 wp-user-avatar wp-user-avatar-100 alignnone photo"></div><div class="saboxplugin-authorname"><span class="fn" itemprop="name">'.$author->name.'</span></div><div class="saboxplugin-desc"><div itemprop="description"></div></div><div class="clearfix"></div></div>';
		}
		if(!empty($wordpress[$row->website])){
			$content = str_replace('%posted%', '', $content);
			$content = str_replace('%biobox%', '', $content);
			$content = str_replace('%top%', '[includeme file="topfix.php"]', $content);
			$content = str_replace('%middle%', '[includeme file="middlefix.php"]', $content);
			$content = str_replace('%bottom%', '[includeme file="bottomfix.php"]', $content);
		} else {
			$content = str_replace('%posted%', $posted, $content);
			$content = str_replace('%biobox%', $biobox, $content);
		}
		
		
		
	  //clear the tokens that were not fulfilled
		for($i = 1; $i < 21; $i++){
			$content = str_replace('%image'.$i.'%', '', $content);
			$content = str_replace('%video'.$i.'%', '', $content);
			$content = str_replace('%imagekeyword'.$i.'%', '', $content);
			$content = str_replace('%result'.$i.'%', '', $content);
			$content = str_replace('%related'.$i.'%', '', $content);
			$content = str_replace('%qa'.$i.'%', '', $content);
			$content = str_replace('%snippet'.$i.'%', '', $content);
			$content = str_replace('%qas%', '', $content);
			$content = str_replace('%intro%', '', $content);
			$content = str_replace('%headline%', '', $content);
			$content = str_replace('%snippets%', '', $content);
			$content = str_replace('%snippetswithintro%', '', $content);
		}
		//replace bad characters ? 
		$content = str_replace('�', ' ', $content);
		//replace hex encoded e.g. \x27
		$content = preg_replace_callback(
		  "(\\\\x([0-9a-f]{2}))i",
		  function($a) {return chr(hexdec($a[1]));},
		  $content
		);
		//
		//echo $content;
		if($wsnippets[$row->website] == 0 && $nosnippets == 1){
			echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: No snippets.<br>";
			//mysqli_query($conn, "UPDATE `keywords` SET `built`='3',`reason`='No snippets.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
			//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
			//logger($row->idkeywords, $row->original, 'Build failed: no snippets', $conn);
		} else {
		if(count(explode(" ", $serpcontent)) > 201){
			echo $content;
			if(empty($filename)) {
				echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: There was an error with image download/ftp transfer.<br>";
				//mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='There was an error with image download/ftp transfer.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
				//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
				//logger($row->idkeywords, $row->original, 'Build failed: error image download/ftp transfer', $conn);
			} else {
				//if website is not WORDPRESS
				if(empty($wordpress[$row->website])){
					//connect to remote database
					$conn1 = mysqli_connect("localhost", htmlentities($mysql[$row->website][1]), htmlentities($mysql[$row->website][2]), htmlentities($mysql[$row->website][0]));
					if($conn1 === false){
						//if failed to connect
						echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: ERROR: Could not connect to remote database.<br>";
						//mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='ERROR: Could not connect to remote database.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
						//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
						//logger($row->idkeywords, $row->original, 'Build failed: can\'t connect to remote db', $conn);
					} else {
						//on successfull remote connection
						
						$exists = mysqli_query($conn1, "SELECT * FROM `pages` WHERE `identifier`='".url_slug($row->original)."'");
						//check if post exists
						if(mysqli_num_rows($exists) < 1){
							//insert into remote
							//$ins = mysqli_query($conn1, "INSERT INTO `pages`(`idkeywords`,`keyword`,`identifier`,`author`,`title`,`html`,`excerpt`,`keywordtop1`,`keywordtop2`,`keywordtop3`,`keywordtop4`,`keywordtop5`,`keywordtop6`,`keywordtop7`,`keywordtop8`,`keywordtop9`,`keywordtop10`,`keywordtop11`,`keywordtop12`,`keywordtop13`,`keywordtop14`,`keywordtop15`,`keywordtop16`) VALUES('".$row->idkeywords."','".$row->original."', '".url_slug($row->original)."', '".$authorid."','".mysqli_real_escape_string($conn1, $ptitle)."', '".mysqli_real_escape_string($conn1, $content)."', '".mysqli_real_escape_string($conn1, $excerpt)."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop1))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop2))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop3))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop4))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop5))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop6))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop7))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop8))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop9))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop10))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop11))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop12))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop13))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop14))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop15))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop16))."')");
							if($ins){
								if($total_built[$website_id] >= $preload[$website_id] && $preload[$website_id] != 0){
									end_preload($ftp[$website_id], $ftpuser[$website_id], $ftppassword[$website_id]);
									mysqli_query($this->link, "UPDATE `websites` SET `preload`='0' WHERE `id`='".$row->website."'");
								}
								echo "KEYWORD ID: ".$row->idkeywords." SUCCESS.<br>";
								//if success to remote -> insert into built
								//mysqli_query($conn, "INSERT INTO `built`(`idkeywords`,`website`,`author`,`title`,`content`,`excerpt`,`keywordtop1`,`keywordtop2`,`keywordtop3`,`keywordtop4`,`keywordtop5`,`keywordtop6`,`keywordtop7`,`keywordtop8`,`keywordtop9`,`keywordtop10`,`keywordtop11`,`keywordtop12`,`keywordtop13`,`keywordtop14`,`keywordtop15`,`keywordtop16`) VALUES('".$row->idkeywords."', '".$row->website."', '".$authorid."', '".mysqli_real_escape_string($conn, $ptitle)."', '".mysqli_real_escape_string($conn, $content)."', '".mysqli_real_escape_string($conn, $excerpt)."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop1))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop2))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop3))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop4))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop5))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop6))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop7))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop8))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop9))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop10))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop11))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop12))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop13))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop14))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop15))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop16))."')");
								//mysqli_query($conn, "UPDATE `keywords` SET `built`='1',`reason`='',`last_action`='builder success', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
								//mysqli_query($conn, "UPDATE `websites` SET `built`=`built`+1,`total_built`=`total_built`+1 WHERE `id`='".$row->website."'");
								//logger($row->idkeywords, $row->original, 'Build success.', $conn);
							} else {
								//if fail to remote
								echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: Failed to add post to remote database. It doesn\'t exist, error at inserting.<br>";
								//mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Failed to add post to remote database. It doesn\'t exist, error at inserting.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
								//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
								//logger($row->idkeywords, $row->original, 'Build failed: can\'t add to remote db, it doesn\'t exist there', $conn);
							}
						} else {
							//if exists at remote
							echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: Post already exists into remote table.<br>";
							//mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Post already exists into remote table.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
							//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
							//logger($row->idkeywords, $row->original, 'Build failed: it exists into remote db', $conn);
						}
						
						//close connection
						mysqli_close($conn1);
					}
				
				} else {
					//if website is wordpress
					$post_tags = url_slug($row->keywordtop1).','.url_slug($row->keywordtop2).','.url_slug($row->keywordtop3).','.url_slug($row->keywordtop4).','.url_slug($row->keywordtop5).','.url_slug($row->keywordtop6).','.url_slug($row->keywordtop7).','.url_slug($row->keywordtop8).','.url_slug($row->keywordtop9).','.url_slug($row->keywordtop10).','.url_slug($row->keywordtop11).','.url_slug($row->keywordtop12).','.url_slug($row->keywordtop13).','.url_slug($row->keywordtop14).','.url_slug($row->keywordtop15).','.url_slug($row->keywordtop16);
					//$add = add_post($wordpress[$row->website].'/wp-content/add_posts.php', $ptitle, mysqli_real_escape_string($conn, $content), $authorid_wordpress, url_slug($row->original), $post_tags, $wordpress_categories[$row->website], $filename);
					if($add == '1'){
							echo "KEYWORD ID: ".$row->idkeywords." SUCCESS.<br>";
							//if success to remote -> insert into built
							//mysqli_query($conn, "INSERT INTO `built`(`idkeywords`,`website`,`author`,`title`,`content`,`excerpt`,`keywordtop1`,`keywordtop2`,`keywordtop3`,`keywordtop4`,`keywordtop5`,`keywordtop6`,`keywordtop7`,`keywordtop8`,`keywordtop9`,`keywordtop10`,`keywordtop11`,`keywordtop12`,`keywordtop13`,`keywordtop14`,`keywordtop15`,`keywordtop16`) VALUES('".$row->idkeywords."', '".$row->website."', '".$authorid."', '".mysqli_real_escape_string($conn, $ptitle)."', '".mysqli_real_escape_string($conn, $content)."', '".mysqli_real_escape_string($conn, $excerpt)."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop1))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop2))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop3))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop4))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop5))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop6))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop7))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop8))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop9))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop10))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop11))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop12))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop13))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop14))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop15))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop16))."')");
							//mysqli_query($conn, "UPDATE `keywords` SET `built`='1',`reason`='',`last_action`='builder success', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
							//mysqli_query($conn, "UPDATE `websites` SET `built`=`built`+1 WHERE `id`='".$row->website."'");
							//logger($row->idkeywords, $row->original, 'Build success wordpress.', $conn);
						} else {
							//if fail to remote
							echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: ".$add."<br>";
							//mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Failed to add post to wordpress.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
							//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
							//logger($row->idkeywords, $row->original, 'Build failed wordpress: '.$add, $conn);
						}					
				}
			}
		} else {
			echo "KEYWORD ID: ".$row->idkeywords." FAILED WITH STATUS: Serp content under 200 words.<br>";
			//mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Serp content under 200 words.',`last_action`='builder', `last_action_date`='".date("Y-m-d H:i:s")."' WHERE `idkeywords`='".$row->idkeywords."'");
			//mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
			//logger($row->idkeywords, $row->original, 'Build failed: serp content under 200 words', $conn);
		}
		}
		}
	}
	
	
?>