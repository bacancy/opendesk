<?php

/**
 * contractController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Employer_ContractController extends PS_Controller_EmployerAction 
{
	
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /contract/
     *   /contract/index
     *
     * @return void
     */
    public function indexAction() 
    {
        $objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'employer - contract' );
        
        $objModel = new Models_Contract();
        //$objForm = new Models_Form_Distributor();
        //$objForm->Distributorsearch();                
        $jobStatus = array('1'=>'active','2'=>'paused','3'=>'closed','4'=>'ended');
        $jobStatusAlert = array('1'=>'success','2'=>'warning','3'=>'danger','4'=>'default');
        
        $CurrentPageNo = $this->_getParam ( 'page' );
        $CurrentPageNo = ($CurrentPageNo == '') ? '1' : $CurrentPageNo;
        $this->view->current_page = $CurrentPageNo;			
        $sortby = trim ( $this->_getParam ( 'sortby' ) );
        $pagingExtraVar = array ();
        $searchText = '';
        $searchType = '';

        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if(isset($formData['txtsearch']))
                    $searchText = $formData['txtsearch'];

            if(isset($formData['searchtype']))
                    $searchType = $formData['searchtype'];																

            if ($objForm->isValid ( $formData )) {
                    $pagingExtraVar = array ('txtsearch' => $searchText, 'searchuser_type' => $searchType, 'sortby' => $sortby );
            }
        }

        if ($sortby != '')
                $arrSortBy = array ('sortby' => $sortby );
        else
                $arrSortBy = array ();				

        $objSelect = $objModel->getList( $searchText ,$searchType ,$sortby );

        $objPaginator = Zend_Paginator::factory ( $objSelect );
        $objPaginator->setItemCountPerPage ( 3 );
        $objPaginator->setPageRange ( 1 );
        $objPaginator->setCurrentPageNumber ( $this->_getParam ( 'page' ) );
        $this->view->pagingExtraVar = array_merge ( $this->getExtraVar (), $pagingExtraVar, $arrSortBy );
        $this->view->objPaginator = $objPaginator;
        $this->view->arrDataList = $objPaginator->getItemsByPage ( $objPaginator->getCurrentPageNumber () );
        unset ( $objModel, $objSelect, $objPaginator );		

        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->sortby = $sortby;
        $this->view->jobStatus = $jobStatus;
        $this->view->jobStatusAlert = $jobStatusAlert;
        //$this->view->objForm = $objForm;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /contract/
     *   /contract/detail
     *
     * @return void
     */
    public function detailAction() 
    {
        $objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'employer - contract detail' );
        
        $objModel = new Models_Contract();
        $objContractratingModel = new Models_Contractrating();
        $objForm = new Models_Form_Contractrating();
        $objForm->contractor();
        $arrData = $objModel->fetchEntry($objRequest->id);		        
        //_pr($arrData ,1);                         
        
        $jobStatus = array('1'=>'active','2'=>'paused','3'=>'closed','4'=>'ended');
        $jobStatusAlert = array('1'=>'success','2'=>'warning','3'=>'danger','4'=>'default');
        
        $jobType = array('1'=>'fixed cost job' ,'2'=>'hourly job');
        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {                
                $validator = new Zend_Validate_Db_RecordExists(array('table'=>'contract_rating','field' =>'contract_id'));
                if ($validator->isValid ($objRequest->id)) {                    
                    $isvalid = $objContractratingModel->updateData( $formData, $objRequest->id);
                }else{
                    $formData['contract_id'] = $objRequest->id;
                    $isvalid = $objContractratingModel->saveData( $formData );
                }                
                if($isvalid){                                        
                    $objError->message = $objTranslate->translate('Saved successfully!');
                    $objError->messageType = 'success';
                    $this->_redirect(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri());
                }
            }else {	
                $objForm->populate($formData);
                $objError->message = formatErrorMessage ( $objForm->getMessages () );
                $objError->messageType = 'error';
            }			
        } else {
            $objForm->populate($arrData);
        }
        
        $this->view->arrData = $arrData;
        $this->view->jobStatus = $jobStatus;
        $this->view->jobStatusAlert = $jobStatusAlert;
        $this->view->jobType = $jobType;
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objForm = $objForm;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /contract/
     *   /contract/add
     *
     * @return void
     */
    public function addAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('employer - add contract');
        
        $objModel = new Models_Contract();
        $objForm = new Models_Form_Contract();                                                       
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {
                $formData['contractor_id'] = $objRequest->contractor_id;
                $formData['employer_id'] = $objSess->user_id;
                $formData['job_id'] = $objRequest->job_id;                
                $isvalid = $objModel->saveData( $formData);
                if($isvalid){                                                                                                                        
                    //send notification mail  
                    $objUserModel = new Models_User();
                    $userData = $objUserModel->getUserInfo($objRequest->contractor_id);                    
                    $bodyHtml = $this->view->partial('email/_newcontract.phtml', array('contractData'=>$formData,'userData'=>$userData));                                                            
                    $mail = new Zend_Mail('utf-8');                                      
                    $res = $mail->addTo($userData['email'])
                        ->setSubject('connectica - newcontract started')
                        ->setFrom('support@connectica.de','Connectica support')
                        ->setBodyHtml($bodyHtml)
                        ->send();
                    
                    $objError->message = $objTranslate->translate('contract started!');
                    $objError->messageType = 'success';
                    $this->_redirect("/employer/contract/index");
                }
            }else {	
                $objForm->populate($formData);
                $objError->message = formatErrorMessage ( $objForm->getMessages () );
                $objError->messageType = 'error';
            }			
        }
        
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objForm = $objForm;                                        
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /contract/
     *   /contract/edit
     *
     * @return void
     */
    public function editAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('employer - edit contract');
        
        $objModel = new Models_Contract();
        $objForm = new Models_Form_Contract();                       
        $arrData = $objModel->fetchContract($objRequest->id);                
                        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {                                                        
                $isvalid = $objModel->updateData( $formData, $objRequest->id);
                if($isvalid){                                                                                                                        
                    //send notification mail  
                    $objUserModel = new Models_User();
                    $userData = $objUserModel->getUserInfo($arrData['contractor_id']);                    
                    $bodyHtml = $this->view->partial('email/_editcontract.phtml', array('contractData'=>$formData,'userData'=>$userData));                                                              
                    $mail = new Zend_Mail('utf-8');                                      
                    $res = $mail->addTo($userData['email'])
                        ->setSubject('connectica - contract updated')
                        ->setFrom('support@connectica.de','Connectica support')
                        ->setBodyHtml($bodyHtml)
                        ->send();
                    
                    $objError->message = $objTranslate->translate('contract updated!');
                    $objError->messageType = 'success';
                    $this->_redirect("/employer/contract/index");
                }
            }else {	
                $objForm->populate($formData);
                $objError->message = formatErrorMessage ( $objForm->getMessages () );
                $objError->messageType = 'error';
            }			
        } else {
            $objForm->populate($arrData);
        }
        
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objForm = $objForm;                                        
    }
	
	
	function deleteAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();        
        
        $objModel = new Models_Bid();        
        $result = $objModel->deleteDatabycontractor($objRequest->id);
		if($result)
		{
			$objError->message = $objTranslate->translate('contract removed from your account!');
			$objError->messageType = 'success';
			$this->_redirect("/employer/job/index");
		}
		else
		{
			$objError->message = $objTranslate->translate('Some error occored.');
			$objError->messageType = 'error';
			$this->_redirect("/employer/job/index");
		}
	}
}
