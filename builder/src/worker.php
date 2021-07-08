<?php
class worker
{
	function __construct($conn){
		$this->link = $conn;
		$this->processes = array("url_scraper",
							"translator",
							"builder",
							"translator_title",
							"translator_intro",
							"translator_headline",
							"serp_checker",
							"rewrite_steps",
							"rewrite_step2",
							"rewrite_step3",
							"rewrite_check",
							"wordtune",
							"wordtune_check",
							"website_translator",
							"ads_translator",
							"translated_posts_upload");
							//status_checker -> bogs down ~10-18 sec check time
		
		echo "Checking workers total number...\n<br>";
		$this->check_max_workers();
		$this->check_workers_running();
		$this->load(); //load classes
		$this->load_custom(); //load additional classes
		$this->create_new_worker();
	}
	
	function check_workers_running(){
		//check for crashed workers
		$this->free_workers();
		
		$select = mysqli_query($this->link, "SELECT COUNT(`id`) AS current FROM workers");
		$current = mysqli_fetch_object($select)->current;
		if($current >= $this->max_workers){
			die("There are currently enough workers active.");
		} else {
			echo "Will create a worker...\n<br>";
		}
	}
	
	function check_max_workers(){
		$select = mysqli_query($this->link, "SELECT value_ AS max FROM config WHERE `name`='MAX_THREADS'");
		
		$this->max_workers = mysqli_fetch_object($select)->max;
	}
	
	function create_new_worker(){
		//
		$worker = mysqli_query($this->link, "INSERT INTO `workers`(`start`) VALUES('".date('Y-m-d H:i:s')."')");
		if($worker){
			$this->worker_id = mysqli_insert_id($this->link);
		}
		//shuffle processes check order
		shuffle($this->processes);
		
		for($i = 1; $i<=count($this->processes); $i++){
			
			if(!$this->check_if_paused()){
				
				$this->update_status($this->processes[$i-1]);
				
				echo "Checking process ".$this->processes[$i-1]." if it has work...<br>\n";
				//run the process
					$time_start = microtime(true);
						$proc = new $this->processes[$i-1]($this->link);
					$time_end = microtime(true);
					echo "<br>\nThis took ".($time_end - $time_start)." seconds<br>\n<hr>";
				
				//make it run forever with random process
				if($i == count($this->processes)){
					$i = 1;
					shuffle($this->processes);
				}
			} else {
				//kill it
				mysqli_query($this->link, "DELETE FROM `workers` WHERE `id`='".$this->worker_id."'");
				die("The workers are set to pause...");
			}
		}
		usleep(rand(1000000,3000000));
	}
	
	function check_if_paused(){
		$select = mysqli_query($this->link, "SELECT value_ AS paused FROM config WHERE `name`='PAUSED'");
		
		$this->paused = mysqli_fetch_object($select)->paused;
		
		return $this->paused;
	}
	
	function free_workers(){
		//parse workers for crashed ones
		$select = mysqli_query($this->link, "SELECT * FROM `workers`");
		while($row = mysqli_fetch_object($select)){
			//free workers spot if not pinged for 10 minutes
			$ping = strtotime($row->ping)+600;
			if(strtotime(date('Y-m-d H:i:s')) > $ping){
				mysqli_query($this->link, "DELETE FROM `workers` WHERE `id`='".$row->id."'");
				echo "I removed a worker #".$row->id."<br>\n";
			}
		}
	}
	
	function update_status($process){
		mysqli_query($this->link, "UPDATE `workers` SET `status`='".$process."', `ping`='".date('Y-m-d H:i:s')."' WHERE `id`='".$this->worker_id."'");
	}
	
	function load(){
		//load all processes classes
		foreach($this->processes as $process){
			echo "Loading class {$process}<br>\n";
			include_once(__DIR__."/classes/".$process.".php");
		}
	}
	
	function load_custom(){
		//load additional classes
		echo "Loading additional classes<br>\n";
		include_once(__DIR__."/classes/class.Diff.php");
	}
	
}
