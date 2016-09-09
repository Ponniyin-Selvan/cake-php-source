<?php

class LinkComponent extends Object {
	
	function seo_friendly($subject, $message_no) {
	    $no_string = substr("000000".$message_no, -6);
	    $url = $subject."-{$no_string}.html";
	    return '/messages/view/'.html_entity_decode($url);
	}
}
?>