<?php
/* SVN FILE: $Id: css.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.webroot
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Enter description here...
 */
	require(CONFIGS . 'paths.php');
	require(CAKE . 'basics.php');
	require(LIBS . 'folder.php');
	require(LIBS . 'file.php');
/**
 * Enter description here...
 *
 * @param unknown_type $path
 * @param unknown_type $name
 * @return unknown
 */
	function make_clean_css($path, $name) {
		 require(VENDORS . 'csspp' . DS . 'csspp.php');
		 $data  =file_get_contents($path);
		 $csspp =new csspp();
		 $output=$csspp->compress($data);
		 $ratio =100 - (round(strlen($output) / strlen($data), 3) * 100);
		 $output=" /* file: $name, ratio: $ratio% */ " . $output;
		 return $output;
	}
/**
 * Enter description here...
 *
 * @param unknown_type $path
 * @param unknown_type $content
 * @return unknown
 */
	function write_css_cache($path, $content) {
		 if (!is_dir(dirname($path))) {
			  mkdir(dirname($path));
		 }
		 $cache=new File($path);
		 return $cache->write($content);
	}

	if (preg_match('|\.\.|', $url) || !preg_match('|^ccss/(.+)$|i', $url, $regs)) {
		 die(__('Wrong file name.'));
	}

	$filename = 'css/' . $regs[1];
	$filepath = CSS . $regs[1];
	$cachepath = CACHE . 'css' . DS . str_replace(array('/','\\'), '-', $regs[1]);

	if (!file_exists($filepath)) {
		 die(__('Wrong file name.'));
	}

	if (file_exists($cachepath)) {
		 $templateModified=filemtime($filepath);
		 $cacheModified   =filemtime($cachepath);

		 if ($templateModified > $cacheModified) {
			  $output=make_clean_css($filepath, $filename);
			  write_css_cache($cachepath, $output);
		 } else {
			  $output = file_get_contents($cachepath);
		 }
	} else {
		 $output=make_clean_css($filepath, $filename);
		 write_css_cache($cachepath, $output);
	}
	header("Date: " . date("D, j M Y G:i:s ", $templateModified) . 'GMT');
	header("Content-Type: text/css");
	header("Expires: " . gmdate("D, j M Y H:i:s", time() + DAY) . " GMT");
	header("Cache-Control: cache"); // HTTP/1.1
	header("Pragma: cache");        // HTTP/1.0
	print $output;
?>