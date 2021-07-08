<?php
session_start();
include("../config.php");

if(empty($_SESSION['loggedin'])){
echo '<meta http-equiv="refresh" content="0; url=login.php">';
} else {
	
	$select = mysqli_query($conn, "SELECT * FROM `keywords` WHERE `idkeywords`='".mysqli_real_escape_string($conn, $_GET['id'])."'");
	
	if(mysqli_num_rows($select) > 0){
		$row = mysqli_fetch_object($select);
		$selweb = mysqli_query($conn, "SELECT `name` FROM `websites` WHERE `id`='".$row->website."'");
		$website = mysqli_fetch_object($selweb);
		if($_GET['type'] == 1){
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";
			echo "<b>Result 1 link:</b>".$row->result1."<br>";
			echo "<b>Result 1 title:</b>".$row->result1title."<br>";
			echo "<b>Result 2 link:</b>".$row->result2."<br>";
			echo "<b>Result 2 title:</b>".$row->result2title."<br>";
			echo "<b>Result 3 link:</b>".$row->result3."<br>";
			echo "<b>Result 3 title:</b>".$row->result3title."<br>";
			echo "<b>Result 4 link:</b>".$row->result4."<br>";
			echo "<b>Result 4 title:</b>".$row->result4title."<br>";
			echo "<b>Result 5 link:</b>".$row->result5."<br>";
			echo "<b>Result 5 title:</b>".$row->result5title."<br>";
			echo "<b>Result 6 link:</b>".$row->result6."<br>";
			echo "<b>Result 6 title:</b>".$row->result6title."<br>";
			echo "<b>Result 7 link:</b>".$row->result7."<br>";
			echo "<b>Result 7 title:</b>".$row->result7title."<br>";
			echo "<b>Result 8 link:</b>".$row->result8."<br>";
			echo "<b>Result 8 title:</b>".$row->result8title."<br>";
			echo "<b>Result 9 link:</b>".$row->result9."<br>";
			echo "<b>Result 9 title:</b>".$row->result9title."<br>";
			echo "<b>Result 10 link:</b>".$row->result10."<br>";
			echo "<b>Result 10 title:</b>".$row->result10title."<br>";
			echo "<b>Related 1:</b>".$row->related1."<br>";
			echo "<b>Related 2:</b>".$row->related2."<br>";
			echo "<b>Related 3:</b>".$row->related3."<br>";
			echo "<b>Related 4:</b>".$row->related4."<br>";
			echo "<b>Related 5:</b>".$row->related5."<br>";
			echo "<b>Related 6:</b>".$row->related6."<br>";
			echo "<b>Related 7:</b>".$row->related7."<br>";
			echo "<b>Related 8:</b>".$row->related8."<br>";
			echo "<b>Related 9:</b>".$row->related9."<br>";
			echo "<b>Related 10:</b>".$row->related10."<br>";
		}elseif($_GET['type'] == 2){
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";
			echo "<b>Video Result 1 link:</b>".$row->vidresult1."<br>";
			//echo "<b>Video Result 1 title:</b>".$row->vidresult1title."<br>";
			echo "<b>Video Result 2 link:</b>".$row->vidresult2."<br>";
			//echo "<b>Video Result 2 title:</b>".$row->vidresult2title."<br>";
			echo "<b>Video Result 3 link:</b>".$row->vidresult3."<br>";
			/*echo "<b>Video Result 3 title:</b>".$row->vidresult3title."<br>";
			echo "<b>Video Result 4 link:</b>".$row->vidresult4."<br>";
			echo "<b>Video Result 4 title:</b>".$row->vidresult4title."<br>";
			echo "<b>Video Result 5 link:</b>".$row->vidresult5."<br>";
			echo "<b>Video Result 5 title:</b>".$row->vidresult5title."<br>";
			echo "<b>Video Result 6 link:</b>".$row->vidresult6."<br>";
			echo "<b>Video Result 6 title:</b>".$row->vidresult6title."<br>";
			echo "<b>Video Result 7 link:</b>".$row->vidresult7."<br>";
			echo "<b>Video Result 7 title:</b>".$row->vidresult7title."<br>";
			echo "<b>Video Result 8 link:</b>".$row->vidresult8."<br>";
			echo "<b>Video Result 8 title:</b>".$row->vidresult8title."<br>";
			echo "<b>Video Result 9 link:</b>".$row->vidresult9."<br>";
			echo "<b>Video Result 9 title:</b>".$row->vidresult9title."<br>";
			echo "<b>Video Result 10 link:</b>".$row->vidresult10."<br>";
			echo "<b>Video Result 10 title:</b>".$row->vidresult10title."<br>";*/
		}elseif($_GET['type'] == 3){
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";		
			for($i = 1; $i<= 20; $i++){			
				echo "<b>Image ".$i.":</b>".$row->{'imgresult'.$i}."<br>";
			}
			for($i = 1; $i<= 16; $i++){			
				echo "<b>Keyword top ".$i.":</b>".$row->{'keywordtop'.$i}."<br>";
			}
		}elseif($_GET['type'] == 4){
			$sel = mysqli_query($conn, "SELECT * FROM `scraped_content_serp` WHERE `idkeywords`='".$row->idkeywords."'");
			$info = mysqli_fetch_object($sel);
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";
			echo "<b>Scraped content:</b>".$info->content."<br><br>";
			if($info->translated == "0"){
				echo "<b>NOT YET TRANSLATED.</b>";
			} else {
				echo "<b>Content german:</b>".$info->content_de."<br><br>";
				echo "<b>Content french:</b>".$info->content_fr."<br><br>";
				echo "<b>Content russian:</b>".$info->content_ru."<br><br>";
				echo "<b>Content english:</b>".$info->content_en."<br><br>";
				
			}
			
		}elseif($_GET['type'] == 5){
			$sel = mysqli_query($conn, "SELECT * FROM `qa` WHERE `original`='".mysqli_real_escape_string($conn, $row->original)."'");
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";
			if(mysqli_num_rows($sel) < 1){
				echo "<br><br><b>There are no q&a's for this particular keyword.</b>";
			}
			while($qa = mysqli_fetch_object($sel)) {
				echo "<b>Question:</b>".$qa->question."<br>";
				echo "<b>Answer:</b>".$qa->answer."<br>";
				echo "<b>Answer german:</b>".$qa->answer_de."<br><br>";
				echo "<b>Answer french:</b>".$qa->answer_fr."<br><br>";
				echo "<b>Answer russian:</b>".$qa->answer_ru."<br><br>";
				echo "<b>Answer english:</b>".$qa->answer_en."<br><br>";
				echo "<hr>";
				
			}
			
		}elseif($_GET['type'] == 6){
			$sel = mysqli_query($conn, "SELECT * FROM `featuredsnippet` WHERE `original`='".mysqli_real_escape_string($conn, $row->original)."'");
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";
			if(mysqli_num_rows($sel) < 1){
				echo "<br><br><b>There are no featured snippets for this particular keyword.</b>";
			}
			while($snippet = mysqli_fetch_object($sel)) {
				echo "<b>Content:</b>".$snippet->snippetcontent."<br>";
				echo "<b>Content german:</b>".$snippet->snippetcontent_de."<br><br>";
				echo "<b>Content french:</b>".$snippet->snippetcontent_fr."<br><br>";
				echo "<b>Content russian:</b>".$snippet->snippetcontent_ru."<br><br>";
				echo "<b>Content english:</b>".$snippet->snippetcontent_en."<br><br>";
				echo "<hr>";
				
			}
			
		}elseif($_GET['type'] == 7){
			$sel = mysqli_query($conn, "SELECT * FROM `built` WHERE `idkeywords`='".mysqli_real_escape_string($conn, $row->idkeywords)."'");
			$page = mysqli_fetch_object($sel);
			echo "<b>Keyword:</b>".$row->original."<br>";
			echo "<b>Website:</b>".$website->name."<br>";
			
			echo "<hr>".$page->content;
			
		}
		
	} else {
		
		echo "Keyword is no longer in the database.";
	}
	
	
}
?>