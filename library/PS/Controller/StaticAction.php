<?php
class PS_Controller_StaticAction extends Zend_Controller_Action {
	
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
                                
        parent::init();
        unset($objGeneralModel,$objGeneralSelect);
    }            
        
    protected function getExtraVar($key=null) {
        return (($key==null) ? $this->_extraVar : ((isset($this->_extraVar[$key])) ? $this->_extraVar[$key] : null));
    }
    
    protected function getSiteVar($key=null) {
        return (($key==null) ? $this->_siteVar : ((isset($this->_siteVar[$key])) ? $this->_siteVar[$key] : null));
    }        
    
    
}
