{* Smarty *}
 {if ($message != null)}
   {$view->element('msg-details', $mcache)}
   {$view->element('thread', $tcache)}
 {else}
   <div id="message">
    <h3>Message not found or Invalid Message No</h3>
   </div>
 {/if}
   