
<?php

/**
 * workdiaryController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Contractor_WorkdiaryController extends PS_Controller_ContractorAction 
{
	
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /workdiary/
     *   /workdiary/index
     *
     * @return void
     */
    public function indexAction() 
    {
        $objTranslate = Zend_Registry::get ( PS_App_Zend_Translate );        
        $objError = new Zend_Session_Namespace ( PS_App_Error );
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );
        $objRequest = $this->getRequest ();
        $this->view->siteTitle = $objTranslate->translate ( 'contractor - workdiary' );
        
        $objModel = new Models_Workdiary();
        $objContractModel = new Models_Contract();                       
        $arrContract = $objContractModel->fetchEntry($objRequest->contract_id);
        //_pr($arrContract,1);
        
        if( !isset($objRequest->contract_id) && $objRequest->contract_id == '' ){
            $objError->message = $objTranslate->translate('please select contract!');
            $objError->messageType = 'error';
            $this->_redirect("/contractor/contract/index");
        }                                
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
                    $pagingExtraVar = array ('txtsearch' => $searchText, 'searchuser_type' => $searchType, 'sortby' => $sortby);
            }
        }

        if ($sortby != '')
                $arrSortBy = array ('sortby' => $sortby );
        else
                $arrSortBy = array ();				

        $objSelect = $objModel->getList( $searchText ,$searchType ,$sortby ,$objRequest);

        $objPaginator = Zend_Paginator::factory ( $objSelect );
        $objPaginator->setItemCountPerPage ( 50 );
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
        $this->view->contract_id = $objRequest->contract_id;
        $this->view->arrContract = $arrContract;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /workdiary/
     *   /workdiary/add
     *
     * @return void
     */
    public function addAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('contractor - add workdiary');
        
        if( !isset($objRequest->contract_id) && $objRequest->contract_id == '' ){
            $objError->message = $objTranslate->translate('please select contract!');
            $objError->messageType = 'error';
            $this->_redirect("/contractor/contract/index");
        }
        
        $objModel = new Models_Workdiary();
        $objForm = new Models_Form_Workdiary();                       
        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {
                //manage upload screenshot  
//                if(isset($_FILES['screenshot'])){
//                    $upload = new Zend_File_Transfer_Adapter_Http();			
//                    $upload->setDestination(SCREENSHOT_ROOT_PATH);			                    
//                    $newFileName = uniqid('screenshot_', FALSE).'.'.pathinfo($upload->getFileName() ,PATHINFO_EXTENSION);    					
//                    $upload->addFilter('Rename', $newFileName);                    
//                    if($upload->receive()){
//                        $formData['screenshot'] = $newFileName;                        
//                    }
//                }
                
                $formData['contract_id'] = $objRequest->contract_id;
                $isvalid = $objModel->saveData( $formData );
                if($isvalid){                                                                                                                        
                    $objError->message = $objTranslate->translate('workdiary Added!');
                    $objError->messageType = 'success';
                    $this->_redirect("/contractor/workdiary/index/contract_id/".$objRequest->contract_id);
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
     *   /workdiary/
     *   /workdiary/edit
     *
     * @return void
     */
    public function editAction() 
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();                        
        $this->view->siteTitle = $objTranslate->translate('contractor - edit workdiary');
        
        $objModel = new Models_Workdiary();
        $objForm = new Models_Form_Workdiary();               
        $arrData = $objModel->fetchEntry($objRequest->id);
        
        if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {                    
                //manage upload screenshot  
                if(isset($_FILES['screenshot'])){
                    $upload = new Zend_File_Transfer_Adapter_Http();			
                    $upload->setDestination(SCREENSHOT_ROOT_PATH);			                    
                    $newFileName = uniqid('screenshot_', FALSE).'.'.pathinfo($upload->getFileName() ,PATHINFO_EXTENSION);    					
                    $upload->addFilter('Rename', $newFileName);                    
                    if($upload->receive()){
                        $formData['screenshot'] = $newFileName;
                        //remove old file 
                        if( $arrData['screenshot']!="" && file_exists(SCREENSHOT_ROOT_PATH.$arrData['screenshot']) ){
                            unlink(SCREENSHOT_ROOT_PATH.$arrData['screenshot']);
                        }
                    }
                }    
                
                $isvalid = $objModel->updateData( $formData, $objRequest->id);
                if($isvalid){                                                                                                                        
                    $objError->message = $objTranslate->translate('workdiary updated!');
                    $objError->messageType = 'success';
                    $this->_redirect($objRequest->getHeader('referer'));
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
        $this->view->arrData = $arrData;        
    }
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /workdiary/
     *   /workdiary/delete
     *
     * @return void
     */
    public function deleteAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();
        
        $objModel = new Models_Workdiary ();        					        
        $arrData = $objModel->fetchEntry($objRequest->id);
        if( $objModel->deleteData ($objRequest->id) ){            
            //remove file after delete record 
            if( $arrData['screenshot']!="" && file_exists(SCREENSHOT_ROOT_PATH.$arrData['screenshot']) ){
                unlink(SCREENSHOT_ROOT_PATH.$arrData['screenshot']);
            }
        }

        $objError->message = $objTranslate->translate('workdiary entry deleted!');
        $objError->messageType = 'success';
        $this->_redirect($objRequest->getHeader('referer'));
        exit;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /workdiary/
     *   /workdiary/exportpdf
     *
     * @return void
     */
    public function exportpdfAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();
        
        $objModel = new Models_Workdiary ();        					        
        $arrData = $objModel->fetchData($objRequest->contract_id);
                
        // Include the main TCPDF library 
        require_once( TCPDF_ROOT_PATH );                
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);        
        $pdf->SetCreator(PDF_CREATOR);                
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Timesheet report', 'Generated on: '.date('d-m-Y'));           
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));       
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);       
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);       
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);                
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);             
        $pdf->SetFont('helvetica', '', 12);
        // add a page
        $pdf->AddPage();        
        
        $header = array('memo', 'time_loged', 'keystroke_count', 'mousestroke_count');         
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(128, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B');
        // Header
        $w = array(40, 35, 40, 45);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();
                
        // Data
        $fill = 0;
        foreach($arrData as $row) {
            $pdf->Cell($w[0], 6, $row['memo'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 6, $row['time_loged'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, $row['keystroke_count'], 'LR', 0, 'R', $fill);
            $pdf->Cell($w[3], 6, $row['mousestroke_count'], 'LR', 0, 'R', $fill);
            $pdf->Ln();
            $fill=!$fill;
        }
        $pdf->Cell(array_sum($w), 0, '', 'T');               
        
        // close and output PDF document
        $pdf->Output('report.pdf', 'D');
        
        exit;
    }
    
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *   /
     *   /workdiary/
     *   /workdiary/exportcsv
     *
     * @return void
     */
    public function exportcsvAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();
        
        $objModel = new Models_Workdiary ();        					        
        $arrData = $objModel->fetchData($objRequest->contract_id);
        //_pr($arrData ,1);        
        
        $fp = fopen(ASSETS_ROOT_PATH.'report.csv', 'w');
        foreach ($arrData as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        
        $path = ASSETS_ROOT_PATH.'report.csv';
        header('Accept-Ranges: bytes');  // For download resume
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header('Content-Description: File Transfer' );
        header('Content-Disposition: attachment; filename="'.basename( $path ).'"' );
        header('Content-Length: ' . filesize($path));  // File size
        header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
        header('Content-Type: application/octet-stream' );
        header('Expires: 0' );
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
        header('Pragma: no-cache' );
        ob_clean();
        flush();
        readfile($path);
        
        exit;
    }
    
    
    
    
}
