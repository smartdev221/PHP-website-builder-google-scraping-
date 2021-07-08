<?php
include 'vendor/autoload.php';

use andreskrey\Readability\Readability;
use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;

$readability = new Readability(new Configuration());

$html = file_get_contents('https://appuals.com/fix-a-required-cd-dvd-drive-device-driver-is-missing-error-message-when-installing-windows-7-from-a-usb/');

try {
    $readability->parse($html);
    echo $readability;
	
	
	$doc = new DOMDocument();
				libxml_use_internal_errors(true);
				
				$doc->loadHTML($readability);
				libxml_clear_errors();

				$intro_s = 0;
				$intro = "";
				$cause_s = 0;
				$cause = "";
				$fix_s = 0;
				$fix = "";
				$headings = array("h2", "h3");
				
$i=1;
				$xpath = new DOMXpath($doc);
				//$elements = $xpath->query("//*");
				$elements = $xpath->query("//div");
				foreach ($elements as $element) {
					echo "<br>element: ".$i." node names:";
					$nodes = $element->childNodes;
					foreach ($nodes as $node) {
						echo " ".$node->nodeName.", ";
						if($intro == "" && $intro_s == 0){
							$intro_s = 1;
							$intro.=$doc->saveHTML($node);
							//print_r($node);
						} elseif(in_array($node->nodeName, $headings) AND $intro_s == 1) {
							$intro_s = 2;
						} elseif($intro_s == 1) {
							$intro.=$doc->saveHTML($node);
						}
						
						if($cause == "" && in_array($node->nodeName, $headings) && preg_match('/(cause|causes|why)/i', $node->textContent)){
							$cause.=$doc->saveHTML($node);
							$cause_s = 1;
						}elseif(in_array($node->nodeName, $headings) AND $cause_s == 1){
							$cause_s = 2;
						}elseif($cause_s == 1){
							$cause.=$doc->saveHTML($node);
						}
						
						if($fix == "" && in_array($node->nodeName, $headings) && preg_match('/(fix|repair|resolution|solution|method)/i', $node->textContent)){
							$fix.=$doc->saveHTML($node);
							$fix_s = 1;
						}elseif(in_array($node->nodeName, $headings) && !preg_match('/(fix|repair|resolution|solution|method)/i', $node->textContent) AND $fix_s == 1){
							$fix_s = 2;
						}elseif($fix_s == 1){
							$fix.=$doc->saveHTML($node);
						}
						//print_r($intro);
					}
				}
				
				print_r($intro);
				echo $intro;
				echo "<hr>".$cause;
				echo "<hr>".$fix;
	
	
} catch (ParseException $e) {
    echo sprintf('Error processing text: %s', $e->getMessage());
}


?>