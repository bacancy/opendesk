<?php

///////////////////////
// SITE CONFIGURATION//
///////////////////////
$path_http = pathinfo('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
//define("SERVER_PATH", $path_http["dirname"]."/"); 								// server path is deined here
$arrDirPath = explode("/", $path_http["dirname"]);
if($arrDirPath[count($arrDirPath)-1] == "admin" || $arrDirPath[count($arrDirPath)-1] == "trunk"){
        define("SERVER_ROOT_PATH", substr(getcwd(), 0, (strlen(getcwd())-strlen($arrDirPath[count($arrDirPath)-1])))); // server root path is deined here
        $serverPath = $arrDirPath;
        array_pop($serverPath);
        $serverUrl = implode("/",$serverPath);
        define("SERVER_PATH", $serverUrl."/"); 		 					// server path is deined here
}else{
        define("SERVER_ROOT_PATH", getcwd()."/"); 		  		// server root path is deined here
        $serverUrl = implode("/",$arrDirPath);
        define("SERVER_PATH", $serverUrl."/"); 								// server path is deined here
        $path_https = pathinfo('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
}

$path_https = pathinfo('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
define("SERVER_SSL_PATH", $path_https["dirname"]."/");


    /* Site Namespace Variables */
    define('PS_App_DbAdapter','DbAdapter');

    define('PS_App_Zend_Translate','Zend_Translate');
    define('PS_App_Error','PS_Error');
    define('PS_App_Auth','PS_Auth');
    define('PS_App_Configuration','configuration');

    define('PS_Front_App_Auth','PS_Front_App_Auth');

    /* Site Path Variables */
    #---  STYLE path ---#
    define('STYLE_PATH',SERVER_PATH.'css/');
    define('STYLE_ROOT_PATH',SERVER_ROOT_PATH.'css/');	

    #---  IMAGE path ---#
    define('IMAGE_PATH',SERVER_PATH.'img/');
    define('IMAGE_ROOT_PATH',SERVER_ROOT_PATH.'img/');

    #---  JAVASCRIPT path ---#
    define('JS_PATH',SERVER_PATH.'js/');
    define('JS_ROOT_PATH',SERVER_ROOT_PATH.'js/');
        
    #---  Assets directory path ---#    
    define('ASSETS_PATH',SERVER_PATH.'assets/');
    define('ASSETS_ROOT_PATH',SERVER_ROOT_PATH.'assets/');
    
    #---   Screenshot directory path ---#    
    define('SCREENSHOT_PATH',ASSETS_PATH.'screenshot/');
    define('SCREENSHOT_ROOT_PATH',ASSETS_ROOT_PATH.'screenshot/');
    
    #---  PHPThumb library path ---#    
    define('PHPTHUMB_PATH',SERVER_PATH.'phpThumb/phpThumb.php?src=');
    define('PHPTHUMB_ROOT_PATH',SERVER_ROOT_PATH.'phpThumb/phpThumb.php?src=');
        
    #---  tcpdf library path ---#    
    define('TCPDF_PATH',SERVER_PATH.'tcpdf/tcpdf.php');
    define('TCPDF_ROOT_PATH',SERVER_ROOT_PATH.'tcpdf/tcpdf.php');
    
    
    // PAGING PARAM SETTINGS
    define('TOTAL_RECORDS_PER_PAGE',  '2');
    define('TOTAL_PAGE_IN_GROUP',  '3');

    #---  SYSTEM EMAIL  ---#
    define("SYSTEM_EMAIL","noreply@opendesk.com");
    
    