<?php
vendor('Browscap');
class ApacheLogComponent extends Object {
	
	var $components = array('GeoIp');
	var $log_path = "/Thiru/Personal/Projects/log-parser/logs";
	var $month = array('Jan' => '01','Feb' => '02','Mar' => '03','Apr' => '04','May' => '05','Jun' => '06','Jul' => '07','Aug' => '08','Sep' => '09','Oct' => '10','Nov' => '11','Dec' => '12');
	
	var $ip_ignore = '(129\.9\.163\.234|68\.61\.111\.241|/76\.112\.29\.27)';
	var $ext_static = '(\.css|\.gif/|\.jpg|\.png/|\.js/|\.ico/|\.txt|\.xml)';
	var $search_engines = "(bot|Google|Slurp|Scooter|Spider|Infoseek|W3C_Vali dator|ia_archiver|DiggEffect|SiteUptime|FeedBurner|Wget|Baiduspider|Jakarta Commons-HttpClient)";
	var $bc;
	var $statslog;
	
	function getAllFiles($dir) {
		$files = array();
		
	    if ($dh = opendir($dir)) {
	        while (($file = readdir($dh)) !== false) {
	    	    //echo "<br />filename: $file : filetype: " . filetype($dir . DS . $file) . "\n";
	        	if (is_dir($dir . "/" . $file)) {
	        		if (substr($file, 0, 1) != ".") { 
	        			$files = $files + $this->getAllFiles($dir . "/" . $file);
			        }
	        	} else {
	        		$files[] = $dir . "/" . $file;
	        	}
	        }
	        closedir($dh);
	    }
	    return $files;
	}	
	
	function getStatFlag($data, $browser_info) {
		$flag = null;
		
		if ($flag == null && eregi($this->ip_ignore, $data['from_ip'])) {
			$flag = "T";	
		}
		if ($flag == null && eregi($this->ext_static, $data['path'])) {
			$flag = "S";	
		}
		if ($flag == null && eregi($this->search_engines, $data['agent'])) {
			$flag = "E";	
		}
		if ($flag == null && $browser_info['Crawler']) {
			$flag = "E";	
		}
		return $flag;
	}
	
	function importLog($log_file) {

		$fh = null;
		if (stristr($log_file, ".gz")) {
			$fh = gzopen($log_file, "r");
		} else {
			$fh = fopen($log_file, "r");
		}
		if ($fh) {
			while ($line = fgets($fh)) {
				preg_match("/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")$/", $line, $matches); // pattern to format the line 
			    if (isset($matches[0])) { // check that it formated OK

				  $access_time = $matches[4].':'.$matches[5];
				  $date_parts = explode(":", $access_time);
				  $mdy = explode("/", $date_parts[0]);
				  $mnt = $mdy[1];
				  $mdy[1] = $this->month[$mnt];
				  $hour_parts = explode(" ", $date_parts[2]);
				  $access_time = "$mdy[2]-$mdy[1]-$mdy[0] $date_parts[1]:$date_parts[2]:$hour_parts[0]";
			    	
                  $data = array(); // make an array to store the lin info in
                  $data['from_ip'] = $matches[1];
                  $data['requested_on'] = $access_time;
                  $data['method'] = $matches[7];
                  $data['path'] = $matches[8];
                  $data['status_code'] = intval($matches[10]);
                  $data['bytes'] = $matches[11];
                  $data['referrer'] = ($matches[12] == "\"-\"" ? null : str_replace("\"", "", $matches[12]));
                  $data['agent'] = str_replace("\"", "", $matches[13]);
                  
                  $current_browser = $this->bc->getBrowser($data['agent'], true);
                  $geo_info = $this->GeoIp->lookupIp($data['from_ip']);
                  
                  $data['flag'] = $this->getStatFlag($data, $current_browser);
                  $data['bro_platform'] = $current_browser['Platform']; 
                  $data['browser'] = $current_browser['Browser']; 
                  $data['bro_version'] = $current_browser['Version']; 
                  $data['user_country'] = $geo_info['country_name']; 
                  $data['user_region'] = $geo_info['region_name']; 
                  $data['user_city'] = $geo_info['city']; 
                  $this->statslog->save($data);
			    }
			}
			fclose($fh);
		}
	}
	
	function importAllLogs() {
		$this->bc = new Browscap(WWW_ROOT.'files');
		// Creates a new Browscap object (loads or creates the cache)
		$this->bc->localFile = WWW_ROOT.'files'.DS.'browscap.ini';
		loadModel('Statslog');
		$this->statslog = new Statslog();
		
		$files = $this->getAllFiles($this->log_path);
		foreach($files as $file) {
			echo "<br />".date("H:i:s")." Importing file ...".$file;
			flush();
			$this->importLog($file);
		}
	}
	
	function importDateLog($date){
		$log_file = $this->log_path."/access.log.".$date;
		$this->importLog($log_file);	
	}
	
	function importYesterdayLog() {
		$log_file = "access.log.".date("Y-m-d");
		$this->importLog($this->log_path."/".$log_file);
	}
	
	function browserInfo() {
		$bc = new Browscap(WWW_ROOT.'files');
		$bc->localFile = WWW_ROOT.'files'.DS.'browscap.ini';
		$current_browser = $bc->getBrowser('Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile m.n)', true);
		print_r($this->GeoIp->lookupIp("220.81.242.150"));
		return $current_browser;
	}
}
?>