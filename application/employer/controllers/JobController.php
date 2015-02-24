<?php

/**
 * JobController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Employer_JobController extends PS_Controller_EmployerAction 
{
	
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /job/
     *   /job/index
     *
     * @return void
     */
    public function indexAction() 
    {
        $objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'employer - jobs' );
        
        $objModel = new Models_Job();
        //$objForm = new Models_Form_Distributor();
        //$objForm->Distributorsearch();
        
        $jobType = array('1'=>'fixed cost job' ,'2'=>'hourly job');
        $objCurrency = new Models_Currency();        
        $currencySymbol = $objCurrency->getCurrencySymbol();
        
        
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
        $objPaginator->setItemCountPerPage ( $this->getSiteVar ( 'PAGING_VARIABLE' ) );
        $objPaginator->setPageRange ( $this->getSiteVar ( 'TOTAL_PAGE_IN_GROUP' ) );
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
        $this->view->jobType = $jobType;
        $this->view->currencySymbol = $currencySymbol;
        //$this->view->objForm = $objForm;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /job/
     *   /job/add
     *
     * @return void
     */
    public function addAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('employer - post Job');                
        
        $objModel = new Models_Job();
        $objForm = new Models_Form_Job();                       
        
        $objJobcategory = new Models_Jobcategory();        
        $objForm->category_id->addMultiOptions($objJobcategory->getListdata());
        
        $objCurrency = new Models_Currency();        
        $objForm->currency_id->addMultiOptions($objCurrency->getCurrencylist());
        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {
                if($formData['type'] == '2'){
                    $objForm->hourly_rate->setRequired(true);
                }
                $formData['employer_id'] = $objSess->user_id;
                $isvalid = $objModel->saveData( $formData );
                if($isvalid){                                                                                                                                            
                    //send notification mail  
                    $bodyHtml = $this->view->partial('email/_newjob.phtml', array('data'=>$formData));                                        
                    $mail = new Zend_Mail('utf-8');                                      
                    $res = $mail->addTo($objSess->email)
                        ->setSubject('connectica - newjob confirmation')
                        ->setFrom('support@connectica.de','Connectica support')
                        ->setBodyHtml($bodyHtml)
                        ->send();
                    
                    $objError->message = $objTranslate->translate('job posted succesfully');
                    $objError->messageType = 'success';
                    $this->_redirect("/employer/job/index");
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
     *   /job/
     *   /job/edit
     *
     * @return void
     */
    public function editAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('employer - edit job');
        
        $objModel = new Models_Job();
        $objForm = new Models_Form_Job();                       
        $arrData = $objModel->fetchEntry($objRequest->id);
        
        $objJobcategory = new Models_Jobcategory();        
        $objForm->category_id->addMultiOptions($objJobcategory->getListdata());
        
        $objCurrency = new Models_Currency();        
        $objForm->currency_id->addMultiOptions($objCurrency->getCurrencylist());                    
        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if($formData['type'] == 1){                    
                $objForm->removeElement('hourly_rate');
            }
            if ($objForm->isValid ( $formData )) {                                                                                        
                $isvalid = $objModel->updateData( $formData, $objRequest->id);
                if($isvalid){
                    //send notification mail  
                    /*$bodyHtml = $this->view->partial('email/_editjob.phtml', array('data'=>$formData));                                        
                    $mail = new Zend_Mail('utf-8');                                      
                    $res = $mail->addTo($objSess->email)
                        ->setSubject('connectica - job is modified')
                        ->setFrom('support@connectica.de','Connectica support')
                        ->setBodyHtml($bodyHtml)
                        ->send();*/
                    
                    $objError->message = $objTranslate->translate('job updated!');
                    $objError->messageType = 'success';
                    $this->_redirect("/employer/job/index");
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
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /job/
     *   /job/delete
     *
     * @return void
     */
    public function deleteAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();
        
        $objModel = new Models_Job();        	
        $objModel_bid = new Models_Bid();
		$objModel_contractor = new Models_Contract();
        
		$objModel_contractor->deleteDatabyColumn ($objRequest->id);
		$objModel_bid->deleteDatabyColumn($objRequest->id);
        $objModel->deleteData ($objRequest->id);
		
        $objError->message = $objTranslate->translate('job deleted!');
        $objError->messageType = 'success';
        $this->_redirect("/employer/job/index");
        
        exit;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /job/
     *   /job/assign
     *
     * @return void
     */
    public function assignAction() 
    {
        $objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'job - assign' );
        
        $objModel = new Models_User();
        //$objForm = new Models_Form_Distributor(); 
        //$objForm->Distributorsearch();                                
        
        $job_id = $objRequest->id;
        $userType = array('1'=>'contractor' ,'2'=>'employer','3'=>'agency');
        
        $objContract = new Models_Contract();
        $contractStatus = $objContract->checkContract($job_id);
        
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

        $objSelect = $objModel->getContractorList( $searchText ,$searchType ,$sortby ,$job_id );

        $objPaginator = Zend_Paginator::factory ( $objSelect );
        $objPaginator->setItemCountPerPage ( $this->getSiteVar ( 'PAGING_VARIABLE' ) );
        $objPaginator->setPageRange ( $this->getSiteVar ( 'TOTAL_PAGE_IN_GROUP' ) );
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
        $this->view->userType = $userType;
        $this->view->job_id = $job_id;
        $this->view->contractStatus = $contractStatus;
        //$this->view->objForm = $objForm;
    }
    
    
	
}
