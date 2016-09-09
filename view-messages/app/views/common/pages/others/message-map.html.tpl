{* Smarty *}
{if ($browserDetect->isIE5())}
Your Browser doesn't support Google Maps <a href="http://maps.google.com/support/bin/answer.py?hl=en&answer=16532">Please check whether your browser is supported here</a>
{else}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_map_key}"
      type="text/javascript"></script>
<script type="text/javascript">
    //<![CDATA[
    {$view->element('google-maps-json', $wcache)}
    //]]>
   jQuery(function($) {ldelim} loadMemberMap(); {rdelim});
   //jQuery(document).unload(function(){ldelim} GUnload(); {rdelim});

</script>
<div id="map">
 <p>The map gives an overview of where the message are coming from. In other words, from where the members are posting messages to the group.
 Please move the mouse over <img src="http://maps.google.com/mapfiles/ms/micons/red-dot.png" /> to see the location and message counts.
 You can use the Controls on the left side of the map to Move the map, Zoom in and out. You can also click and drag the map using the mouse.</p>
<p>
<script type="text/javascript">
document.writeln("Map last updated on " + last_updated_on);
</script>
</p>
</div>
<div id="message-map">
   <img src="/img/common/loading.gif" />
   <noscript><div class="alert">Please enable javascript to view the map. </div></noscript>
</div>
{/if}
