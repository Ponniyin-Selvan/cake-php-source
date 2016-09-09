<?php
class LogsController extends AppController {
	
    var $name = 'Statslog';
	var $components = array('ApacheLog');
	
    function index() {
    	set_time_limit(0);
      	$this->layout = 'plain';
		$this->ApacheLog->importAllLogs(); 
    }
}
?>