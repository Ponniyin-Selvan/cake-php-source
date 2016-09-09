				<div class="xsnazzy side-bar-widget">
 				 <b class="xtop"><b class="xb1"></b><b class="xb2 color_a"></b><b class="xb3 color_a"></b><b class="xb4 color_a"></b></b>
                 <div class="xboxcontent ">
                  <h1 class="color_a">Top Posters</h1>
				  <table cellspacing="0">
					<tr>
					 <th>Poster</th>
					 <th>Posts</th>
					</tr>
				  {php}
					$this->_tpl_vars['top_posters'] = $this->_tpl_vars['pageNav']->requestAction('messages/topPosters');
				  {/php}
				  {foreach from=$top_posters item=poster}
					<tr>
					 <td class="poster">{$poster.posts.member_name|wordwrap:25:" ":true}</td>
					 <td class="number">{$poster.0.count|number_format:0:".":","}</td>
					</tr>
				  {/foreach}
				  </table>
                 </div>
                 <b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
               </div>
