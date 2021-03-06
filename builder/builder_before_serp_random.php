<?php
include("../config.php");

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
			$mysql[$website->id] = array($website->db, $website->user, $website->pass);
		}
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
		$website_check = mysqli_query($conn, "SELECT * FROM `websites` WHERE `built`<`dripfeed_current` LIMIT 1");
		if(mysqli_num_rows($website_check) > 0){
			$website_id = mysqli_fetch_object($website_check);
			
	$select = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `scraped`='1' and `images`='1' and `url_scraped`='1' and `translated_qa`='1' and `translated_snippet`='1' and `translated_serp`='1' and `translated_serp_title`='1' and `built`='0' and `website`='".$website_id->id."' LIMIT 1");//3
	
	while($row = mysqli_fetch_object($select)){
		
			
		$template = $templates[$row->website];
		$title = $titles[$row->website];
		shuffle($template);
		shuffle($title);
		
		$content = $template[0];
		$ptitle = $title[0];
		$ptitle = str_replace('%keyword%', $row->original, $ptitle);
		$ptitle = str_replace('%keywordinitialised%', ucwords($row->original), $ptitle);
		$ptitle = str_replace('%keywordinitialized%', ucwords($row->original), $ptitle);
		
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
				$content = str_replace('%video'.$i.'%', '<iframe width="560" height="315" src="'.$embed.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $content);
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
				$filename = download($image, url_slug($row->original), $ftp[$row->website], $ftpuser[$row->website], $ftppassword[$row->website], $row->website);
				
				list($width, $height) = getimagesize(__DIR__.(str_replace('/posts', '/images_website_'.$row->website, $filename)));
				//echo $width." -> ".__DIR__.(str_replace('/posts', '/images_website_'.$row->website, $filename));
				
				if($width > 700){
					$content = str_replace('%image1%', '<img src="'.$filename.'" width="50%" alt="'.$row->original.'">', $content);
				} else {
					$content = str_replace('%image1%', '<img src="'.$filename.'" alt="'.$row->original.'">', $content);
				}
			} else {
				$content = str_replace('%image'.$i.'%', '<img src="'.$image.'" alt="'.$row->original.'">', $content);
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
		
		//q&a's
		$toc = array();
		$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#updd">%date% Update</a></li>';
		//get h's from serpcontent
		preg_match_all('/<h([2-3])>(.*)<\/h[2-3]>/sU', $content, $hs);
		if(count($hs[0]) > 0){
				for($i =0; $i<count($hs[0]); $i++){
					if(strlen($hs[2][$i]) > 2){
						$content = str_replace("<h".$hs[1][$i].">".$hs[2][$i]."</h".$hs[1][$i].">", "<h".$hs[1][$i]." id=\"".($i+10)."\">".ucwords($hs[2][$i])."</h".$hs[1][$i].">", $content);
						//$toc[]= '<li><a href="#'.($i+10).'">'.$hs[2][$i].'</a></li>';
						$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#'.($i+10).'">'.ucwords($hs[2][$i]).'</a></li>';
					}
				}
		}
		///
		
		
		$qasel = mysqli_query($conn, "SELECT * FROM `qa` WHERE `original`='".mysqli_real_escape_string($conn, $row->original)."' LIMIT 10");
		$qas = array();
		$i = 0;
		if(mysqli_num_rows($qasel) > 0){
			while($qa = mysqli_fetch_object($qasel)){
				if(preg_match('/qa'.$i.'/', $content)){
					//$toc[]= '<li><a href="#'.$i.'" title="'.$qa->question.'">'.$qa->question.'</a></li>';
					$toc[]= '<li class="ez-toc-page-1 ez-toc-heading-level-2"><a class="ez-toc-link ez-toc-heading-1" href="#'.$i.'" title="'.$qa->question.'">'.$qa->question.'</a></li>';
				}
				$content = str_replace('%qa'.$i.'%', "<p><h2 id=\"".$i."\">".$qa->question."</h2>".$qa->answer_en."</p>", $content);
				//put all questions and answers
				$qas[] = "<p><h2 id=\"".$i."\">".$qa->question."</h2>".$qa->answer_en."</p>";
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
		$content = str_replace('%toc%', '', $content);
		//posted by 
		$posted = "";
		$biobox = "";
		$authorssel = mysqli_query($conn, "SELECT * FROM `authors` WHERE `website`='".$row->website."' order by RAND() LIMIT 1");
		if(mysqli_num_rows($authorssel) > 0){
			$author = mysqli_fetch_object($authorssel);
			
			$posted = '<span style="font-size: 85%">'.date("F d, Y").' by '.$author->name.'</span>';
			$authorid = $author->id;
			$biobox = '<div class="saboxplugin-wrap" itemtype="http://schema.org/Person" itemscope="" itemprop="author"><div class="saboxplugin-gravatar"><img src="/images/'.url_slug($author->name).'.'.$author->image_extension.'" width="100" height="100" alt="'.$author->name.'" class="avatar avatar-100 wp-user-avatar wp-user-avatar-100 alignnone photo"></div><div class="saboxplugin-authorname"><span class="fn" itemprop="name">'.$author->name.'</span></div><div class="saboxplugin-desc"><div itemprop="description"></div></div><div class="clearfix"></div></div>';
		}
		
		$content = str_replace('%posted%', $posted, $content);
		$content = str_replace('%biobox%', $biobox, $content);
		
		
		
		
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
		$content = str_replace('???', ' ', $content);
		//replace hex encoded e.g. \x27
		$content = preg_replace_callback(
		  "(\\\\x([0-9a-f]{2}))i",
		  function($a) {return chr(hexdec($a[1]));},
		  $content
		);
		//
		//echo $content;
		if($wsnippets[$row->website] == 0 && $nosnippets == 1){
			mysqli_query($conn, "UPDATE `keywords` SET `built`='3',`reason`='No snippets.' WHERE `idkeywords`='".$row->idkeywords."'");
			mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
		} else {
		if(count(explode(" ", $serpcontent)) > 201){
			if(empty($filename)) {
				mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='There was an error with image download/ftp transfer.' WHERE `idkeywords`='".$row->idkeywords."'");
				mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
			} else {
				//connect to remote database
				$conn1 = mysqli_connect("localhost", htmlentities($mysql[$row->website][1]), htmlentities($mysql[$row->website][2]), htmlentities($mysql[$row->website][0]));
				if($conn1 === false){
					//if failed to connect
					mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='ERROR: Could not connect to remote database.' WHERE `idkeywords`='".$row->idkeywords."'");
					mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
				} else {
					//on successfull remote connection
					
					$exists = mysqli_query($conn1, "SELECT * FROM `pages` WHERE `identifier`='".url_slug($row->original)."'");
					//check if post exists
					if(mysqli_num_rows($exists) < 1){
						//insert into remote
						$ins = mysqli_query($conn1, "INSERT INTO `pages`(`idkeywords`,`keyword`,`identifier`,`author`,`title`,`html`,`excerpt`,`keywordtop1`,`keywordtop2`,`keywordtop3`,`keywordtop4`,`keywordtop5`,`keywordtop6`,`keywordtop7`,`keywordtop8`,`keywordtop9`,`keywordtop10`,`keywordtop11`,`keywordtop12`,`keywordtop13`,`keywordtop14`,`keywordtop15`,`keywordtop16`) VALUES('".$row->idkeywords."','".$row->original."', '".url_slug($row->original)."', '".$authorid."','".mysqli_real_escape_string($conn1, $ptitle)."', '".mysqli_real_escape_string($conn1, $content)."', '".mysqli_real_escape_string($conn1, $excerpt)."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop1))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop2))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop3))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop4))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop5))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop6))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop7))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop8))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop9))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop10))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop11))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop12))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop13))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop14))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop15))."', '".mysqli_real_escape_string($conn1, url_slug($row->keywordtop16))."')");
						if($ins){
							//if success to remote -> insert into built
							mysqli_query($conn, "INSERT INTO `built`(`idkeywords`,`website`,`author`,`title`,`content`,`excerpt`,`keywordtop1`,`keywordtop2`,`keywordtop3`,`keywordtop4`,`keywordtop5`,`keywordtop6`,`keywordtop7`,`keywordtop8`,`keywordtop9`,`keywordtop10`,`keywordtop11`,`keywordtop12`,`keywordtop13`,`keywordtop14`,`keywordtop15`,`keywordtop16`) VALUES('".$row->idkeywords."', '".$row->website."', '".$authorid."', '".mysqli_real_escape_string($conn, $ptitle)."', '".mysqli_real_escape_string($conn, $content)."', '".mysqli_real_escape_string($conn, $excerpt)."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop1))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop2))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop3))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop4))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop5))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop6))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop7))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop8))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop9))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop10))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop11))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop12))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop13))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop14))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop15))."', '".mysqli_real_escape_string($conn, url_slug($row->keywordtop16))."')");
							mysqli_query($conn, "UPDATE `keywords` SET `built`='1',`reason`='' WHERE `idkeywords`='".$row->idkeywords."'");
							mysqli_query($conn, "UPDATE `websites` SET `built`=`built`+1 WHERE `id`='".$row->website."'");
						} else {
							//if fail to remote
							mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Failed to add post to remote database. It doesn\'t exist, error at inserting.' WHERE `idkeywords`='".$row->idkeywords."'");
							mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
						}
					} else {
						//if exists at remote
						mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Post already exists into remote table.' WHERE `idkeywords`='".$row->idkeywords."'");
						mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
					}
					
					//close connection
					mysqli_close($conn1);
				}
				
			}
		} else {
			mysqli_query($conn, "UPDATE `keywords` SET `built`='2',`reason`='Serp content under 200 words.' WHERE `idkeywords`='".$row->idkeywords."'");
			mysqli_query($conn, "UPDATE `websites` SET `scraped`=`scraped`-1,`scraped_img`=`scraped_img`-1 WHERE `id`='".$row->website."'");
		}
		}
		}
	}
	
	
?>