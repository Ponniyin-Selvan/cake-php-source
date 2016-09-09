<?php
/* SVN FILE: $Id: aro.php 4605 2007-03-09 23:26:37Z phpnut $ */

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
 * @subpackage		cake.cake.libs.controller.components.dbacl.models
 * @since			CakePHP(tm) v 0.10.0.1232
 * @version			$Revision: 4605 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-03-09 17:26:37 -0600 (Fri, 09 Mar 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Short description for file.
 *
 * Long description for file
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller.components.dbacl.models
 *
 */
class Aro extends AclNode {

/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $name = 'Aro';
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $hasAndBelongsToMany = array('Aco' => array('with' => 'Permission'));
}

?>