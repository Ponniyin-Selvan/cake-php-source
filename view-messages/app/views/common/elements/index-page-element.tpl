{* Smarty *}
  {php}
	$this->_tpl_vars['data'] = $this->_tpl_vars['pageNav']->requestAction('messages/get_page', $this->_tpl_vars['page_info']);
  {/php}
  {$view->element('index-page')}
