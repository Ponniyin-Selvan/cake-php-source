<?php
/* SVN FILE: $Id: app_model.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppModel extends Model{
	
/**
 * Sets the DataSource to which this model is bound
 *
 * @param string $dataSource The name of the DataSource, as defined in Connections.php
 * @return boolean True on success
 */
	function setDataSource($dataSource = null) {

		require(APP.DS.'config'.DS.'groups-config.php');
        $project = $_SERVER['SERVER_NAME'];
        $project_settings = $application_config[$project];
        $db_conn = $project_settings['db'];
		parent::setDataSource($db_conn);
	}
}
?>