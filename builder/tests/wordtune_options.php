<?php

if(isset($_POST['text'])){
	$option = htmlentities($_POST['option']);
	if($option == 1){
		
	} elseif($option == 2){
		echo "First sentence per paragraph<hr>";
		preg_match_all('/<p>(.*[.?!:])\s?/U', $_POST['text'], $matches);
		foreach($matches[1] as $match){
			echo $match."<br>";
			
		}
		echo "<br>";
	} elseif($option == 3){
		echo "Just headings<hr>";
		preg_match_all('/<h([2-3])>(.*)<\/h[2-3]>/sU', $_POST['text'], $matches);
		foreach($matches[2] as $match){
			echo $match."<br>";
			
		}
		echo "<br>";
	} elseif($option == 5){
		echo "First intro paragraph<hr>";
		preg_match('/<p>(.*)<\/p>/U', $_POST['text'], $matches);
		//foreach($matches[1] as $match){
			echo $matches[1]."<br>";
			
		//}
		echo "<br>";
	}
	
	
}

?>


<form action="#" method="post">
<textarea name="text" rows="10" cols="100"></textarea>

<select name="option">
	<option value="1">Every sentence</option>
	<option value="2">First sentence per paragraph</option>
	<option value="3">Just headings</option>
	<option value="4">First sentence per paragraph and headings</option>
	<option value="5">First intro paragraph</option>
	<option value="6">Just snippets/PAA</option>
</select>



<input type="submit" name="submit" value="submit">
</form>