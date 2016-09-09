<?php
/**
 * Thread Helper, responsible for displaying the messages in thread format
 */
class SitemapComponent extends Object {

    var $components = array('Link');
    var $group_name = null;
    var $sitemap_index = null;

    function log_message($message) {
        echo date('Y/m/d H:i:s')." $message\n";
    }

	function init($group_name) {
		$this->group_name = $group_name;
		Configure::load("sitemap-index-$this->group_name");
		$this->sitemap_index = Configure::read('sitemap_index');
	}
    function create_all_sitemaps($group_name) {
    	$this->init($group_name);
    	$this->create_static_files_sitemap();
    	$this->create_year_sitemaps();
    	$this->create_index_sitemap();
    }

    function update_sitemaps($group_name) {
    	$this->init($group_name);
    	$this->update_static_files_sitemap();
    	$this->update_year_sitemaps();
    	$this->create_index_sitemap();
    }

    function get_all_pages($dir) {
    	$files = array();
    	//echo "<br />Reading ".$dir;
    	if (!file_exists($dir)) {
			return null;
		}
	    if ($dh = opendir($dir)) {
	        while (($file = readdir($dh)) !== false) {
	    	    //echo "<br />filename: $file : filetype: " . filetype($dir . DS . $file) . "\n";
	        	if (is_dir($dir . DS . $file)) {
	        		if (substr($file, 0, 1) != ".") {
	        			$files = $files + $this->get_all_pages($dir . DS . $file);
			        }
	        	} else {
	        		$files[] = $dir . DS . $file;
	        	}
	        }
	        closedir($dh);
	    }
	    return $files;
    }

    function create_static_files_sitemap() {
		$static_xml_file = WWW_ROOT . "sitemap-{$this->group_name}-static.xml";
		$this->update_static_files_sitemap();
	}

    function update_static_files_sitemap() {

	    $configure =& Configure::getInstance();

	    $static_files = array();
	    foreach($configure->viewPaths as $view_path) {
	      	$pages = $view_path . 'pages';
			$files = $this->get_all_pages($pages);
			if (!$files == null) {
      			$static_files = array_merge($static_files, $files);
			}
		}

		$static_xml_file = WWW_ROOT . "sitemap-{$this->group_name}-static.xml";

		$static_files_changed = true;
		$last_updated_on = time();
		if (file_exists($static_xml_file)) {
			$static_xml_time = filectime($static_xml_file);
			$static_files_changed = false;

			foreach($static_files as $file) {
				$time = date(DATE_ISO8601 , filectime($file));
				if ($time > $static_xml_time) {
					$static_files_changed = true;
					$last_updated_on = $time;
					break;
				}
			}
		}

		if ($static_files_changed) {
			$this->log("Static Files Changed, update sitemap file");
			$handle = $this->create_sitemap_file($static_xml_file);
			foreach($static_files as $file) {
				$time = filectime($file);
				$file = str_replace($view_path, '/', $file);
				$file = str_replace(DS.$this->group_name, '', $file);
				$file = str_replace(DS, '/', $file);
				$file = str_replace(".tpl", '', $file);
				$file = $file;
				fwrite($handle, $this->get_xml($file, $time));
				echo $file." last modified on ".$time;
			}
			$this->close_sitemap_file($handle);
			$this->sitemap_index['static'] = array('type' => '', 'updated_on' => date("Y:m:d H:i:s", $last_updated_on));
		} else {
			$this->log("Static Files SiteMap is up-to-date");
		}
    }

	function create_index_sitemap() {
		$index_xml_file = "";
		foreach($this->sitemap_index as $key => $year_index) {
			if (is_array($year_index)) {
				$file_name = "sitemap-{$this->group_name}".($year_index['type'] == "" ? "" : "-".$year_index['type'])."-{$key}.xml";
				$index_xml_file = $index_xml_file.$this->get_sitemap_index_xml($file_name, $year_index['updated_on']);
			}
		}
		$this->create_sitemap_index_file("sitemap-{$this->group_name}-index.xml", $index_xml_file);
		$this->sitemap_index['last_updated_on'] = date('YmdHis', time());
		Configure::store("sitemap_index", "sitemap-index-$this->group_name", $this->sitemap_index);
	}

	function update_year_sitemaps() {
    	loadModel('Post');
    	$post = new Post();

		$this->log_message('Last Updated On '.$this->sitemap_index['last_updated_on']);

		$years = $post->get_years_of_modified_posts($this->sitemap_index['last_updated_on']);
		if (count($years) > 0) {
			foreach($years as $year) {
				$this->create_year_sitemaps($year[0]['year']);
			}
			$this->log_message("Changes found, sitemap files are updated");
		} else {
			$this->log_message("No Changes found, sitemap files are not updated");
		}
	}

    function create_year_sitemaps($year = null) {

    	loadModel('Post');
    	$post = new Post();

		$posts = $post->get_posts_and_last_updated($year);
		if (count($posts) == 0) {
			return;
		}
		$current_year = $posts[0][0]['year'];
		$year = $current_year;
		$current_xml_file = null;
		$previous_post = null;

		foreach($posts as $post_info) {
			$year = $post_info[0]['year'];
			if ($current_year !== $year) {
				$this->create_message_sitemap_year_file($current_year, $current_xml_file);
				$current_xml_file = "";
				$this->sitemap_index[$current_year] = array('type' => 'posts', 'updated_on' => $previous_post['threads']['last_updated_on']);
				$current_year = $year;
			}
			$current_xml_file = $current_xml_file.
				$this->get_post_xml($post_info['posts']['id'],
						$post_info['posts']['seo_friendly_topic'],
						$post_info['threads']['last_updated_on']);
			$previous_post = $post_info;
		}

		if ($current_xml_file !== "") {
			$this->create_message_sitemap_year_file($year, $current_xml_file);
			$this->sitemap_index[$current_year] = array('type' => 'posts', 'updated_on' => $previous_post['threads']['last_updated_on']);
		}
    }

	function create_message_today_sitemap_file($xml) {
		$file_name = "sitemap-{$this->group_name}-posts-today.xml";
		create_message_sitemap_file($file_name, $xml);
	}

	function create_message_sitemap_year_file($year, $xml) {
		$file_name = "sitemap-{$this->group_name}-posts-{$year}.xml";
		$this->create_message_sitemap_file($file_name, $xml);
	}

	function create_message_sitemap_file($file_name, $xml) {
		//print($file_name);
		//print($xml);
		$handle = $this->create_sitemap_file($file_name);
		fwrite($handle, $xml);
		$this->close_sitemap_file($handle);
	}

	function create_sitemap_index_file($file_name, $xml) {
		$handle = $this->create_index_file($file_name);
		fwrite($handle, $xml);
		$this->close_index_file($handle);
	}

	function get_post_xml($id, $thread_subject, $last_updated) {
    	$url = $this->Link->seo_friendly($thread_subject, $id);
    	$xml = $this->get_xml($url, $last_updated, "daily", "0.9");
    	return $xml;
    }

    function get_xml($url, $last_updated, $frequency = "weekly", $priority = "1.0") {
    	if (gettype($last_updated) == "string") {
    		$last_updated = strtotime($last_updated);
    	}
    	$last_updated = date(DATE_W3C, $last_updated);
    	$xml = "<url>\n<loc>http://".$_SERVER['SERVER_NAME']."$url</loc>\n<lastmod>$last_updated</lastmod>\n";
    	$xml = $xml."<changefreq>$frequency</changefreq>\n<priority>$priority</priority>\n";
    	$xml = $xml."</url>\n";
    	return $xml;
    }

    function create_sitemap_file($file_name) {
    	$handle = fopen($file_name, "w");

    	$xml_header = "<?xml version='1.0' encoding='UTF-8'?>\n".
		"	<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n".
		"	xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n".
		"	xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n".
		"	http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";

    	fwrite($handle, $xml_header);
    	return $handle;
    }

    function close_sitemap_file($handle) {
	    fwrite($handle, "</urlset>\n");
    	fclose($handle);
    }

    function get_sitemap_index_xml($url, $last_updated) {
    	if (gettype($last_updated) == "string") {
    		$last_updated = strtotime($last_updated);
    	}
    	$last_updated = date(DATE_W3C, $last_updated);
    	$xml = "<sitemap>\n<loc>http://".$_SERVER['SERVER_NAME']."/$url</loc>\n<lastmod>$last_updated</lastmod>\n";
    	$xml = $xml."</sitemap>\n";
    	return $xml;
    }

    function create_index_file($file_name) {
    	$handle = fopen($file_name, "w");
    	$xml_header = "<?xml version='1.0' encoding='UTF-8'?>\n".
			"	<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n".
			"	xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n".
			"	xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n".
			"	http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd\">";

    	fwrite($handle, $xml_header);
    	return $handle;
    }

    function close_index_file($handle) {
	    fwrite($handle, "</sitemapindex>\n");
    	fclose($handle);
    }
}
?>