<?php

function sortr_longer($first, $second) {
   return (strlen($first) < strlen($second)) ? 1 : -1;
}
function acronymit($text) {
    $acronyms = array(
'AIIMS'	=>	'All India Institute of Medical Sciences',
'AIR '	=>	'All India Radio',
'AK '	=>	'Adithya Karikalan',
'AKK '	=>	'Adithya Karikalan',
'ARCH '	=>	'Archaeology',
'ASAP '	=>	'As soon  as possible',
'ASI'	=>	'Archaeological Survey of India',
'BHEL'	=>	'Bharat Heavy Electricals Limited',
'BIT'	=>	'Birla Institute of Technology',
'BSE'	=>	'Bombay Stock Exchange',
'BSNL'	=>	'Bharat Sanchar Nigam Ltd',
'CBI'	=>	'Central Bureau of Investigation',
'CBSE'	=>	'Central Board of Secondary Education',
'CBT'	=>	'Computer Based Training',
'CRPF'	=>	'Central Reserve Police Force',
'CSI'	=>	'Computer Society of India',
'DIY'	=>	'Do It Youself',
'DRDO'	=>	'Defence Research & Development Organisation',
'FAQ'	=>	'Frequently Asked Questions',
'FYI'	=>	'For Your Information',
'GKC'	=>	'Gangai Konda Cholapuram',
'IIITB'	=>	'Indian Institute of Information Technology, Bangalore',
'IIM'	=>	'Indian Institute of Management',
'IIT'	=>	'Indian Instittue of Technology',
'IMHO'	=>	'In my humble opinion',
'IMO'	=>	'In my opinion',
'ISCKON'	=>	'International Society of Krishna Consciousness',
'ISRO'	=>	'Indian Space Research Organisation',
'KAN'	=>	'KA Neelakanta shastri',
'KANS'	=>	'KA Neelakanta shastri',
'PK'	=>	'Parthiban Kanavu',
'PS'	=>	'Ponniyin Selvan',
'RBI'	=>	'Reserve Bank Of India',
'RC'	=>	'Rajendra Cholan',
'RCII'	=>	'Rajendra Cholan II',
'RJ'	=>	'Rajendra Cholan',
'RJC'	=>	'Rajendra Cholan',
'RR'	=>	'Raja Raja Cholan',
'RRC'	=>	'Raja Raja Cholan',
'SS'	=>	'Sivakamiyin Sabatham',
'TNAU'	=>	'Tamilnadu Agricultural University',
'TNJ'	=>	'Thanjavur',
'TNPSC'	=>	'Tamil Nadu Public Service Commission',
'TSC'	=>	'Tamil Standard Code',
'TSCII'	=>	"'Tamil Standard Code,
for Information Interchange'",
'VD'	=>	'Vallavaraiyan Vandhiyathevan',
'VT'	=>	'Vallavaraiyan Vandhiyathevan',
'VV'	=>	'Vallavaraiyan Vandhiyathevan',
'VVD'	=>	'Vallavaraiyan Vandhiyathevan'
    );
    uksort($acronyms, 'sortr_longer'); // comment out if already sorted
    foreach ($acronyms as $acronym => $definition) {
        $text = preg_replace("|(?!<[^<>]*?)(?<![?./&])\b$acronym\b(?!:)(?![^<>]*?>)|imsU","<acronym title=\"$definition\">$acronym</acronym>", $text);
    }
    return $text;
}

function smileys_to_images($string) {

	$smiliestrans = array(
	':mrgreen:'	=> 'icon_mrgreen.gif',
	':neutral:'	=> 'icon_neutral.gif',
	':twisted:'	=> 'icon_twisted.gif',
	':arrow:'	=> 'icon_arrow.gif',
	':shock:'	=> 'icon_eek.gif',
	':smile:'	=> 'icon_smile.gif',
	' :???:'	=> 'icon_confused.gif',
	':cool:'	=> 'icon_cool.gif',
	':evil:'	=> 'icon_evil.gif',
	':grin:'	=> 'icon_biggrin.gif',
	':idea:'	=> 'icon_idea.gif',
	':oops:'	=> 'icon_redface.gif',
	':razz:'	=> 'icon_razz.gif',
	':roll:'	=> 'icon_rolleyes.gif',
	':wink:'	=> 'icon_wink.gif',
	':cry:'		=> 'icon_cry.gif',
	':eek:'		=> 'icon_surprised.gif',
	':lol:'		=> 'icon_lol.gif',
	':mad:'		=> 'icon_mad.gif',
	':sad:'		=> 'icon_sad.gif',
	' 8-)'		=> 'icon_cool.gif',
	' 8-O'		=> 'icon_eek.gif',
	' :-('		=> 'icon_sad.gif',
	' :-)'		=> 'icon_smile.gif',
	' :-?'		=> 'icon_confused.gif',
	' :-D'		=> 'icon_biggrin.gif',
	' :-P'		=> 'icon_razz.gif',
	' :-o'		=> 'icon_surprised.gif',
	' :-x'		=> 'icon_mad.gif',
	' :-|'		=> 'icon_neutral.gif',
	' ;-)'		=> 'icon_wink.gif',
	' 8)'		=> 'icon_cool.gif',
	' 8O'		=> 'icon_eek.gif',
	' :('		=> 'icon_sad.gif',
	' :)'		=> 'icon_smile.gif',
	' :?'		=> 'icon_confused.gif',
	' :D'		=> 'icon_biggrin.gif',
	' :P'		=> 'icon_razz.gif',
	' :o'		=> 'icon_surprised.gif',
	' :x'		=> 'icon_mad.gif',
	' :|'		=> 'icon_neutral.gif',
	' ;)'		=> 'icon_wink.gif',
	':!:'		=> 'icon_exclaim.gif',
	':?:'		=> 'icon_question.gif',
	);

	$url = "/img/common/smileys/";

	// generates smilies' search & replace arrays
	foreach($smiliestrans as $smiley => $img) {
	    $smilies_search[] = $smiley;
	    $smiley_masked = htmlspecialchars( trim($smiley) , ENT_QUOTES);
	    $smilies_replace[] = " <img src='" . $url . "$img' alt='$smiley_masked' class='smiley' /> ";
	}
	$string = str_replace($smilies_search, $smilies_replace, $string);
	return $string;

}

function kuerzen($string,$length) {
	$returner = $string;
	if (strlen($returner) > $length) {
		$url = preg_match("=[^/]/[^/]=",$returner,$treffer,PREG_OFFSET_CAPTURE);
		$cutpos = $treffer[0][1]+2;
		$part[0] = substr($returner,0,$cutpos);
		$part[1] = substr($returner,$cutpos);

		$strlen1 = $cutpos;
		if ($strlen1 > $length) return substr($returner,0,$length-3).'...';
		$strlen2 = strlen($part[1]);
		$cutpos = $strlen2-($length-3-$strlen1);
		$returner = $part[0].'...'.substr($part[1],$cutpos);
	}
	return $returner;
}

function embedify($url) {

   $embed_info = array(
					array('type' => 'host',
					      'host' => 'www.youtube.com',
						  'params' => array('v'),
						  'embed_string' => '<object type="application/x-shockwave-flash" style="width:425px; height:350px;" data="http://www.youtube.com/v/{v}"><param name="movie" value="http://www.youtube.com/v/{v}" /></object>'
						  ),
					array('type' => 'host',
					      'host' => 'video.google.com',
						  'params' => array('docid'),
						  'embed_string' => '<object type="application/x-shockwave-flash" style="width:425px; height:350px;" data="http://video.google.com/googleplayer.swf?docId={docid}"><param name="wmode" value="transparent" /><param name="movie" value="http://video.google.com/googleplayer.swf?docId={docid}" /></object>'
						  ),
					array('type' => 'extension',
					      'extension' => 'mp3',
						  'embed_string' => '<object width="240" height="20" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"> <param name="movie" value="/files/common/mp3player.swf" /> <param name="menu" value="false" /> <param name="quality" value="low" /> <param name="flashvars" value="file={url}" /> <embed src="/files/common/mp3player.swf" width="240" height="20" menu="false" quality="low" flashvars="file={url}" type="application/x-shockwave-flash" pluginspage= "http://www.macromedia.com/go/getflashplayer" /> </object>'
						  )
					);

    $embed_url = "";
	foreach($embed_info as $embed) {
        $url_info = parse_url($url);
		switch($embed['type']) {
		    case 'host':
			     if ($url_info['host'] == $embed['host']) {
				   if (array_key_exists("query", $url_info)) {
			           parse_str($url_info['query'], $params);
			           $embed_url = $embed['embed_string'];
			           foreach($embed['params'] as $param) {
						  if (isset($params[$param])) {
	                          $embed_url = str_replace("{".$param."}", $params[$param], $embed_url);
						  }
			           }
					}
				 }
				 break;
		    case 'extension':
				 if (isset($url_info['path'])) {
					 $path_info = pathinfo($url_info['path']);
				     if (array_key_exists('extension', $path_info)) {
				         $extension = $path_info['extension'];
				         if ($extension == $embed['extension']) {
				             $embed_url = $embed['embed_string'];
				         }
				     }
				 }
				 break;
		}
	}
    $embed_url = str_replace("{url}", $url, $embed_url);
    return $embed_url;
}

function linkify($url_to_linkify) {
	$link_name = kuerzen($url_to_linkify, 50);
	$url = trim($url_to_linkify);
	$url = (stristr($url, "://") ? $url : "http://".$url);
	$url = (stristr($url, "&") ? urlencode($url) : $url);
	$linkify = "/redirect.php?uri=".$url;
	$anchor = "<a target=\"_blank\" href=\"".$linkify."\">".$link_name."</a>";
	return $anchor;
}

function my_wordwrap($str, $maxLength=80, $char="\n") {
    $wordEndChars = array(" ", "\n", "\r", "\f", "\v", "\0");
    $count = 0;
    $newStr = "";
    $openTag = false;
    for($i=0; $i<strlen($str); $i++){
        $newStr .= $str{$i};

        if($str{$i} == "<"){
            $openTag = true;
            continue;
        }
        if(($openTag) && ($str{$i} == ">")){
            $openTag = false;
            continue;
        }

        if(!$openTag){
            if(!in_array($str{$i}, $wordEndChars)){//If not word ending char
                $count++;
                if($count==$maxLength){//if current word max length is reached
                    $newStr .= $char;//insert word break char
                    $count = 0;
                }
            }else{//Else char is word ending, reset word char count
                    $count = 0;
            }
        }

    }//End for
    return $newStr;
}

function hide_quoted_text($message_body) {

    $lines = preg_split("/\\n/", $message_body, -1, PREG_SPLIT_DELIM_CAPTURE);
    $formatted_text = "";
    //print_r($lines);
    $is_quote = false;
    for ($i = 0 ; $i < count($lines) ; $i++) {
         if (substr($lines[$i], 0, 1) == ">") {
            if (!$is_quote) {
               $is_quote = true;
               $formatted_text .= "</p><blockquote>";
            }
         } else if ($is_quote  && substr($lines[$i - 1], 0, 1) !== ">") {
               $is_quote = false;
               $formatted_text .= "</blockquote><p>";
         }
         $formatted_text .= str_replace(">", " ", $lines[$i])."\n";
     }
     return $formatted_text;
}

function shorten_url($string, $length=50, $link="SIMPLE", $redir="url.php")
	{
	if (!function_exists('kuerzen')) {
	function kuerzen($string,$length)
		{
		$returner = $string;
		if (strlen($returner) > $length)
			{
			$url = preg_match("=[^/]/[^/]=",$returner,$treffer,PREG_OFFSET_CAPTURE);
			$cutpos = $treffer[0][1]+2;
			$part[0] = substr($returner,0,$cutpos);
			$part[1] = substr($returner,$cutpos);

			$strlen1 = $cutpos;
			if ($strlen1 > $length) return substr($returner,0,$length-3).'...';
			$strlen2 = strlen($part[1]);
			$cutpos = $strlen2-($length-3-$strlen1);
			$returner = $part[0].'...'.substr($part[1],$cutpos);
			}
		return $returner;
		}
	}

	// strtoupper() casts TRUE to string "1" and FALSE to string '' (empty)
	// this line lets us maintain backwards compatibility and is handled in the switch
	$link=strtoupper($link);
	$pattern = '#(^|[^\"=]{1})(https?://|ftp://|mailto:|news:)([^\s<>\)]+)([\s\n<>\)]|$)#sme';

	switch (TRUE)
		{
		case ($link === "NONE" OR $link === ''): // just show the URL truncated
			$string = preg_replace($pattern,"kuerzen('$2$3',$length)",$string);
			break;
		case ($link === "SIMPLE" OR $link === '1'): // builds basic a href link showing truncated URL
			$string = preg_replace($pattern,"'$1<a href=\"$2$3\" title=\"$2$3\" target=\"_blank\">'.kuerzen('$2$3',$length).'</a>$4'",$string);
			break;
		case ($link === "GET"): // allows passing url to a redirecting file via GET - use redir param with format "url.php?url="
			$string = preg_replace($pattern,"'$1<a href=\"$redir$2$3\" title=\"$2$3\" target=\"_blank\">'.kuerzen('$2$3',$length).'</a>$4'",$string);
			break;
		case ($link === "POST"): // send URL via POST (builds form and embeds javascript submit script in link) - use redir param with format "url.php"
			// here we use preg_match_all() to be able to
			// build multiple forms (since each form needs
			// a unique name)
			preg_match_all($pattern,$string,$matches,PREG_SET_ORDER);
			foreach ($matches as $key=>$ul) {
				// use the key as the unique formname
				$form = "<form name=\"sub$key\" method=\"post\" action=\"$redir\"><input type=\"hidden\" id=\"up\" name=\"up\" value=\"$ul[2]$ul[3]\"></FORM>";
				$string_new .= "$form $ul[1]<a href=\"javascript:void(0)\" onclick=\"document.sub$key.submit(); return false;\" target=\"_blank\">" . kuerzen("$ul[2]$ul[3]",$length)."</a>$ul[4]";
			}
			$string = $string_new;
			break;
		}
	return $string;
	}
