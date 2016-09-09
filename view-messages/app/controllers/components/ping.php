<?php
class PingComponent extends Object {

    var $ping_urls = array('http://www.google.com/webmasters/sitemaps/ping?sitemap=${url}',
    					   'http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=${url}',
						   'http://submissions.ask.com/ping?sitemap=${url}',
    					   'http://api.moreover.com/ping?u=${url}',
    					   'http://www.feedburner.com/fb/a/pingSubmit?bloglink=${root_url}');

    function ping_services() {
    	$sitemap_url = "http://".$_SERVER['SERVER_NAME']."/sitemap.xml";
    	$this->pingAll($sitemap_url);	
    }
    
    function pingAll($url) {
    	$root_url = urlencode ("http://".$_SERVER['SERVER_NAME']);
    	foreach($this->ping_urls as $url_to_ping) {
    		$url_to_ping = eval("return \"$url_to_ping\";");
    		echo "Pinging $url_to_ping";
    		flush();
    		$success = $this->ping($url_to_ping);
    		if ($success) {
    			echo " Successful\n";		
    		} else {
    			echo " Failed\n";		
    		}
    	}
    	echo "\n\n";

    }
    
	function ping( $uri ) {
	    $timeout = 10;
	    $parsed_url = parse_url($uri);
	
	    if ( !$parsed_url || !is_array($parsed_url) )
	        return false;
	
	    if ( !isset($parsed_url['scheme']) || !in_array($parsed_url['scheme'], array('http','https')) )
	        $uri = 'http://' . $uri;
	
	    if ( ini_get('allow_url_fopen') ) {
	        $fp = @fopen( $uri, 'r' );
	        if ( !$fp )
	            return false;
	
	        //stream_set_timeout($fp, $timeout); // Requires php 4.3
	        $linea = '';
	        while( $remote_read = fread($fp, 4096) )
	            $linea .= $remote_read;
	        fclose($fp);
	        return $linea;
	    } else if ( function_exists('curl_init') ) {
	        $handle = curl_init();
	        curl_setopt ($handle, CURLOPT_URL, $uri);
	        curl_setopt ($handle, CURLOPT_CONNECTTIMEOUT, 1);
	        curl_setopt ($handle, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt ($handle, CURLOPT_TIMEOUT, $timeout);
	        $buffer = curl_exec($handle);
	        curl_close($handle);
	        return $buffer;
	    } else {
	        return false;
	    }
	}
}
?>