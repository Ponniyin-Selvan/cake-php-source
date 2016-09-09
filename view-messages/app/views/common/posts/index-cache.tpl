{* Smarty *}
    <div id="messages">
    {$view->element('index-page-element' , $pcache)}
   </div>
   <div id="message-footer">
    <div class="page-nav">
       {$pageNav->display2($page_no, $page_count)} 
    </div> <!-- pagination -->
   </div> <!-- message-footer -->
