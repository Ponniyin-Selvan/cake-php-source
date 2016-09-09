<?php
/* SVN FILE: $Id: bake_task.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Base class for bake tasks.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007,	Cake Software Foundation, Inc.
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
 * @subpackage		cake.cake.scripts.bake
 * @since			CakePHP(tm) v 1.2
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class BakeTask {

	/**
	 * Override this function in subclasses to implement the task logic.
	 * @param array $params The command line params (without script and task name).
	 */
	function execute($params) {
		// empty
	}

	/**
	 * Override this function in subclasses to provide a help message for your task.
	 */
	function help() {
		echo "There is no help available for the specified task.\n";
	}
}