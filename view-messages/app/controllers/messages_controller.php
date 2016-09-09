<?php
class MessagesController extends AppController {

    var $name = 'Posts';

    var $components = array('RequestHandler', 'Thread', 'Sitemap', 'Link', 'Archive', 'Email');

    var $paginate = array('limit' => 15, 'order' => array('Post.posted_on' => 'desc'));

	function get_page() {
		$start_post_no = $this->params['PageMap']['start_post_no'];
		$end_post_no = $this->params['PageMap']['end_post_no'];
		return $this->paginate(null, "id >= ".$start_post_no." AND id <= ".$end_post_no);
	}

    function index() {
    }

    function recentTopics() {
        return $this->Post->recent_topics(7);
    }

    function recentPosts() {
        return $this->Post->recent_posts(7);
    }

    function topPosters() {
        return $this->Post->top_posters(7);
    }

    function mostDiscussed() {
		return $this->Post->most_discussed(7);
    }

	function getMessageThread($id) {
		$thread_messages = $this->Post->get_thread_messages($id);
		$thread_messages = $this->Thread->format($thread_messages);
        return $thread_messages;
	}

	function getNeighbourDetails($id) {

        $message_neighbours = $this->Post->get_neighbours($id);
        if ($message_neighbours['prev']) {
        	$message_neighbours['prev'] = $this->Post->get_post_details($message_neighbours['prev']['Post']['id']);
        }
        if ($message_neighbours['next']) {
        	$message_neighbours['next'] = $this->Post->get_post_details($message_neighbours['next']['Post']['id']);
        }

        return $message_neighbours;
	}

    function view($id) {
    }

    function vview($id) {
        $message = null;
        if (!is_numeric($id)) {
            // parse seo friendly url
            $id = substr($id, -11, 6); // xxx-xx-xx-999999.html
        }
        $message_details = null;

        $message = $this->Post->get_post_forum_details($id);
        print_r($message)
    }

    function purgeCache() {
    	clearCache("element_".$this->group_name."_most-discussed-topics",'views','');
    	clearCache("element_".$this->group_name."_recent-posts",'views','');
    	clearCache("element_".$this->group_name."_recent-topics",'views','');
    	clearCache("element_".$this->group_name."_top-posters",'views','');
    	clearCache("element_".$this->group_name."_right-side-bar",'views','');
    	$this->layout = 'plain';
    }

	function createAllSitemaps() {
      	$this->layout = 'plain';
    	$this->Sitemap->create_all_sitemaps($this->group_name);
	}

	function updateAllSitemaps() {
      	$this->layout = 'plain';
    	$this->Sitemap->update_sitemaps($this->group_name);
	}

    function purgeAllCache() {
      	$this->layout = 'plain';
      	$group_files = CACHE."views".DS."*_".$this->group_name."_*";
      	foreach(glob($group_files) as $file) {
		    echo $file."\n";
		    unlink($file);
        }
    }

    function upgrade($post_id = null) {
    	$this->layout = "plain";
    	$this->Archive->upgrade($this->group_name, $post_id);
    }

    function archive() {
                echo "Archiving..<hr />";
		ob_start();

		try {
			$from_no = (array_key_exists('from', $this->params['pass']) ? intval($this->params['pass']['from']) : null);
			$no_of_messages = (array_key_exists('total', $this->params['pass']) ? intval($this->params['pass']['total']) : null);
			$batch_count = (array_key_exists('batch', $this->params['pass']) ? intval($this->params['pass']['batch']) : null);
			$sleep_seconds = (array_key_exists('sleep', $this->params['pass']) ? intval($this->params['pass']['sleep']) : null);
			$send_mail = (array_key_exists('mail', $this->params['pass']) ? intval($this->params['pass']['mail']) : false);
			$debug = (array_key_exists('debug', $this->params['pass']) ? intval($this->params['pass']['debug']) : false);

			/* Fix for Date issue from old messages
			$messages = $this->Post->findAll("DATE(posted_on) = '1969-12-31'",
						array("id"), null);
			foreach($messages as $message) {
				$this->Archive->archive($this->group_name, $message['Post']['id'], 1, 1, 1, false, false);
			}  */
			/* Fix for Thread is '0'
			$messages = $this->Post->findAll("thread_id = 0",
						array("id"), null);
			foreach($messages as $message) {
				$this->Archive->archive($this->group_name, $message['Post']['id'], 1, 1, 1, false, false);
			}  */
			$no_of_messages_archived = $this->Archive->archive($this->group_name,
						$from_no, $no_of_messages, $batch_count, $sleep_seconds);

			if ($no_of_messages_archived && $no_of_messages_archived > 0) {
				$this->updatePageMapping();
			}
		} catch (Exception $e) {
			echo $e->getTraceAsString();
		}
		$archive_content = ob_get_contents();
		ob_end_clean();
		ob_start();

		if ($send_mail && ($no_of_messages_archived > 0 || $debug)) {
			$this->Email->from = "noreply@ponniyinselvan.in";
			$this->Email->to = $this->webmaster_email;
			$this->Email->subject = "$no_of_messages_archived Message(s) archived for $this->group_title";
			//$this->Email->filePaths = array(TMP."archive");
			//$this->Email->attachments = array("cookie-$this->group_name.txt", "archive-$this->group_name.txt");
			$this->Email->send($archive_content);
		} else {
			echo $archive_content;
		}
    }


    function createPageMapping() {
    	$this->layout = "plain";
    	loadModel('PageMap');
    	$page_map = new PageMap();
		$page_map->truncate();

    	$pages = $this->Post->get_page_count($this->page_size);
        echo "pages - $pages page_size - $this->page_size\n";
        $end_no = null;
        for ($i = 1 ; $i <= $pages - $this->index_threshold; $i++) {
        	$condition = null;
   			if ($end_no !== null) {
   				$condition = "id > $end_no";
   			}
			$data = $this->Post->findAll($condition, array("id", "thread_topic", "member_name", "member", "posted_on", "message"), "posted_on ASC", $this->page_size);
			$start_no = $data[0]['Post']['id'];
			$end_no = $data[count($data) - 1]['Post']['id'];
			$data = array('page_no' => ($pages - $i) + 1, 'start_post_no' => $start_no, 'end_post_no' => $end_no);
			$page_map->save($data);
		}
    }

    function updatePageMapping() {
    	$this->layout = "plain";
    	loadModel('PageMap');
    	$page_map = new PageMap();

    	$pages = $this->Post->get_page_count($this->page_size);
		$page_info = $page_map->get_info();

		echo "Total Pages $pages\n";
		echo "Page Mapping info ".print_r($page_info, true)."\n";

		$last_page_no = $page_info[0]['max_page_no'];
		$min_page_no = $page_info[0]['min_page_no'];
		$last_post_no = $page_info[0]['last_post_no'];

		$new_pages = $pages - $last_page_no;
		if ($new_pages > 0) {
		    echo "Found $new_pages New Page(s), updating mapping\n";
			$page_map->update_page_nos($new_pages);
			for ($i = 0 ; $i < $new_pages ; $i++) {
				$data = $this->Post->get_page($last_post_no, null, $this->page_size, "posted_on ASC");
				$start_no = $data[0]['Post']['id'];
				$end_no = $data[count($data) - 1]['Post']['id'];
				$last_post_no = $end_no;
				$data = array('page_no' => $min_page_no + $i, 'start_post_no' => $start_no, 'end_post_no' => $end_no);
				$page_map->save($data);
			}
		} else {
		    echo "No new pages found\n";
		}
    }

	function refreshFormattedMessage($post_no = null) {
    	$this->layout = "plain";
		$this->Archive->format_messages($post_no);
	}

	function refresh($post_no = null) {
    	$this->layout = "plain";
		$this->Archive->refresh($this->group_name, $post_no);
	}

	function analyze($post_no = null) {
    	$this->layout = "plain";
		$this->Archive->analyze($this->group_name, $post_no);
	}

	function get_member_map_statistics() {
    	$this->layout = "plain";
		$data = $this->Post->get_member_map_statistics();
		$map_info = array();
		foreach ($data as $map_data) {
		    $map_info[] = array_merge($map_data['ps_posts'], array("count" => $map_data[0]['count']));
		}
		return $map_info;
	}
}
?>
