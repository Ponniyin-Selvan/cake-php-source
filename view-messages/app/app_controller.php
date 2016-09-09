<?php
/* SVN FILE: $Id: app_controller.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */


class AppController extends Controller {
    var $view = 'Smarty';
    var $helpers = array('Html', 'Javascript', 'Time', 'Paginator', 'PageNav', 'Date', 'Link', 'SocialLinks', 'BrowserDetect');

    var $project_name = null;

    var $group_name = null;
    var $group_title = null;


    function set_project_settings($settings) {

        foreach($settings as $key => $value) {
			$this->$key = $value;
			$this->set($key, $value);
		}
    }

    /**
    * Called before the controller action.  Overridden in subclasses.
    *
    */
    function beforeFilter() {

        parent::beforeFilter();

		//print_r("IE5 ? ".$this->BrowserDetect->is_IE5());
        $this->project = $_SERVER['SERVER_NAME'];

		require(APP.DS.'config'.DS.'groups-config.php');

		$this->set_project_settings($application_config['global']); // set global settings
		$this->set_project_settings($application_config[$this->project]); // project specific settings

        // common view
        $common_view_path = VIEWS."common" .DS;
        // group specific views
        $group_view_path = VIEWS.$this->group_name.DS;

        // group specific views takes precedence
        $viewPaths = array($group_view_path, $common_view_path);

        // common helper folder
        $common_helper_path = $common_view_path. 'helpers'.DS;
        // group helper folder
        $group_helper_path = $group_view_path.$this->group_name.'helpers'.DS;

        // group specific folder takes precedence
        $helperPaths = array($group_helper_path, $common_helper_path);

	    $configure =& Configure::getInstance();
        // order of precedence group, common and general
	    $configure->viewPaths = array_merge($viewPaths, $configure->viewPaths);
	    $configure->helperPaths = array_merge($helperPaths, $configure->helperPaths);
	    $this->set("scache",
               array('cache' => '+365 days', 'plugin' => $this->group_name));
	    $this->set("wcache",
               array('cache' => '+7 days', 'plugin' => $this->group_name));
    }
}
?>