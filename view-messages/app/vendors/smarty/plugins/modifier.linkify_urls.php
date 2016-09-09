<?php

function smarty_linkify($matches) {
	$link_name = wordwrap($matches[0], 55, " ", true);
	$url = trim($matches[0]);
	$url = (stristr($url, "&") ? urlencode($url) : $url);
	$linkify = "http://".$_SERVER['SERVER_NAME']."/redirect.php?uri=".$url;
	return "<a target=\"_blank\" href=\"".$linkify."\" rel=\"nofollow\">".$link_name."</a>";
}

function smarty_modifier_linkify_urls($text) {
        $text = ' ' . $text . ' ';
        $text = preg_replace_callback("!\bhttps?://([\w\-]+\.)+[a-zA-Z]{2,3}(/(\S+)?)?\b!", "smarty_linkify", $text);
        return $text;
}

?>
