<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

vendor('formatting');
/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     smileys<br>
 * Date:     March 6, 2007
 * Purpose:  convert smilies to img tags
 * Input:<br>
 *         - contents = contents to replace
 *         - preceed_test = if true, includes preceeding break tags
 *           in replacement
 * Example:  {$text|smileys}
 * @version  1.0
 * @author   Jelena Pavlovic <jelena@arraystudio.com>
 * @param string
 * @return string
 */
function smarty_modifier_smileys($string) {
	return smileys_to_images($string);
}
?>