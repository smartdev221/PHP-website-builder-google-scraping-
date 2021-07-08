<?php
include("../config.php");


class Spintax
{
    public function process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );
    }
    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}
function isCapital($word){
	
	if(preg_match('#^\p{Lu}#u', $word)){
		return true;
	} else {
		return false;
	}
	
}
if(isset($_POST['submit'])){

	$original = preg_replace('/\s+/', ' ',$_POST['text']);
	
	
	
	
	//$original = preg_replace('/\s+/', ' ',$original);
	
	echo "<textarea rows=\"50\" cols=\"50\">".$original."</textarea>";
	
	$split = explode(" ", $original);
	
	//$i = 4;
	if($_POST['no'] > 1){
	for($i = rand(0,5); $i<= count($split); $i=$i+$_POST['no']){
		$word = $split[$i];
			$word1 = str_replace(".","",$word);
			$word1 = str_replace(",","",$word1);
			$word1 = str_replace("?","",$word1);
			$word1 = str_replace("!","",$word1);
			$word1 = str_replace("-","",$word1);
			$word1 = str_replace(":","",$word1);
						
			$word2 = str_replace($word1, "%word%", $word);
			//if the word is windows or error go to no 5
			if(strtolower($word1) == "windows" or strtolower($word1) == "error"){
				if(!isCapital($split[$i-1])){
					//check for capital
				$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-1]))."'");
				if(mysql_num_rows($select) > 0){
					$row1 = mysql_fetch_object($select);
					//echo "{".$row1->spin."}";
					$split[$i-1]= "{".$row1->spin."}";
				} else {
					if(!isCapital($split[$i+1])){
						//if 5 is not in thesaurus go to no 7
						$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i+1]))."'");
						if(mysql_num_rows($select) > 0){
							$row1 = mysql_fetch_object($select);
							//echo "{".$row1->spin."}";
							$split[$i+1]= "{".$row1->spin."}";
						}
					}
				}
				}//end isCapital
			} else {
				//if its not Capital
				if(!isCapital($word1)){
					//select regular word
					$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($word1))."'");
					if(mysql_num_rows($select) > 0){
						$row1 = mysql_fetch_object($select);
						//echo "{".$row1->spin."}";
						$split[$i]= str_replace("%word%","{".$row1->spin."}", $word2);
					} else {
						/// if word not in thesaurus go to no 5
						if(!isCapital($split[$i-1])){
							//echo "%notfound% $word1";
							//$new[]="#not".$word1."found#";
							$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-1]))."'");
							if(mysql_num_rows($select) > 0){
								$row1 = mysql_fetch_object($select);
								//echo "{".$row1->spin."}";
								$split[$i-1]= "{".$row1->spin."}";
							} else {
								/// if no 5 not in thesaurus, go to no 7
								if($i >= count($split)){
									//if 7 reached end of split go to no 4.
									if(!isCapital($split[$i-2])){
										$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i-2]))."'");
										if(mysql_num_rows($select) > 0){
											$row1 = mysql_fetch_object($select);
											//echo "{".$row1->spin."}";
											$split[$i-2]= "{".$row1->spin."}";
										}
									}//end isCapital
									//echo "final";
								} else {
									if(!isCapital($split[$i+1])){
										//if 7 not reached end of life.. continue with it.
										$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($split[$i+1]))."'");
										if(mysql_num_rows($select) > 0){
											$row1 = mysql_fetch_object($select);
											//echo "{".$row1->spin."}";
											$split[$i+1]= "{".$row1->spin."}";
										}
									}//end isCapital
								}	
							}
						}//end isCapital
					}
				}
			}
			
		
		//$i++;
	}
	} else {
		for($i = 0; $i<= count($split); $i=$i+1){
			$word = $split[$i];
			$word1 = str_replace(".","",$word);
			$word1 = str_replace(",","",$word1);
			$word1 = str_replace("?","",$word1);
			$word1 = str_replace("!","",$word1);
			$word1 = str_replace("-","",$word1);
			$word1 = str_replace(":","",$word1);
						
			$word2 = str_replace($word1, "%word%", $word);
			//if the word is windows or error
			if(strtolower($word1) != "windows" or strtolower($word1) != "error"){
				//if its not Capital
				if(!isCapital($word1)){
					//select regular word
					$select = mysql_query("SELECT * FROM `spinwords` WHERE `word`='".mysql_real_escape_string(strtolower($word1))."'");
					if(mysql_num_rows($select) > 0){
						$row1 = mysql_fetch_object($select);
						//echo "{".$row1->spin."}";
						$split[$i] = str_replace("%word%","{".$row1->spin."}", $word2);
					}
				}
			}			
		}
	}
	
	echo "<textarea rows=\"50\" cols=\"50\">".implode(" ", $split)."</textarea>";
	
	
	$spintax = new Spintax();
	$spinned = $spintax->process(implode(" ", $split));
	
	echo "<textarea rows=\"50\" cols=\"50\">".$spinned."</textarea>";
	
	
	} else {
	?>
<form action="#" method="post">
<textarea rows="50" cols="50" name="text"></textarea>

<select name="no">
		<option value="1">1</option>
		<option value="3" selected="">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
	</select>
<input type="submit" name="submit" value="submit">
</form>
<?php	
		
	}

?>