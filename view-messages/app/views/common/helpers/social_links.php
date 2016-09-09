<?php

class SocialLinksHelper extends AppHelper {

	var $links = array(
					array('img' => 'delicious.png', 'title' => 'Add to del.icio.us Bookmark',
					      'url' => 'http://del.icio.us/post?url=${sl_url}&title=${sl_title}'),
					array('img' => 'digg.png', 'title' => 'Submit to Digg',
					      'url' => 'http://digg.com/submit?phase=2&url=${sl_url}&title=${sl_title}'),
					array('img' => 'reddit.png', 'title' => 'Submit to reddit',
					      'url' => 'http://reddit.com/submit?url=${sl_url}&title=${sl_title}'),
					array('img' => 'stumbleupon.png', 'title' => 'Submit to StumbleUpon',
					      'url' => 'http://www.stumbleupon.com/refer.php?url=${sl_url}&title=${sl_title}'),
					array('img' => 'technorati.png', 'title' => 'Add to Technoarti Favorites',
					      'url' => 'http://www.technorati.com/faves?add=${sl_url}'),
					array('img' => 'google.png', 'title' => 'Add to Google Bookmark',
					      'url' => 'http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=${sl_url}&title=${sl_title}')
					);

	function display($url = null, $subject = null) {

		if ($url == null) {
			$url = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		if (strstr($url, "http://") == false) {
			$url = "http://".$_SERVER["SERVER_NAME"].$url;
		}

		$sl_url = $url;
		if ($subject == null) {
			$static_page = $_SERVER["REQUEST_URI"];
			$static_page = str_replace("/pages", "", $static_page);
			$static_page = str_replace(".html", "", $static_page);
			$static_page = str_replace("/", " ", $static_page);
			$static_page = str_replace("-", " ", $static_page);
			$static_page = ucwords($static_page);
			$sl_title = urlencode($static_page);
		} else {
			$sl_title = urlencode($subject);
		}

		$social_url = "";
		$social_sites_links = "";
		foreach ($this->links as $social_link) {
			$social_link_url = $social_link['url'];
			$social_url = eval("return \"".eval("return \"\$social_link_url\";")."\";");
			$social_url = str_replace('&', '&amp;', $social_url);
			$social_sites_links = $social_sites_links.
				'<li><a target="_blank" title="'.$social_link['title'].'" href="'.
				$social_url.'"><img src="/img/common/snet/'.$social_link['img'].'" alt="'.$social_link['title'].'" /></a></li>';
		}
		return $social_sites_links;
	}
}
?>