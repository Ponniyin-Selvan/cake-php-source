{php}
$this->_tpl_vars['member_info'] = $this->_tpl_vars['html']->requestAction('messages/get_member_map_statistics');
{/php}
var map_json = {ldelim} "postInfo" : {$javascript->object($member_info)} {rdelim};
var last_updated_on = "{$date->nice($smarty.now)}";