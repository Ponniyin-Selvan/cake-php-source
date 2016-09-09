<?php

/**
 * expects $params to hold at least values for $var and $value, and optionally
 * $glue. Value should look like "key1=>value1,key2=>value2", or if $glue is
 * passed in , it will split on $glue rather than commas, useful if commas are
 * needed inside your keys or vals. Will assign the associative array that is
 * created from $value to the variable name contained in $var
 */
function smarty_function_assign_assoc($params, &$smarty)
{
	//extracts variables passed in
	extract($params);
    $assoc_array = array();

    if(!isset($value) ||  !isset($var))
    {
    	return;
    } 
    
    if(!isset($glue))
    {
    	$glue = ',';
    }
    $key_val_pairs=explode($glue,$value);
    foreach($key_val_pairs as $pair){
   	   list($key,$val) = explode('=>',$pair);
   	  $assoc_array[trim($key)] = trim($val);
    }
	$smarty->assign($var,$assoc_array);

}

?>