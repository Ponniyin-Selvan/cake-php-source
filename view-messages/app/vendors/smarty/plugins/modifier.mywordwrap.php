<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty wordwrap modifier plugin
 *
 * Type:     modifier<br>
 * Name:     wordwrap<br>
 * Purpose:  wrap a string of text at a given length
 * @link http://smarty.php.net/manual/en/language.modifier.wordwrap.php
 *          wordwrap (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_mywordwrap($str,$max=80,$break="\n",$cut=false)
{
  // create array by deviding at each occurrence of "<a"
  $arr = explode('<a', $str);

  // break up long words in $arr[0] since
  // it will never contain a hyberlink
  $arr[0] = preg_replace('/([^\s]{'.$max.'})/i',"$1$break",$arr[0]);

  // run loop to devide remaining elements
  for($i = 1; $i < count($arr); $i++) {

    // devide each element in $arr at each occurrence of "</a>"
    $arr2 = explode('</a>', $arr[$i]);

    // break up long words in $arr2 that does not
    // contain hyberlinks
    $arr2[1] = preg_replace('/([^\s]{'.$max.'})/i',"$1$break",$arr2[1]);

    // rejoin $arr2 and assign as element in $arr
    $arr[$i] = join('</a>', $arr2);
  }
  // rejoin $arr to string and return it
  return join('<a', $arr);
}

?>
