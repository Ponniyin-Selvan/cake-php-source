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
  <!-- begin: main content area #main -->

  <div class="others">

  {$content_for_layout|acronymit|smileys|highlight_search_words}
     
  </div> <!-- main -->
  
  {*$view->element('google-ad-top-center-leader-board', $scache)*}
  <div id="footer">{$view->element('page-footer', $scache)}</div>
  
 </div> <!-- page_margins -->
</div> <!-- page -->

</body>
</html>
