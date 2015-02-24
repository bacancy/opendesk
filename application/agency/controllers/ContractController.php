<?php

/**
 * contractController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Agency_ContractController extends PS_Controller_AgencyAction 
{
	// LOAD CONTROLLER CONSTRUCT
	/*public function init()
    {
		//we are creating nll cookie
        
		setcookie("menu_name", "", time()+2592000, "/");
		$_COOKIE['menu_name'] = "Contract";
	}*/
		
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
        $this->view->siteTitle = $objTranslate->translate ( ':: Selected Contractor view list ::' );
        
        $objModel = new Models_Agency();
        //$objForm = new Models_Form_Distributor();
        //$objForm->Distributorsearch();                
        $jobStatus = array('1'=>'active','2'=>'paused','3'=>'closed','4'=>'end');
        $jobStatusAlert = array('1'=>'success','2'=>'warning','3'=>'danger');
        
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
		
		$contractor_id = $objRequest->contractor_id;
        $objSelect = $objModel->getList( $searchText ,$searchType ,$sortby ,$contractor_id );
		
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
        $this->view->siteTitle = $objTranslate->translate ( 'contractor - contract detail' );
        
        $objModel = new Models_Contract();
        $arrData = $objModel->fetchEntry($objRequest->id);
        //_pr($arrData ,1);
        
        $jobStatus = array('1'=>'active','2'=>'paused','3'=>'closed');
        $jobStatusAlert = array('1'=>'success','2'=>'warning','3'=>'danger');
        
        $jobType = array('1'=>'fixed cost job' ,'2'=>'hourly job');
        
		$this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
		$objError->message = "";
        $objError->messageType = '';
        $this->view->arrData = $arrData;
        $this->view->jobStatus = $jobStatus;
        $this->view->jobStatusAlert = $jobStatusAlert;
        $this->view->jobType = $jobType;
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
        $this->view->siteTitle = $objTranslate->translate('Agency - add contract');
        
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
                    $objError->message = $objTranslate->translate('contract started!');
                    $objError->messageType = 'success';
                    $this->_redirect("/agency/job");
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
        $this->view->siteTitle = $objTranslate->translate('Agency - edit contract');
        
        $objModel = new Models_Contract();
        $objForm = new Models_Form_Contract();                       
        $arrData = $objModel->fetchContract($objRequest->id);                
                        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {                                                        
                $isvalid = $objModel->updateData( $formData, $objRequest->id);
                if($isvalid){                                                                                                                        
                    $objError->message = $objTranslate->translate('contract updated!');
                    $objError->messageType = 'success';
                    $this->_redirect("/agency/contract/index");
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
	
	
	public function changestatusAction()
	{
		$objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
		
		$objModel = new Models_Contract();
		
		$data['status'] = $objRequest->status;
        $result = $objModel->updateData($data,$objRequest->contract_id);
		
		if($result)
		{
			$objError->message = $objTranslate->translate('Updated Successfully');
            $objError->messageType = 'success';
            $this->_redirect("/agency/contract/detail/id/".$objRequest->contract_id);
		}
		else
		{
			$objError->message = $objTranslate->translate('Not updated!');
            $objError->messageType = 'error';
            $this->_redirect("/agency/contract/detail/id/".$objRequest->contract_id);
		}
	}
}