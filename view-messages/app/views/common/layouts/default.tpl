{* Smarty *}
{$view->element('html-header', $scache)}
{$view->element('group-html-header', $scache)}
{$view->element('project-html-header', $scache)}
<title>{$group_title} : {$title_for_layout}</title>
{if (!$browserDetect->isIE5())}
<script type="text/javascript" src="/js/all_js.js"></script>
{/if}
</head>
<body id="default">

<div id="page_margins">

 <div id="page">

  <div id="header">{$view->element('page-header', $scache)}</div>
  <div id="nav_main">{$view->element('page-nav', $scache)}</div>
  {$view->element('google-ad-top-center-leader-board', $scache)}
  <!-- begin: main content area #main -->

  <div id="main">

	<div id="col1">
   	 <div id="col1_content" class="clearfix">
	  <a name="navigation"></a>
	  <noscript><div class="alert">Please enable javascript to access all features of the site. </div></noscript>
	  <!-- google_ad_section_start -->
	  {$content_for_layout|smileys|highlight_search_words}
   	  <!-- google_ad_section_end -->
     </div> <!-- col1_content -->
    </div> <!-- col1 -->
    
	<div id="col2">
  	 <div id="col2_content" class="clearfix">
      {$view->element('google-ad-left-sky-scrapper', $scache)}
     </div> <!-- col2_content -->
    </div> <!-- col2 -->
  	 
	<div id="col3">
	  <div id="col3_content" class="clearfix">
		<a id="content" name="content"></a>
  	    {$view->element('right-side-bar', $scache)|highlight_search_words}
        {$view->element('google-ad-right-square', $scache)}
  	  </div> <!-- col3_content -->
	  <div id="ie_clearing">&amp;nbsp;</div>
  	</div> <!-- col3 -->
     
  </div> <!-- main -->
  
  <div id="footer">{$view->element('page-footer', $scache)}</div>
  
 </div> <!-- page_margins -->
</div> <!-- page -->

</body>
</html>