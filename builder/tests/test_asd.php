<?php
$content_en = "<p> At any moment, a lot of information is transferred between your Windows 10 PC and the endless vacuum on the Internet. This is done through a process in which network-dependent processes look for free TCP and UDP ports through which they communicate with the Internet. First, your data is sent to the remote destination ports or website to which your processes want to connect, and then to the local ports of your PC. </P>
<p> In most cases, Windows 10 knows how to manage the ports and make sure that traffic is routed through the right ports so that these processes can connect to what they need. However, sometimes a port can be assigned to two ports, or you just want to get a better picture of network traffic and I / O. </P>
<p> For this reason, we decided to write this guide, which shows how to get an overview of your ports and see which applications use which ports. </P>
<h2> hint method </h2>
<p> Click the Start button, type <code> cmd </code> and right-click Command Prompt when it appears in the search results. Click â€œRun as administratorÐ Ð°ratora '. </P>
<p> This continues to display a list of ports that can be quite long, as well as the Windows processes that they use. (You can press <kbd> Ctrl </kbd> + <kbd> A, </kbd>, and then press <kbd> Ctrl </kbd> + <kbd> C </kbd> to copy all the information to print -papiers.) On an average PC, there are two local primary IP addresses that contain ports on your PC. </P>
<p> First of all, it is '127.0.0.1'. This IP address is also called â€œlocalhostâ€ or â€œfeedback addressâ€. Each process that listens on ports interacts within your local network without using a network interface. The actual port is the number you see after the colon (see the figure below). </P>
<p> Most of your processes are probably listening on ports with the prefix '192.168.xxx.xxx'. This is your IP address. This means that the processes listed here are awaiting communication from remote Internet sites (such as websites). Again, the port number is the number after the colon. </P>
<h2> TCPView </h2>
<p> If you donâ€™t mind installing a third-party application and want to have more control over what happens to all of your ports, you can use the lightweightAn attachment called TCPView. It immediately displays a list of related processes and ports. </P>
<p> What makes this better than the command line is that you can actively see how ports open, close, and send packets. Pay attention to green, red and yellow reflections. You can also modify the list by clicking on the column headers to find the process you need or find two separate processes competing for the same port. </P>
<p> If you find a process or connection that you want to close, just right-click on it. Then you can select â€œEnd Processâ€. This is exactly the same function as in Windows Task Manager. Or you can click â€œClose Connectionâ€ to leave the process open, but prevent it from monitoring a specific port. </P>
<p> If you have problems with Windows 10, check if it could be related to updating Windows. We also have a handy guide for managing the state of your hard drive in Windows 10. </p>";
$content = "%serpcontent%";
$content = str_replace('%serpcontent%', "[serpcontent]%top% %middle% %qas% %images1%[/serpcontent]", $content);
			
			preg_match('/\[serpcontent\]([A-z0-9%\s]{1,})\[\/serpcontent\]/', $content, $matches);
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
				//shuffle($tags);
				//shuffle($tags);
				shuffle($tags);
				print_r($tags);
				foreach($tags as $match){
					//$paragraphs[] = "<br>".$match."<br>";
				}
				//shuffle($paragraphs);
				$it = 0;
				foreach($paragraphs as $par){
					if(!empty($tags[$it])){
						$paragraphs1[] = $par."<br>".$tags[$it]."<br>";
					} else {
						$paragraphs1[] = $par;
					}
					$it++;
				}
				print_r($paragraphs);
				print_r($paragraphs1);
				$content = str_replace($matches[0], "%serpcontent%", $content);
				$content_en = implode("<p", $paragraphs1);
				foreach($tags as $match){
					$content_en = str_replace("<p<br>".$match, $match, $content_en);
				}
			}
			print_r($content_en);
			$content = str_replace('%serpcontent%', $content_en, $content);
			$content = str_replace('%source%', $serp->source, $content);
			$serpcontent = $serp->content_en;



?>