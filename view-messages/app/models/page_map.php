<?php
class PageMap extends AppModel {

   var $name = 'PageMap';

   function get_page_details($page_no) {
	   return $this->find(array("page_no" => $page_no));
   }

   function get_info() {
	   return $this->find(null, array('MAX(end_post_no) last_post_no',
	   			'MAX(page_no) max_page_no', 'MIN(page_no) min_page_no'));
   }

   function update_page_nos($increment) {
   	   $this->query("UPDATE $this->tablePrefix$this->table SET page_no = page_no + $increment");
   }

   function truncate() {
   	   $this->query("TRUNCATE TABLE $this->tablePrefix$this->table");
   }

}
?>