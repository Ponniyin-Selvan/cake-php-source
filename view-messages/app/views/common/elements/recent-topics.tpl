				<div class="xsnazzy side-bar-widget">
 				 <b class="xtop"><b class="xb1"></b><b class="xb2 color_a"></b><b class="xb3 color_a"></b><b class="xb4 color_a"></b></b>
                 <div class="xboxcontent ">
                  <h1 class="color_a">Recent Topics</h1>
                  <ul>
				  {php}
        			$this->_tpl_vars['recent_topics'] = $this->_tpl_vars['pageNav']->requestAction('messages/recentTopics');
				  {/php}
				  {foreach from=$recent_topics item=message}
                    <li><a href="{$link->seo_friendly($message.Post.thread_topic, $message.Post.id)}">{$message.Post.thread_topic|escape|wordwrap:20:" ":true}</a></li>
				  {/foreach}
                  </ul>
                 </div>
                 <b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
               </div>
