<?php

/**
 * UsermessagesController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Contractor_UsermessagesController extends PS_Controller_ContractorAction 
{
	
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /usermessages/
     *   /usermessages/index
     *
     * @return void
     */
    public function indexAction() 
    {
        $objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'Messages' );
                                
        $CurrentPageNo = $this->_getParam ( 'page' );
        $CurrentPageNo = ($CurrentPageNo == '') ? '1' : $CurrentPageNo;
        $this->view->current_page = $CurrentPageNo;			        
        $pagingExtraVar = array ();
        
        $objModel = new Models_UserMessages();        
        $objSelect = $objModel->getList( $objRequest );
        
        $objPaginator = Zend_Paginator::factory ( $objSelect );
        $objPaginator->setItemCountPerPage ( 10 );
        $objPaginator->setPageRange ( $this->getSiteVar ( 'TOTAL_PAGE_IN_GROUP' ) );
        $objPaginator->setCurrentPageNumber ( $this->_getParam ( 'page' ) );
        $this->view->pagingExtraVar = array_merge ( $this->getExtraVar (), $pagingExtraVar );
        $this->view->objPaginator = $objPaginator;
        $this->view->arrDataList = $objPaginator->getItemsByPage ( $objPaginator->getCurrentPageNumber () );
        unset ( $objModel, $objSelect, $objPaginator );		

        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objSess = $objSess;
    }
    
    
    
	
}
