<?php

// php settings 
date_default_timezone_set('UTC');


// APPLICATION CONSTANTS - Set the constants to use in this application.
// These constants are accessible throughout the application, even in ini
// files. We optionally set APPLICATION_PATH here in case our entry point
// isn't index.php (e.g., if required from our test suite or a script). 
defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__));

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');
	
//if(!defined('APPLICATION_ENVIRONMENT')){
//	if($_SERVER['HTTP_HOST'] == 'localhost')
//		define('APPLICATION_ENVIRONMENT', 'development'); // development
//	else
//		define('APPLICATION_ENVIRONMENT', 'testing'); // development
//}


// APPLICATION VARIABLES
include_once APPLICATION_PATH . "/config/site_variables.php";

// FRONT CONTROLLER - Get the front controller.
// The Zend_Front_Controller class implements the Singleton pattern, which is a
// design pattern used to ensure there is only one instance of
// Zend_Front_Controller created on each request.
$frontController = Zend_Controller_Front::getInstance();

// CONTROLLER DIRECTORY SETUP - Point the front controller to your action
// controller directory.
$controllerDir = array(
	'admin'     =>  APPLICATION_PATH . '/admin/controllers',
	'default'   =>  APPLICATION_PATH . '/default/controllers',
    'contractor'   =>  APPLICATION_PATH . '/contractor/controllers',
    'employer'   =>  APPLICATION_PATH . '/employer/controllers',
	'agency'   =>  APPLICATION_PATH . '/agency/controllers'
);

$frontController->setControllerDirectory($controllerDir);

// APPLICATION ENVIRONMENT - Set the current environment
// Set a variable in the front controller indicating the current environment --
// commonly one of development, staging, testing, production, but wholly
// dependent on your organization and site's needs.
$frontController->setParam('env', APPLICATION_ENVIRONMENT);

// LAYOUT SETUP - Setup the layout component
// The Zend_Layout component implements a composite (or two-step-view) pattern
// In this call we are telling the component where to find the layouts scripts.
$options = array(
    'layoutPath' => APPLICATION_PATH . '/layouts/default',
);
Zend_Layout::startMvc($options);

// VIEW SETUP - Initialize properties of the view object
// The Zend_View component is used for rendering views. Here, we grab a "global"
// view instance from the layout object, and specify the doctype we wish to
// use -- in this case, XHTML1 Strict.
$view = Zend_Layout::getMvcInstance()->getView();
$view->doctype('XHTML1_TRANSITIONAL');
$view->setEscape("escapeString");


// It can be used throughout the applicatin
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

// CONFIGURATION - Setup the configuration object
// The Zend_Config_Ini component will parse the ini file, and resolve all of
// the values for the given section.  Here we will be using the section name
// that corresponds to the APP's Environment
$configuration = new Zend_Config_Ini(APPLICATION_PATH . '/config/application.ini', APPLICATION_ENVIRONMENT);


// DATABASE ADAPTER - Setup the database adapter
// Zend_Db implements a factory interface that allows developers to pass in an
// adapter name and some parameters that will create an appropriate database
// adapter object.  In this instance, we will be using the values found in the
// "database" section of the configuration obj.
//_pr($configuration->database->params->adapter,1);

$dbAdapter = Zend_Db::factory($configuration->database);

//$dbAdapterMaster = Zend_Db::factory($configuration->database->params->adapter,$configuration->database->params->db1);
//$dbAdapterSlave = Zend_Db::factory($configuration->database->params->adapter,$configuration->database->params->db2);


// DATABASE TABLE SETUP - Setup the Database Table Adapter
Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);


$objLocale = new Zend_Locale();
$objTranslate = new Zend_Translate('array', '../language/english.php', 'en');
$objTranslate->addTranslation('../language/spanish.php', 'es');

if (!$objTranslate->isAvailable($objLocale->getLanguage()))
{
	$objTranslate->setLocale('en');
} else {
	$objTranslate->setLocale('en');
}

// REGISTRY - setup the application registry
// An application registry allows the application to store application
// necessary objects into a safe and consistent (non global) place for future
// retrieval.  This allows the application to ensure that regardless of what
// happends in the global scope, the registry will contain the objects it
// needs.
$registry = Zend_Registry::getInstance();
$registry->set(PS_App_Configuration, $configuration);
$registry->set(PS_App_DbAdapter, $dbAdapter);
$registry->set(PS_App_Zend_Translate, $objTranslate);




$router = new Zend_Controller_Router_Rewrite();
$request =  new Zend_Controller_Request_Http();
$router->route($request);


// CLEANUP - remove items from global scope
// This will clear all our local bootsrap variables from the global scope of
// this script (and any scripts that called bootstrap).  This will enforce
// object retrieval through the Applications's Registry
unset($frontController, $view, $dbAdapter, $registry);
