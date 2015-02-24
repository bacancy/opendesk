<?php

/**
 * IndexController is the default controller for this application
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class StaticController extends PS_Controller_StaticAction 
{
        
    function init() {                        
                
        parent::init();        
    }
    
    /**    
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/index
     *
     * @return void
     */
    public function indexAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);
        
        $this->view->siteTitle = $objTranslate->translate('Home');
       
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;                
        $objError->message  = "";       
    }                
    
    function aboutusAction()
    {
        $this->view->message = "";
        $this->view->messageType = "";
    }
    
    function contactusAction()
    {       
        $this->view->message = "";
        $this->view->messageType = "";
    }

    function howtogetjobAction()
    {
        $this->view->message = "";
        $this->view->messageType = "";
    }
}
