<?php
ini_set('include_path', ini_get('include_path') . ':'. APP.DS.'vendors');
vendor('Zend/Search/Lucene');

class IndexComponent extends Object {

      var $index = null;
      function initialize_index() {
         //Zend_Search_Lucene_Analysis_Analyzer::setDefault(
         //       new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
         $indexPath = TMP."cache/index";
         echo $indexPath;
         if (!file_exists($indexPath)) {
            $this->index = Zend_Search_Lucene::create($indexPath);
         } else {
            $this->index = Zend_Search_Lucene::open($indexPath);
            echo "\nCount ".$indexSize = $this->index->count();
            echo "\nDocs ".$documents = $this->index->numDocs();
            
         }
      }
      
      function add_message($message_details) {

          if ($this->index == null) {
              $this->initialize_index();
          }
          $doc = new Zend_Search_Lucene_Document();
          $doc->addField(Zend_Search_Lucene_Field::Text('message', strtolower($message_details['message']),'utf-8'));
          $doc->addField(Zend_Search_Lucene_Field::Text('topic', strtolower($message_details['thread_topic']),'utf-8'));
          $doc->addField(Zend_Search_Lucene_Field::Text('member', strtolower($message_details['member']),'utf-8'));
          $doc->addField(Zend_Search_Lucene_Field::Text('msg_id', ''.$message_details['id']));
          $doc->addField(Zend_Search_Lucene_Field::Text('posted_on', $message_details['posted_on']));
          $this->index->addDocument($doc);
          $this->index->commit();
      }
      
      function query($query) {

          if ($this->index == null) {
              $this->initialize();
          }
          $hits  = $this->index->find($query);
          //print_r($hits);
          echo "Query ".$query;
          foreach ($hits as $hit) {
               echo "\ntopic - ".$hit->topic;
               echo "\nmessage - ".$hit->message;
          }
      }
}
?>