<?php
class PS_Controller_Action extends Zend_Controller_Action {
	
    private $_extraVar = array();
    private $_siteVar = array();
	
    function init() {                
        //Get model ,controlller ,action   
        $moduleName     = $this->getRequest()->getModuleName();
        $actionName     = $this->getRequest()->getActionName();
        $controllerName = $this->getRequest()->getControllerName();
        
        //Set Layout 
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('layout');
        
        //Get General Options   
        $objGeneralModel = new Models_GeneralSetting;
        $objGeneralSelect = $objGeneralModel->getGeneralSettingRec();
        foreach($objGeneralSelect as $GeneralSettings) {
                $this->_siteVar[$GeneralSettings['settings_varname']] = $GeneralSettings['settings_value'];
        }
        $this->view->siteTitle = self::getSiteVar('SITE_TITLE').' | '.$moduleName.' | '.$actionName.' | '.$controllerName;
        
        //check for user is already loged in 
        if($actionName != 'logout'){
            $this->isUserAlreadyLoggedIn();        
        }
        
        parent::init();
        unset($objGeneralModel,$objGeneralSelect);
    }            
        
    protected function getExtraVar($key=null) {
        return (($key==null) ? $this->_extraVar : ((isset($this->_extraVar[$key])) ? $this->_extraVar[$key] : null));
    }
    
    protected function getSiteVar($key=null) {
        return (($key==null) ? $this->_siteVar : ((isset($this->_siteVar[$key])) ? $this->_siteVar[$key] : null));
    }
    
    function isUserAlreadyLoggedIn() {
        $objSess = new Zend_Session_Namespace(PS_App_Auth);		
        if(isset($objSess->isUserLogin) && $objSess->isUserLogin == true)
        {
            if($objSess->user_type == '1'){
                $this->_redirect("/contractor/index/index");
            } elseif ($objSess->user_type == '2') {
                $this->_redirect("/employer/index/index");
            }elseif ($objSess->user_type == '3') {
                $this->_redirect("/agency/index/index");
            } else {
                $objError->message = $objTranslate->translate('invalid user!');
                $objError->messageType = 'error';
                $this->_redirect("/default/index/index");
            }
        }
    }
    
    
}
