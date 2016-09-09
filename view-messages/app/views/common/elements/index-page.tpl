{* Smarty *}
    {foreach from=$data item=message}
     <div class="message {cycle values=",alt"}">
      <h3 class="topic">
       <a href="{$link->seo_friendly($message.Post.thread_topic, $message.Post.id)}"
          title="{$message.Post.id} {$message.Post.thread_topic|htmlentities}">
          {$message.Post.thread_topic|htmlentities|wordwrap:40:" ":true}
       </a>
      </h3>
      <p class="info">by {$message.Post.member_name|wordwrap:25:" ":true} ({$message.Post.member|wordwrap:25:" ":true}), {*timeframe_format from=$message.Post.posted_on ago*} on {$date->nice($message.Post.posted_on)}</p>
      <p class="snippet">{$message.Post.message|truncate:300|htmlentities|wordwrap:80:" ":true}
        <a href="{$link->seo_friendly($message.Post.thread_topic, $message.Post.id)}"
          title="{$message.Post.thread_topic|htmlentities}">
          <img alt="{$message.Post.thread_topic|htmlentities}" src="/img/common/goto.gif" />
        </a>
      </p>
     </div>
    {/foreach}