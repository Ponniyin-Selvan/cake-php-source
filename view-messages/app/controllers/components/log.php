<?php
define('LOG4PHP_CONFIGURATION',
'vendors'.DS.'log4php'.DS.'config'.DS.'MyLogger.xml');
vendor('log4php'.DS.'LoggerManager');

class LogComponent extends Object{

       var $controller;
       var $logger;

       /*
        * init logger object
        */
   function startup( &$controller ) {
               $this->controller = &$controller;
               $this->init();
   }

   /*
    * Init Log component
    */
   function init(){
       if ($this->logger == null){

               $prefix = '('. $_SERVER['QUERY_STRING'] .')';
               $this->logger =& LoggerManager::getLogger( $prefix );
       }
   }

   /*
    * Debug
    * @param String text
    */
   function debug($text){
       $this->init();
       $this->logger->debug($text);
   }

   /*
    * info
        * @param String text*
    */
   function info($text){
       $this->init();
       $this->logger->info($text);
   }

   /*
    * warn
    * @param String text
    */
   function warn($text){
       $this->init();
       $this->logger->warn($text);
   }

   /*
    * error
    * @param String text
    */
       function error($text){
               $this->init();
       $this->logger->error($text);
   }

   /*
    * fatal
    * @param String text
    */
   function fatal($text){
       $this->init();
       $this->logger->fatal($text);
   }

   /*
    * Shutdown
    */
   function shutdown(){
       LoggerManager::shutdown();
   }
}
?>