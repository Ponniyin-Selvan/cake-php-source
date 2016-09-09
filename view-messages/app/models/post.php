<?php
class Post extends AppModel {

   var $name = 'Post';

   var $select_columns = array('id',
   							   'thread_id',
                               'thread_topic',
                               'seo_friendly_topic',
                               'message',
                               'formatted_message',
                               'member',
                               'member_name',
                               'posted_on');

   var $validate = array(
      'id' => VALID_NOT_EMPTY,
      'thread_id' => VALID_NOT_EMPTY,
      'thread_topic' => VALID_NOT_EMPTY,
      'seo_friendly_topic' => VALID_NOT_EMPTY,
      'message' => VALID_NOT_EMPTY,
      'formatted_message' => VALID_NOT_EMPTY,
      'original_source' => VALID_NOT_EMPTY,
      'posted_on' => VALID_NOT_EMPTY
   );

   function recent_topics($no_topics_to_return = 7) {
        return $this->findAll("id = thread_id AND parent_id IS NULL",
        	array("id", "thread_topic"), "posted_on DESC", $no_topics_to_return );
   }

   function recent_posts($no_posts_to_return = 7) {
        return $this->findAll(null, array("id", "thread_topic"),
        	"posted_on DESC", $no_posts_to_return );
   }

   function top_posters($no_posters_to_return = 7) {
	   $query = "SELECT member, member_name, count( id ) \"count\" FROM".
	            " $this->tablePrefix$this->table posts GROUP BY member, member_name".
	            " ORDER BY count( id ) DESC LIMIT $no_posters_to_return";
       return $this->query($query);
   }

   function most_discussed($no_topics_to_return = 7) {
	   $query = "SELECT id, thread_topic FROM $this->tablePrefix$this->table posts,".
	            " (SELECT thread_id, count(id) \"posts\", max(posted_on) \"posted_on\"".
	            " FROM $this->tablePrefix$this->table GROUP BY thread_id ".
	            " ORDER BY posts DESC, posted_on DESC LIMIT $no_topics_to_return) dis".
	            " WHERE posts.id = posts.thread_id AND posts.id = dis.thread_id";
	   return $this->query($query);
   }

   function get_rss($no_topics_to_return = 7) {
	   return $this->findAll(null, null, "posted_on DESC", 50);
   }

   function get_unthreaded_posts() {
	   $query = "SELECT posts.id, posts.thread_id, posts.thread_subject, postc.total_messages".
	   			" FROM $this->tablePrefix$this->table posts,".
	   			" (SELECT thread_id, count(*) \"total_messages\" FROM $this->tablePrefix$this->table".
	   			" GROUP BY thread_id) postc WHERE posts.thread_topic <> \"\"".
	   			" AND posts.thread_id = postc.thread_id AND posts.parent_id = 0".
	   			" AND posts.id = posts.thread_id ORDER BY posts.thread_topic, posted_on";
       return $this->query($query);
   }

   function get_thread_messages($thread_id) {
	   return $this->findAllThreaded (array("thread_id" => $thread_id), null, "posted_on ASC");
   }

   function get_neighbours($post_id) {
	   return $this->findNeighbours (null, "id", $post_id);
   }

   function get_post_details($post_id) {
	   return $this->find(array("id" => $post_id), $this->select_columns);
   }

   function get_original_source($post_id) {
	   return $this->find(array("id" => $post_id), array('id','original_source'));
   }

   function get_last_archived_message_no() {
    	$last_message = $this->find(null, "IFNULL(MAX(id),-1) last_id");
    	return $last_message[0]['last_id'];
   }

   function get_messages_archived_today() {
    	$last_message = $this->find(null, "IFNULL(MAX(id),-1) last_id");
    	return $last_message[0]['last_id'];
   }

   function get_posts_and_last_updated($year = null) {
	   $sql = 	"SELECT posts.id, ".
				"	    posts.thread_id, ".
				"	    posts.seo_friendly_topic, ".
				"	    posts.posted_on, ".
				"	    threads.last_updated_on, ".
				"	    YEAR(posts.posted_on) year".
				"  FROM $this->tablePrefix$this->table posts, (".
				"					SELECT thread_id, ".
				"						   max(posted_on) last_updated_on".
				"					  FROM $this->tablePrefix$this->table".
				"				     GROUP BY thread_id".
				"				   ) threads".
				" WHERE threads.thread_id = posts.thread_id".
				($year !== null ? " AND YEAR(posts.posted_on) = $year" : "").
				" ORDER BY YEAR(posts.posted_on), threads.last_updated_on";

		//print($sql);
        return $this->query($sql);
   }

   function get_years_of_modified_posts($last_updated_on) {
	   $sql =
			" SELECT YEAR(posted_on) year,".
			" 	    count(posts.id)".
			"  FROM $this->tablePrefix$this->table posts, (".
			" 					SELECT thread_id, ".
			" 						   max(posted_on) last_updated_on".
			" 					  FROM $this->tablePrefix$this->table".
			" 					 WHERE posted_on > $last_updated_on".
			" 				     GROUP BY thread_id".
			" 				   ) threads".
			" WHERE threads.thread_id = posts.thread_id".
			" GROUP BY YEAR(posted_on)".
			" ORDER BY posts.archived_on DESC";

        return $this->query($sql);
	}

	function get_page($start_no = null, $end_no = null, $page_size = 15, $order_by = "posted_on DESC") {
	    if ($start_no !== null) {
			$where = "id >= $start_no";
		} else {
			$where = "1=1";
		}
		if ($end_no !== null) {
			$where = $where." AND id <= $end_no";
		}
		if ($page_size == null) {
			$page_size = 15;
		}
        return $this->get_page_data($where , null , $order_by, $page_size);
	}

    function get_page_data($condition = null, $fields = null, $order = null, $page_size = 15) {
        return $this->findAll($condition , $fields , $order, $page_size);
    }

	function paginate($conditions, $fields, $order, $limit, $page, $recursive) {
		if ($page > 10) {
			$limit = null;
		}
		return $this->findAll($conditions, $fields, $order, $limit, $page, $recursive);
	}

	function paginateCount($conditions = null, $recursive = false) {
		return $this->findCount(null, $recursive);
	}

	function get_page_count($page_size = 15, $round = true) {

    	$count = $this->findCount();
        $pages = ($count / $page_size);
        if ($round && (($count/$page_size) - intval($pages) > 0)) {
            $pages = $pages + 1;
        }
        $pages = intval($pages);
        return $pages;
	}

	function get_member_map_statistics() {
	    $sql = " SELECT member_latitude, ".
					"	   member_longitude, ".
					"	   member_country, ".
					"	   member_region, ".
					"	   member_city, ".
					"	   count(id) \"count\"".
					" FROM $this->tablePrefix$this->table".
					" WHERE member_latitude IS NOT NULL".
					"  AND member_longitude IS NOT NULL".
					" GROUP BY member_latitude, ".
					"         member_longitude, ".
					"         member_country, ".
					"         member_region, ".
					"         member_city".
					" ORDER BY count(id) DESC LIMIT 200";
        return $this->query($sql);
	}


   function get_post_forum_details($post_id) {
        $sql = "SELECT a.id,
                       a.phpbb_post_id,
                       b.topic_id,
                       b.post_id,
                       a.thread_topic,
                       b.post_subject,
                       c.topic_first_post_id
                FROM   ps_posts a, phpbb_posts b, phpbb_topics c
                WHERE  a.id = {$post_id}
                  AND a.phpbb_post_id = b.post_id
                  AND b.topic_id = c.topic_id";
       echo $sql;
       return $this->query($sql);
   }
}
?>