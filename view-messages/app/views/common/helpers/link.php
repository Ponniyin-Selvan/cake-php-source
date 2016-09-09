<?php

class LinkHelper extends AppHelper {
	
    var $components = array('Link');

	function seo_friendly($subject, $message_no) {
	    $no_string = substr("000000".$message_no, -6);
	    $url = htmlentities(strtolower($subject));
	    $url = preg_replace("/&([a-z])(uml|acute|grave|circ|tilde|cedil|ring);/", '$1', $url);
	    $url = preg_replace('/([^a-z0-9]+)/', '-', html_entity_decode($url));
	    $url = $url."-{$no_string}.html";
	    return '/messages/view/'.html_entity_decode($url);
	}
}
?>