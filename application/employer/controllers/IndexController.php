<?php

/**
 * IndexController is the default controller for this application
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Employer_IndexController extends PS_Controller_EmployerAction 
{
	
	/**
     * The "index" action is the default action for all controllers -- the 
     * landing page of the site.
     *
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /index/
     *   /index/index
     *
     * @return void
     */
    public function indexAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('employer');
    	
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;                
        $objError->message  = "";    	
    }
    
    
    /**    
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/profile
     *
     * @return void
     */
    public function profileAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('My Profile');
        
        $objModel = new Models_User();
        $profileData = $objModel->getEmployerInfo($objSess->user_id);
        $contractData = $objModel->getEmployerContractsInfo($objSess->user_id);
        
        $this->view->profileData = $profileData;
        $this->view->contractData = $contractData;        
        $this->view->sidebar = $this->view->partial('index/_sidebar.phtml', array());
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;                
        $objError->message  = "";        
    }
    
    
     /**    
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/editprofile
     *
     * @return void
     */
    public function editprofileAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('My Profile');
        
        $objModel = new Models_User();
        $objForm = new Models_Form_Profile();               
        $profileData = $objModel->getEmployerInfo($objSess->user_id);
        
		$objForm->country->addMultiOptions($objModel->getcountry_list());
		
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {                                                                
                if(isset($_FILES['portrait'])){
                    //manage upload profile picture 
                    $upload = new Zend_File_Transfer_Adapter_Http();			
                    $upload->setDestination(ASSETS_ROOT_PATH);			                    
                    $newFileName = uniqid('portrait_', FALSE).'.'.pathinfo($upload->getFileName() ,PATHINFO_EXTENSION);    					
                    $upload->addFilter('Rename', $newFileName);                    
                    if($upload->receive()){
                        $formData['portrait'] = $newFileName;
                        //remove old file 
                        if( $profileData['portrait']!="" && file_exists(ASSETS_ROOT_PATH.$profileData['portrait']) ){
                            unlink(ASSETS_ROOT_PATH.$profileData['portrait']);
                        }
                    }
                }
                
                $isvalid = $objModel->updateData( $formData, $objSess->user_id);
                if($isvalid){                                                                                                                        
                    $objError->message = $objTranslate->translate('profile updated!');
                    $objError->messageType = 'success';
                    $this->_redirect("/employer/index/profile");
                }
            }else {	
                $objForm->populate($formData);
                $objError->message = formatErrorMessage ( $objForm->getMessages () );
                $objError->messageType = 'error';
            }			
        } else {
            $objForm->populate($profileData);
        }
        
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objForm = $objForm;                        
        $this->view->profileData = $profileData;
        $this->view->sidebar = $this->view->partial('index/_sidebar.phtml', array());                
    }
    
    
	
}
