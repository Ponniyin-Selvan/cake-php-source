<?php
vendor('Browscap');
class BrowserDetectHelper extends AppHelper {

	function get_user_browser_info() {
	    return $this->get_browser_info($_SERVER['HTTP_USER_AGENT']);
	}

	function get_browser_info($agent_string) {
		$bc = new Browscap(WWW_ROOT.'files' .DS. 'common');
		$bc->localFile = WWW_ROOT.'files'.DS.'browscap.ini';
		$current_browser = $bc->getBrowser($agent_string, true);
		return $current_browser;
	}

	function isIE() {
		$browser_info = get_user_browser_info();
		return ($browser_info['Browser'] == "IE");
	}

	function isIE5() {
		$browser_info = $this->get_user_browser_info();
		//print_r($browser_info);
		return ($browser_info['Browser'] == "IE" && $browser_info['MajorVer'] == 5 && $browser_info['MinorVer'] == 1);
	}
}
?>