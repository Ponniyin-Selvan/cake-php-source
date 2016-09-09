<?php 
/**
 * Thread Helper, responsible for displaying the messages in thread format
 */
class ThreadComponent extends Object {

    var $components = array('Log');

    function visit($thread_messages, $level) {
    	
		$all_messages = array();
		foreach ($thread_messages as $message) {
			$thread = $message;
      		$thread['depth'] = $level;
      		$thread['children'] = null;
        	array_push($all_messages, $thread);
      		if(isset($message['children'][0])) {
				$child_messages = $this->visit($message['children'], $level + 1);
				foreach($child_messages as $child_message) {
        			array_push($all_messages, $child_message);
				}
      		}
		}
		return $all_messages;
	}
	
	function format($thread_messages) {
		$all_messages = $this->visit($thread_messages, 1);
		return $all_messages;
	}

}
?>