<?php

vendor('mailparser');
vendor('formatting');

class ArchiveComponent extends Object {

	var $components = array('GeoIp', 'Index', 'Ping');
    var $name = 'Posts';

	var $client = null;
	var $debug_log_file = null;
	var $login_url = "https://login.yahoo.com/config/login";
	var $logout_url = "http://login.yahoo.com/config/login?logout=1&.partner=&.intl=us&.done=http%3a%2f%2fmy.yahoo.com%2findex.html&.src=my";
	var $group_url = "http://groups.yahoo.com";
	var $login_id = "ps.archive";
	var $login_pwd = "ps12345";
	var $login_src = "ygrp";
	var $message_uri = 'http://groups.yahoo.com/group/$this->group_name/message/$message_no/?source=1&var=1&l=1';
	var $init_format_uri = 'http://groups.yahoo.com/group/$this->group_name/messages/?xm=1&m=p&l=1';
	var $group_name = null;

	var $use_proxy = false;

    var $threads;

    var $db_conn;

    var $msg = null;
    var $post = null;
    
    var $links = array();

	function initialize_db() {
		//loadModel('Message');
		loadModel('Post');
    	$this->post = new Post();
    	//$this->msg = new Message();
	}

	function initiliaze_client() {
		$this->client = curl_init();

		$archive_debug_log = TMP."archive".DS."archive-".$this->group_name.".txt";
		$archive_cookie_file = TMP."archive".DS."cookie-".$this->group_name.".txt";
		$this->debug_log_file = fopen ($archive_debug_log, "w");

		if (!$this->debug_log_file) {
			die("Unable to open ".$archive_debug_log. " for writing.\n");
		}
		curl_setopt ($this->client, CURLOPT_FILE, $this->debug_log_file);
		// set URL and other appropriate options
		curl_setopt($this->client, CURLOPT_FAILONERROR, TRUE);
		curl_setopt($this->client, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->client, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($this->client, CURLOPT_HEADER, FALSE);
		curl_setopt($this->client, CURLOPT_CONNECTTIMEOUT, 30); //30 seconds
		curl_setopt($this->client, CURLOPT_STDERR, $this->debug_log_file);

		curl_setopt ($this->client, CURLOPT_COOKIEJAR, $archive_cookie_file);
		curl_setopt ($this->client, CURLOPT_COOKIEFILE, $archive_cookie_file);

		curl_setopt($this->client, CURLOPT_VERBOSE, TRUE);
		// define CGI VARIABLE if behind proxy
		if (array_key_exists('ARC_PROXY_SERVER', $_SERVER)) {
			curl_setopt($this->client, CURLOPT_PROXY, $_SERVER['ARC_PROXY_SERVER']);
			curl_setopt($this->client, CURLOPT_PROXYUSERPWD, $_SERVER['ARC_PROXY_USERPWD']);
		}
		curl_setopt($this->client, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
	}

    function log_message($message) {
        echo date('Y/m/d H:i:s')." $message\n";
    }

	function close_client() {
		// close CURL resource, and free up system resources
		curl_close($this->client);
		fclose ($this->debug_log_file);
		$this->log_message('Closed client');
	}

	function __destruct() {
		if ($this->client !== null) {
			$this->close_client();
		}
	}

	function login() {
		$login_string = "login=$this->login_id&passwd=$this->login_pwd&.src=$this->login_src&.done=$this->group_url";

		curl_setopt ($this->client, CURLOPT_URL, $this->login_url);
		curl_setopt($this->client, CURLOPT_POST, TRUE);
		curl_setopt($this->client, CURLOPT_POSTFIELDS, $login_string);
		// grab URL and pass it to the browser
		$result = curl_exec($this->client);
		$status = curl_errno($this->client);
		if ($status != CURLE_OK) {
			$this->log_message("Login Error No ".curl_errno($this->client));
			$this->log_message("Login Error String ".curl_error ($this->client));
		}
		return ($status == CURLE_OK);
	}

	function logout() {
		curl_setopt ($this->client, CURLOPT_URL, $this->logout_url);
		$result = curl_exec($this->client);
		$status = curl_errno($this->client);
		if ($status != CURLE_OK) {
			$this->log_message("Logout Error No ".curl_errno($this->client));
			$this->log_message("Logout Error String ".curl_error ($this->client));
		}
		return ($status == CURLE_OK);
	}

	function getMessage($message_no) {
		$uri = eval("return \"$this->message_uri\";");
		return $this->getPage($uri);
	}

	function getPage($uri) {
		$this->log_message("Get page $uri");

		curl_setopt ($this->client, CURLOPT_URL, $uri);
		$result = curl_exec($this->client);
		$status = curl_errno($this->client);
		if ($status != CURLE_OK) {
			$this->log_message("Get Page Error No ".curl_errno($this->client));
			$this->log_message("Get Page Error String ".curl_error ($this->client));
			$result = null;
		} else {
			//$this->log_message("\nResult $result");
		}
		return $result;
	}

	function get_last_archived_message_no() {
    	return $this->post->get_last_archived_message_no();
	}

	function seo_friendly($subject) {
	    $subject = htmlentities(strtolower($subject));
	    $subject = preg_replace("/&([a-z])(uml|acute|grave|circ|tilde|cedil|ring);/", '$1', $subject);
	    $subject = preg_replace('/([^a-z0-9]+)/', '-', html_entity_decode($subject));
	    return $subject;
	}

	function extract_message($xpath) {

		$td_source = $xpath->query('//td[@class="source user"]');
		$email_content = null;

		if ($td_source->length > 0) {
			$td_element = $td_source->item(0);
			$email_content_nodes = $td_element->childNodes;
			// Go through each child nodes, and create mail message
			$line = "";
			for ($i = 0 ; $i < $email_content_nodes->length ; $i++) {
				$text = trim($email_content_nodes->item($i)->textContent);
				//$this->log_message("\n".$i." ==> ".$email_content_nodes->item($i)->nodeName." ==> ".$text);
				// if there is a <br />, then add a new line
				if ($email_content_nodes->item($i)->nodeName == "br") {
                    $line = str_replace("To:", "To: ", $line);
					$email_content = $email_content.$line."\n";
					$line = "";
				} else if ($text == "From") {
					$line = "Fromx: ";
				} else if (substr($text, -1) == ":") {
					$line = $line.$text." ";
				} else {
					$line = $line.$text;
				}
			}
		} else {
			$this->log_message("Not able to get the email source");
		}
		return $email_content;
	}

	function decode_email($source) {
		$mail_parser = new MailParser;
		$mail = $mail_parser->parse($source);
		return $mail;
	}

	function set_subject_details($source, $message, $xpath, $message_details) {

          $subject_remove_find = array("re:", "fw:", "fwd:", "[".$this->group_name."]");
          $subject_remove_replace = array("", "", "", "");
          $subject = trim(iconv_mime_decode($message->getHeader('subject'), 0, "UTF-8"));
          echo $subject."<br />";
          $thread_subject = trim(str_ireplace($subject_remove_find, $subject_remove_replace, $subject));
          $thread_subject = ($thread_subject == "" ? "(no subject)" : $thread_subject);

          // actual subject from email
          $message_details['topic'] = $subject;
          // remove unnecessary re:fw:fwd: and [groupname]
          $message_details['thread_topic'] = $thread_subject;
          // make it search engine friendly for url
          $message_details['seo_friendly_topic'] = $this->seo_friendly($thread_subject);
          return $message_details;
	}

	// figure out from which ip the message was sent
	function set_ip_info($source, $message, $xpath, $message_details) {

        $ip = "";
        if (stristr($message->getHeader('user-agent'), 'eGroups-EW')) {
            $ip = $message->getHeader('x-yahoo-post-ip');
            if ($ip == null || $ip == "") {
               $ip = $message->getHeader('x-originating-ip');
            }
            if ($ip == null || $ip == "") {
               $ip = $message->getHeader('x-egroups-remote-ip');
            }

        } else {
		    $received_header = $message->getHeader('received');
     	    $received = split(";", $received_header);
        		// extract the ip address
		    preg_match("/(\d{1,3}\.){3}\d{1,3}/", $received[0], $matches, PREG_OFFSET_CAPTURE);
		    if (count($matches) > 0) {
			   $ip =  $matches[0][0];
            }
        }
        if ($ip != "") {
           $message_details['member_ip'] = $ip;
       		// Get the dns name
       	   //$message_details['member_host_name'] = gethostbyaddr($ip);
       		// Get geographic information
       	   $geo_info = $this->GeoIp->lookupIp($ip);
       	   $message_details['member_country'] = $geo_info['country_name'];
           $message_details['member_region'] = $geo_info['region_name'];
           $message_details['member_city'] = ($geo_info['city'] == "Madras" ? "Chennai" : $geo_info['city']);
           $message_details['member_latitude'] = $geo_info['latitude'];
           $message_details['member_longitude'] = $geo_info['longitude'];
        }

        return $message_details;
	}

    function get_mail_parts($part, $mail_parts = null) {
		//echo get_class($part)."\n";

		if ($mail_parts == null) {
			$mail_parts = array();
		}
		if ($part instanceof ezcMail) {
			$mail_parts = $this->get_mail_parts($part->body, $mail_parts);

		} else if ($part instanceof ezcMailText) {
			if (!array_key_exists($part->subType, $mail_parts)) {
				$mail_parts[$part->subType] = array();
			}
			$mail_parts[$part->subType][] = $part->text;

		} else if ($part instanceof ezcMailRfc822Digest) {
        	$mail_parts = $this->get_mail_parts($part->mail, $mail_parts);

        } else if ($part instanceof ezcMailMultiPartAlternative
            			|| $part instanceof ezcMailMultipartMixed) {
			foreach ($part->getParts() as $key => $alternativePart)  {
				$mail_parts = $this->get_mail_parts($alternativePart, $mail_parts);
			}

        } else if ($part instanceof ezcMailMultiPartRelated ) {
			$mail_parts = $this->get_mail_parts($part->getMainPart(), $mail_parts);

	    } else if ($part instanceof ezcMailFile) {
			// ignore

		} else {
        	$this->log_message("No clue about the ".get_class( $part )."\n");
		}
        return $mail_parts;
    }

	function set_message_content($source, $message, $xpath, $message_details) {
		$mail_parts = $this->get_mail_parts($message->body);
		$mail_content = "";
		if (array_key_exists('plain', $mail_parts)) {
			foreach($mail_parts['plain'] as $text) {
				$mail_content.= $text."\n";
			}
		} else if (array_key_exists('html', $mail_parts)) {
			foreach($mail_parts['html'] as $text) {
				$mail_content.= $text."\n";
			}
		}
		$message_details['message'] = $mail_content;
		return $message_details;
	}

	function set_thread_format($source, $message, $xpath, $message_details) {
		// Thread ids and their format
		$thread_tds = $xpath->query('//table[@id="ygrp-msglist"]//td[@class="message "]|//table[@id="ygrp-msglist"]//td[@class="message footaction"]');
		$thread_info = array();
		for ($i = 0 ; $i < $thread_tds->length ; $i++) {
			$thread_detail = array();
			$td_element = $thread_tds->item($i);
			$thread_hrefs = $td_element->getElementsByTagName("a");
			for ($j = 0 ; $j < $thread_hrefs->length ; $j++) {
				//$this->log_message("\n".$thread_hrefs->item($j)->getAttribute("href"));
				preg_match("/[0-9]+/", $thread_hrefs->item($j)->getAttribute("href"), $matches, PREG_OFFSET_CAPTURE);
				$thread_detail['id'] = intval($matches[0][0]);
			}
			$thread_divs = $td_element->getElementsByTagName("div");
			for ($j = 0 ; $j < $thread_divs->length ; $j++) {
				//$this->log_message("\n".$thread_divs->item($j)->getAttribute("class"));
				preg_match("/[0-9]+/", $thread_divs->item($j)->getAttribute("class"), $matches, PREG_OFFSET_CAPTURE);
				if (count($matches) > 0) {
					$thread_detail['indent'] = intval($matches[0][0]);
				}
			}
			$thread_info[] = $thread_detail;
		}

        // Update the parent, child relationship using the indentation

		if (count($thread_info) > 0) {
			$message_details['thread_id'] = $thread_info[0]['id'];
			for ($i = 1 ; $i < count($thread_info); $i++) {
				$id = $thread_info[$i]['id'];
                $parent_id = intval($thread_info[0]['id']);
                if (array_key_exists('indent', $thread_info[$i]) && $thread_info[$i]['indent'] > 0) {
                    for ($j = $i ; $j >= 0; $j--) {
                        if (array_key_exists('indent', $thread_info[$j])
                            && $thread_info[$j]['indent'] == $thread_info[$i]['indent'] - 1) {
                            $parent_id = intval($thread_info[$j]['id']);
                            break;
                        }
                    }
                }
                $thread_info[$i]['parent_id'] = intval($parent_id);
                if ($thread_info[$i]['id'] == $message_details['id']) {
                   $message_details['parent_id'] = $parent_id;
                }
			}
		} else {
			$message_details['thread_id'] = $message_details['id'];
		}
		$message_details['thread_format'] = $thread_info;

		return $message_details;
	}

	function set_email_meta_data($source, $message, $xpath, $message_details) {
		$message_details['posted_on'] = date('YmdHis', strtotime($message->getHeader('date')));
		$member_from = $message->getHeader('from');
        $delimiter_pos = strrpos($member_from, "<");

        if ($delimiter_pos > 0) {
           $member_from = substr($member_from, 0, $delimiter_pos);
        }
        $member_from = str_replace("\"", "", $member_from);

        $message_details['member_name'] = $member_from;
		$message_details['member'] = $message->getHeader('x-yahoo-profile');
		if (!$message_details['member']) {
		   $message_details['member'] = $member_from;
		}
		preg_match("/[^@]+/", $message->messageId, $matches, PREG_OFFSET_CAPTURE);
		if (count($matches) > 0) {
			$message_details['email_msg_id'] = substr($matches[0][0], 1); //$message['Headers']['message-id:'];
		}
		preg_match("/[^@]+/", $message->getHeader('in-reply-to'), $matches, PREG_OFFSET_CAPTURE);
		if (count($matches) > 0) {
			$message_details['reply_to_msg_id'] = substr($matches[0][0], 1);
		}
		$message_details['original_source'] = $source;
		return $message_details;
	}

	function set_message_no_info($source, $message, $xpath, $message_details) {
		$msg_td = $xpath->query('//table[@class="footaction"]//td[@align="right"]');
		if ($msg_td->length > 0) {
			$td_element = $msg_td->item(0);
			preg_match("/[0-9]+/", $td_element->textContent, $matches, PREG_OFFSET_CAPTURE);
			if (count($matches) > 0) {
				$message_details['id'] = intval($matches[0][0]);
			} else  {
                $this->log_message("Couldn\'t find message no from $msg_td->length,$td_element->textContent");
            }

			// Next Prev
			$nav_hrefs = $td_element->getElementsByTagName("a");
			for ($j = 0 ; $j < $nav_hrefs->length ; $j++) {
				preg_match("/[0-9]+/", $nav_hrefs->item($j)->getAttribute("href"), $matches, PREG_OFFSET_CAPTURE);
				//print_r($matches);
				if (strstr($nav_hrefs->item($j)->textContent, "Prev")) {
					$message_details['prev_message_no'] = intval($matches[0][0]);
				} else if (strstr($nav_hrefs->item($j)->textContent, "Next")) {
					$message_details['next_message_no'] = intval($matches[0][0]);
				}
			}
		}
		return $message_details;
	}

	function format_message($source, $message, $xpath, $message_details) {
		$message_body = $message_details['message'];
		$message_body = strip_tags($message_body);
        $message_body = hide_quoted_text($message_body);
		$message_body = preg_replace_callback(
                      "!\bhttps?://([\w\-]+\.)+[a-zA-Z0-9]{2,3}(/(\S+)?)?\b!",
                      array($this,'linkify_url'), $message_body);
		/*$message_body = preg_replace_callback(
                      '/\b(http:\/\/|https:\/\/){0,1}(\w(\w|-)+\.)+(dk|com|net|org|se|no|nl|us|uk|de|it|nu|edu|info)(\/\w*)*(\.\w{2,4}){0,1}(\?\w*|#\w*|&\w*|=\w*)*\b/',
                      'linkify', $message_body);*/
		$message_body = my_wordwrap($message_body, 55, " ", true);
		$message_body = nl2br($message_body);
		$message_body = smileys_to_images($message_body);
        $message_body = acronymit($message_body);
		$message_details['formatted_message'] = "<p>".utf8_encode($message_body)."</p>";
		return $message_details;
	}

	function extract_message_details($source_html) {

		$dom = new DOMDocument();
		@$dom->loadHTML($source_html);
		$xpath = new DOMXPath($dom);

		$source = $this->extract_message($xpath);
		if ($source == null) {
			$this->log_message("Not able to extract message");
			return null;
		}
		print_r($source);

		$message = $this->decode_email($source);
		if ($message == null) {
			$this->log_message("Not able to decode message as email");
			return null;
		}
		print_r($message);

		$message_details = array();
		$message_details = $this->set_ip_info($source, $message, $xpath, $message_details);
		$message_details = $this->set_message_content($source, $message, $xpath, $message_details);
		$message_details = $this->set_subject_details($source, $message, $xpath, $message_details);
		$message_details = $this->set_email_meta_data($source, $message, $xpath, $message_details);
		$message_details = $this->set_message_no_info($source, $message, $xpath, $message_details);
		$message_details = $this->set_thread_format($source, $message, $xpath, $message_details);
		$message_details = $this->format_message($source, $message, $xpath, $message_details);
		//print_r($message_details);
		return $message_details;
	}

    function upgrade($group_name, $post_id = null) {
        set_time_limit(0);
        $this->group_name = $group_name;
        $this->initialize_db();
    	if ($post_id != null) {
			$message = $this->msg->find(array("id" => $post_id));
			$source = $message['Message']['original_source'];
			$source = str_replace("To:", "To: ", $source);
			$source = str_replace("From:", "From: ", $source);
			$source = str_replace("X-X-Sender:", "X-X-Sender: ", $source);
			$source = str_replace("Sender:", "Sender: ", $source);
			$new_post = array();
			$new_post['id'] = $message['Message']['id'];
			$new_post['parent_id'] = intval($message['Message']['parent_id']);
			$new_post['thread_id'] = intval($message['Message']['thread_id']);
			$new_post['message'] = $message['Message']['plain_text_message'];
			$new_post['archived_on'] = $message['Message']['archived_on'];

			try {
				$mail_message = $this->decode_email($source);
				$new_post = $this->set_ip_info(null, $mail_message, null, $new_post);
				$new_post = $this->set_subject_details(null, $mail_message, null, $new_post);
				$new_post = $this->set_email_meta_data(null, $mail_message, null, $new_post);
				$new_post = $this->format_message(null, $mail_message, null, $new_post);
			} catch (Exception $e) {
				$this->log_message("Exception ".$e->getTraceAsString()." while upgrading ".$i." Message ".$message['Message']['id']);
			}

			$new_post['original_source'] = $source;
			echo "Successfully saved ".$this->post->save($new_post);
			return;
		}
    	$count = $this->msg->findCount();
        $pages = ($count / 100);
        if (($count /100) - intval($pages) > 0) {
            $pages = $pages + 1;
        }
        $pages = intval($pages);
        for ($i = 1 ; $i <= $pages ; $i++) {

			$messages = $this->msg->findAll(null, null, null, 100, $i, null);

			foreach($messages as $message) {
				$this->log_message("Archiving Page ".$i." Message ".$message['Message']['id']);
				//print_r($message);
				$new_post = array();
				$new_post['id'] = $message['Message']['id'];
				$new_post['parent_id'] = (
					($message['Message']['parent_id'] == 0
					     || $message['Message']['parent_id'] == '0') ? null : $message['Message']['parent_id']);
				$new_post['thread_id'] = $message['Message']['thread_id'];
				$new_post['message'] = $message['Message']['plain_text_message'];
				$new_post['archived_on'] = $message['Message']['archived_on'];

				$source = $message['Message']['original_source'];
				$source = str_replace("To:", "To: ", $source);
				try {
					$mail_message = $this->decode_email($source);
					//print_r($mail_message);
					$new_post = $this->set_ip_info(null, $mail_message, null, $new_post);
					$new_post = $this->set_subject_details(null, $mail_message, null, $new_post);
					$new_post = $this->set_email_meta_data(null, $mail_message, null, $new_post);
					$new_post = $this->format_message(null, $mail_message, null, $new_post);
				} catch (Exception $e) {
					$this->log_message("Exception ".$e->getTraceAsString()." while upgrading ".$i." Message ".$message['Message']['id']);
				}

				$new_post['original_source'] = $source;
				//print_r($new_post);
				$this->post->save($new_post);
			}
        }
    }

    function refresh_message($source) {
        $new_post = array();
		try {
			$source = str_replace("From ", "From: ", $source);
			$mail_message = $this->decode_email($source);
			//print_r($mail_message);
			$new_post = $this->set_ip_info(null, $mail_message, null, $new_post);
		    $new_post = $this->set_message_content($source, $mail_message, null, $new_post);
			$new_post = $this->set_subject_details(null, $mail_message, null, $new_post);
			$new_post = $this->set_email_meta_data(null, $mail_message, null, $new_post);
			$new_post = $this->format_message(null, $mail_message, null, $new_post);
		} catch (Exception $e) {
			$this->log_message("Exception ".$e->getTraceAsString()." while upgrading ".$i." Message ".$message['Message']['id']);
		}
		$new_post['original_source'] = $source;
		return $new_post;
    }

    function refresh($group_name, $post_id = null) {
        set_time_limit(0);
        $this->group_name = $group_name;
        $this->initialize_db();

    	if ($post_id != null) {
			$message = $this->post->get_original_source($post_id);
			$source = $message['Post']['original_source'];
            $new_post = $this->refresh_message($source);
            $new_post['id'] = $message['Post']['id'];
            $this->post->save($new_post);
			return;
		}
    	$pages = $this->post->get_page_count(100);
        echo "pages - $pages\n";
        $end_no = null;
        for ($i = 1 ; $i <= $pages; $i++) {
            $where = "1=1";
            if ($end_no !== null) {
                $where = $where." AND id > $end_no";
            }
            //$where = $where." AND member_latitude IS NULL and member_country IS NOT NULL";
			$messages = $this->post->get_page_data($where, null,"posted_on ASC", 100);
			$start_no = $messages[0]['Post']['id'];
			$end_no = $messages[count($messages) - 1]['Post']['id'];

			$this->log("Refreshing Page ".$i);
			flush();
			foreach($messages as $message) {
			    //$this->log_message("Post ".$message['Post']['id']);
				//print_r($message);
				$source = $message['Post']['original_source'];
                $new_post = $this->refresh_message($source);
                $new_post['id'] = $message['Post']['id'];
				$this->post->save($new_post);
			}
		}
        //print_r($acronyms);
    }

    function linkify_url($matches) {
	   $url = trim($matches[0]);
       $linkified_url = linkify($url);
       $embed_url = embedify($url);
       if ($embed_url !== null) {
       		$linkified_url = $embed_url."<br /><p>".$linkified_url."</p>";
       }
       return "</p>".$linkified_url."<p>";
    }

    function gather_links($matches) {
       $path_info = null;
	   $url = trim($matches[0]);
	   $url_info = parse_url($url);
	   $host = $url_info['host'];
	   if (array_key_exists($host, $this->links)) {
          $this->links[$host] = array_merge($this->links[$host], array($url => $url));
       } else {
          $this->links[$host] = array($url => $url);
       }
       //print_r($this->links);
    }

    function analyze($group_name, $post_id = null) {
        set_time_limit(0);
        $this->group_name = $group_name;
        $this->initialize_db();
        $acronyms = array();

    	if ($post_id != null) {
			$message = $this->post->get_post_details($post_id);
			$source = $message['Post']['message'];
		    preg_replace_callback(
                      "!\bhttps?://([\w\-]+\.)+[a-zA-Z0-9]{2,3}(/(\S+)?)?\b!",
                      array(&$this, 'gather_links'), $source);
            print_r($this->links);
			return;
		}
    	$pages = $this->post->get_page_count(200);
        echo "pages - $pages\n";
        $end_no = null;
        for ($i = 1 ; $i <= $pages; $i++) {
            $where = "1=1";
            if ($end_no !== null) {
                $where = $where." AND id > $end_no";
            }
            //$where = $where." AND member_latitude IS NULL and member_country IS NOT NULL";
			$messages = $this->post->get_page_data($where, null,"posted_on ASC", 200);
			$start_no = $messages[0]['Post']['id'];
			$end_no = $messages[count($messages) - 1]['Post']['id'];

			$this->log_message("Refreshing Page ".$i);
			flush();
			foreach($messages as $message) {
				$source = $message['Post']['message'];
		        preg_replace_callback(
                      "!\bhttps?://([\w\-]+\.)+[a-zA-Z0-9]{2,3}(/(\S+)?)?\b!",
                      array(&$this, 'gather_links'), $source);
			}
		}
		ksort($this->links);
		print_r($this->links);
    }

    function format_messages($post_no = null) {

        set_time_limit(0);
		$this->initialize_db();

		if ($post_no !== null) {
			$posted_msg = $this->post->get_post_details($post_no);
			$posted_msg['Post'] = $this->format_message(null, null, null, $posted_msg['Post']);
			$this->post->save($posted_msg['Post']);
		} else {
    		$pages = $this->post->get_page_count();
			$from_post_no = null;

			$this->log_message("Total Pages $pages\n");
    		for($i = 0 ; $i < $pages ; $i++) {
				$this->log_message("\nFormatting Page $i\n");
    			$condition = null;
    			if ($from_post_no !== null) {
    				$condition = "id > $from_post_no";
    			}
    			$posts = $this->post->findAll($condition, null, 'posted_on ASC', 15, 1);
    			if ($posts !== null && count($posts) > 0) {
					$from_post_no = $posts[count($posts) - 1]['Post']['id'];
    			} else {
    				$this->log_message(print_r($condition, true)." returned 0 rows");
    			}
    			foreach ($posts as $posted_msg) {
    				$this->log_message("Formatting message ".$posted_msg['Post']['id']);
					$posted_msg['Post'] = $this->format_message(null, null, null, $posted_msg['Post']);
					$this->post->save($posted_msg['Post']);
    			}
    		}
    	}
    }

    function test() {
		$message_html = file_get_contents(TMP."archive".DS."message.html");
		$message_details = $this->extract_message_details($message_html);
		print_r($message_details);
    }

    function clear_cache() {
    	clearCache("element_".$this->group_name."_most-discussed-topics",'views','');
    	clearCache("element_".$this->group_name."_recent-posts",'views','');
    	clearCache("element_".$this->group_name."_recent-topics",'views','');
    	clearCache("element_".$this->group_name."_top-posters",'views','');
    	clearCache("element_".$this->group_name."_right-side-bar",'views','');
    }

	function archive($group_name, $from_message_no = -1,
                    $no_of_messages_to_archive = 10, $batch_count = 5,
                    $sleep_time = 8, $ping = true, $clear_cache = true) {

		$this->test();
		return;

        $from_message_no = ($from_message_no == null ? -1 : $from_message_no);
        $no_of_messages_to_archive = ($no_of_messages_to_archive == null ? 10 : $no_of_messages_to_archive);
        $batch_count = ($batch_count == null ? 5 : $batch_count);
        $sleep_time = ($sleep_time == null ? 8 : $sleep_time);

        $this->group_name = $group_name;

        set_time_limit(0);
        $this->initialize_db();
		$this->initiliaze_client();

		if (!$this->login()) {
			$this->log_message("Couldn't login to Yahoo Groups'");
			$this->close_client();
			return;
		}

		$proceed = true;
		$archive_no = -1;
		$no_of_messages_archived = 0;

        if ($from_message_no == -1) { // Retrieve last archived message no + 1
		    $from_message_no = $this->get_last_archived_message_no();
		    if ($from_message_no == -1) { // Never archived before, archive from 1
                $archive_no = 1;
            } else {
				$this->log_message("Last Archived message ".$from_message_no);
		        $source = $this->getMessage($from_message_no);
			    if ($source != null) {
				    $message_details = $this->extract_message_details($source);
				    if ($message_details == null) {
					    $this->log_message("Not able to get the message details for message ==> ".$from_message_no);
					    $proceed = false;
				    } else if (!array_key_exists('next_message_no', $message_details)) {
						      $this->log_message("No new messages found");
						      $proceed = false;
				    } else {
					      $archive_no = $message_details['next_message_no'];
				    }
			    } else {
				    $this->log_message("Not able to get the email source");
				    $proceed = false;
			    }
            }
		} else {
			$archive_no = $from_message_no; // Retrieve from specific message no
		}


		if ($proceed) {
        	$this->log_message("Archiving from $from_message_no");
			while ($archive_no > 0) {
				$this->log_message("Archiving message ==> ".$archive_no);
				$source = $this->getMessage($archive_no);
				if ($source != null) {
					$message_details = $this->extract_message_details($source);
					if ($message_details == null) {
						$this->log_message("Not able to get the message details for message ==> ".$archive_no);
                        $archive_no = $archive_no + 1;
						//break;
					} else {
						$this->log_message("Archived message ==> ".$message_details['thread_topic'].", by ".$message_details['member_name']);
                        $no_of_messages_archived++;
                        $this->post->save($message_details);
                        // clear thread cache
						clearCache("element_".$group_name."_".$message_details['thread_id']."_thread",'views','');
					}
					if (array_key_exists('next_message_no', $message_details)) {
						$archive_no = $message_details['next_message_no'];
					} else {
						$archive_no = 0;
					}
				} else {
					$this->log_message("Not able to get the email source");
					break;
				}
				if ($no_of_messages_to_archive != -1
                   && $no_of_messages_archived == $no_of_messages_to_archive) {
                   break;
                }
                if (($no_of_messages_archived % $batch_count) == 0) {
                    sleep($sleep_time);
                }
			}
			if ($no_of_messages_archived > 0) {
				$this->log_message("Archived $no_of_messages_archived new messages");
				// Ping Search engine, rss providers
				if ($ping) {
    				$this->Ping->ping_services();
				}
				if ($clear_cache) {
					// Clear side bar cache
					$this->clear_cache();
				}
			}
		}

		$this->logout();

		$this->log_message("Done\n");
		return $no_of_messages_archived;
	}
}
