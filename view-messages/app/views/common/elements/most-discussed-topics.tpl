				<div class="xsnazzy side-bar-widget">
 				 <b class="xtop"><b class="xb1"></b><b class="xb2 color_a"></b><b class="xb3 color_a"></b><b class="xb4 color_a"></b></b>
                 <div class="xboxcontent ">
                  <h1 class="color_a">Most Discussed Topics</h1>
                  <ul>
				  {php}
					$this->_tpl_vars['most_discussed'] = $this->_tpl_vars['pageNav']->requestAction('messages/mostDiscussed');
				  {/php}
				  {foreach from=$most_discussed item=message}
                    <li><a href="{$link->seo_friendly($message.posts.thread_topic, $message.posts.id)}">{$message.posts.thread_topic|escape|wordwrap:20:" ":true}</a></li>
				  {/foreach}
                  </ul>
                 </div>
                 <b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
               </div>
