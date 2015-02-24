<?php
class PS_Controller_ContractorAction extends Zend_Controller_Action {
	
    private $_extraVar = array();
    private $_siteVar = array();    
	
    function init() {    		
        //Get model ,controlller ,action   
        $moduleName     = $this->getRequest()->getModuleName();
        $actionName     = $this->getRequest()->getActionName();
        $controllerName = $this->getRequest()->getControllerName();
        
        //Set Layout 
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('contractor');
        
        //Get General Options   
        $objGeneralModel = new Models_GeneralSetting;
        $objGeneralSelect = $objGeneralModel->getGeneralSettingRec();
        foreach($objGeneralSelect as $GeneralSettings) {
                $this->_siteVar[$GeneralSettings['settings_varname']] = $GeneralSettings['settings_value'];
        }
        $this->view->siteTitle = self::getSiteVar('SITE_TITLE').' | '.$moduleName.' | '.$actionName.' | '.$controllerName;
        
        //check for Authorized or Not  
        $this->doAdminAuthorisation();
        
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $this->view->objSess = $objSess;
        
        parent::init();
        unset($objGeneralModel,$objGeneralSelect);
    }            
        
    protected function getExtraVar($key=null) {
        return (($key==null) ? $this->_extraVar : ((isset($this->_extraVar[$key])) ? $this->_extraVar[$key] : null));
    }
    
    protected function getSiteVar($key=null) {
        return (($key==null) ? $this->_siteVar : ((isset($this->_siteVar[$key])) ? $this->_siteVar[$key] : null));
    }
    
    function doAdminAuthorisation() {
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
		
        if(!isset($objSess->isUserLogin) || $objSess->isUserLogin != true || $objSess->user_type != '1')
        {
            $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
            $objError = new Zend_Session_Namespace(PS_App_Error);
            $objError->message = formatErrorMessage($objTranslate->_('You are not Authorised!'));
            $this->_redirect("/default/index/login/");
        }
    }
    
    
}
