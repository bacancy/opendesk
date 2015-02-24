<?php

/**
 * JobController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */

class Contractor_JobController extends PS_Controller_ContractorAction 
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
        $this->view->siteTitle = $objTranslate->translate ( 'Find jobs' );
        
        $objModel = new Models_Job();
        $objForm = new Models_Form_Findjob();        
        
        $objJobcategory = new Models_Jobcategory();        
        $objForm->category->addMultiOptions($objJobcategory->getListdata());
		
        $jobType = array('1'=>'fixed cost job' ,'2'=>'hourly job');        
        
        $CurrentPageNo = $this->_getParam ( 'page' );
        $CurrentPageNo = ($CurrentPageNo == '') ? '1' : $CurrentPageNo;
        $this->view->current_page = $CurrentPageNo;			
        $sortby = trim ( $this->_getParam ( 'sortby' ) );
        $pagingExtraVar = array ();
        $searchText = '';
        $searchType = '';

        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost();
            if(isset($formData['title']))
                    $searchText = $formData['title'];

            if(isset($formData['category']))
                    $searchType = $formData['category'];																

            if ($objForm->isValid ( $formData )) {
                    $pagingExtraVar = array ('txtsearch' => $searchText, 'searchuser_type' => $searchType, 'sortby' => $sortby );
            }
        }

        if ($sortby != '')
                $arrSortBy = array ('sortby' => $sortby );
        else
                $arrSortBy = array ();				
		
        $objSelect = $objModel->getjobList( $searchText ,$searchType ,$sortby );
		
		$objError->message = "We have found ".count($objSelect)." Job(s) for you";
		$objError->messageType = "success";
		
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
        $this->view->objForm = $objForm;
        $this->view->sortby = $sortby;	
        $this->view->jobType = $jobType;     
    }
	
	function applyAction()
	{
		$objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'My - jobs' );
        
        $objModel = new Models_Bid();
		
		//here we are decoding the job id.
		$job_id = (int)base64_decode($objRequest->jobid);
		//here we are checking job id is available or not, if it is not available then we will show error message
		if($job_id=="")
		{
			$objError->message = $objTranslate->translate('Some thing going wrong');
			$objError->messageType = 'Error';
			$this->_redirect("/contractor/job/index");
		}
		
		//these fields are important to make as an array.
		$data['job_id'] = $job_id;
		$data['contractor_id'] = $objSess->user_id;
		
		//array passing to model there data will insert into bid table.
		$res = $objModel->saveData($data);
		
		//here checking res is true or false.
		if($res)
		{
			$objError->message = $objTranslate->translate('You have applied Successfully!');
			$objError->messageType = 'success';
			$this->_redirect("/contractor/job/index");
		}
		else
		{
			$objError->message = $objTranslate->translate('Some Problem Occurred');
			$objError->messageType = 'Error';
			$this->_redirect("/contractor/job/index");
		}		
	}
	
	function appliedjobsAction()
	{
		$objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'My - jobs' );
        
        $objModel = new Models_Bid();        
        
        $jobType = array('1'=>'fixed cost job' ,'2'=>'hourly job');        
        
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

        $objSelect = $objModel->getjobList( $searchText ,$searchType ,$sortby );

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
	}
}
