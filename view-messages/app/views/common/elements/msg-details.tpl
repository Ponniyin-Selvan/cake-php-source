{* Smarty *}
   {php}
     $message = $this->_tpl_vars['message'];
     $this->_tpl_vars['message_neighbours'] = $this->_tpl_vars['pageNav']->requestAction('messages/getNeighbourDetails/'.$message['Post']['id']);
     $this->_tpl_vars['current_url'] = $this->_tpl_vars['link']->seo_friendly($message['Post']['thread_topic'], $message['Post']['id']);
   {/php}
   {if ($message != null)}
   <div id="message-details">
    <h3 class="topic">
     <a href="{$current_url}" 
        title="{$message.Post.id} {$message.Post.thread_topic|htmlentities}">
        {$message.Post.thread_topic}
     </a>
    </h3>
    <p class="info">by {$message.Post.member_name|wordwrap:25:" ":true} ({$message.Post.member|wordwrap:25:" ":true}), {*timeframe_format from=$message.Post.posted_on ago*} on {$date->nice($message.Post.posted_on)}</p>
    <ul class="slinks">
       {$socialLinks->display($current_url, $message.Post.thread_topic)}
    </ul>
    {*$view->element('related-wikipedia', $scache)*}
    {$message.Post.formatted_message}
    <div class="msg-links">
     <a target="_blank" href="http://groups.yahoo.com/group/ponniyinselvan/post?act=reply&amp;messageNum={$message.Post.id}">Reply</a>
     | <a target="_blank" href="http://groups.yahoo.com/group/ponniyinselvan/post?act=forward&amp;messageNum={$message.Post.id}">Forward</a>
     | 
	{if (is_array($message_neighbours.prev))}
	     <a href="{$link->seo_friendly($message_neighbours.prev.Post.thread_topic, $message_neighbours.prev.Post.id)}"
	        title="{$message_neighbours.next.Post.id} {$message_neighbours.prev.Post.thread_topic|htmlentities}">&lt; Prev</a>
	{else}
		&lt; Prev
	{/if}
	|
	{if (is_array($message_neighbours.next))}
	     <a href="{$link->seo_friendly($message_neighbours.next.Post.thread_topic, $message_neighbours.next.Post.id)}"
	        title="{$message_neighbours.next.Post.id} {$message_neighbours.next.Post.thread_topic|htmlentities}">Next &gt;</a>
	{else}
		Next &gt;
	{/if}
    </div> <!-- msg-links -->
   </div> 
  {/if}
