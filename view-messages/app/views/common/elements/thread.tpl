  {php}
    $this->_tpl_vars['thread_messages'] = $this->_tpl_vars['pageNav']->requestAction('messages/getMessageThread/'.$this->_tpl_vars['message']['Post']['thread_id']);
  {/php}
 <a name="comments"></a>
 {if ($thread_messages)}
   <div id="thread-messages">
    <h2>Message Discussion</h2>
   {foreach from=$thread_messages item=tmessage}
     <div class="message {cycle values=",alt"}">
      <div class="msg-indent{$tmessage.depth}">
       <div class="msg-elbow"><img class="msg-ielbow" src="/img/common/elbow.gif" width="13" height="9" alt="" /></div>
        <h3 class="topic">
         <a href="{$link->seo_friendly($tmessage.Post.thread_topic, $tmessage.Post.id)}"
            title="{$tmessage.Post.id} {$tmessage.Post.thread_topic|htmlentities}">
            {$tmessage.Post.thread_topic|htmlentities}
         </a>
        </h3>
       <p class="info">by {$tmessage.Post.member|wordwrap:25:" ":true}, {$date->nice($tmessage.Post.posted_on)}</p>
	   <p class="snippet">{$tmessage.Post.message|truncate:200|htmlentities|wordwrap:80:" ":true}</p>
      </div>
     </div>
   {/foreach}
   </div>
 {else}
   <div id="thread-messages">
    <h2>Message Discussion</h2>
    <p>Not Discussed</p>
   </div>
 {/if}
