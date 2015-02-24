<?php

/**
 * contractController 
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Agency_AgentController extends PS_Controller_AgencyAction 
{
	// LOAD CONTROLLER Agency
	/*public function init()
    {
		//we are creating nll cookie
        setcookie("pagename", "", time()+2592000, "/");
		setcookie("menu_name", "", time()+2592000, "/");
		$_COOKIE['menu_name'] = "profile";
		
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
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();
        //creating new cookie..
        // $_COOKIE['pagename'] = "New User";
		
		$this->view->siteTitle = $objTranslate->translate('My Profile');
        
		
        $objModel = new Models_Agency();
        $allagencies = $objModel->getAllAgencies($objSess->user_id);
        
        $this->view->allagencies = $allagencies;     
		$this->view->sidebar = $this->view->partial('index/_sidebar.phtml', array());
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;		
        $objError->message  = "";
	}
	
	public function profileAction()
	{	
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        
		//getting the request values from URL
		$objRequest = $this->getRequest();
		
		//if parameter is not available then it will redirect to agency page
		if( !isset($objRequest->agent_id) && $objRequest->agent_id == '' ){
            $objError->message = $objTranslate->translate('please select contract!');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
        }
		
		//decoding parameter value and typecasting.
		$agent_id = (int)base64_decode($objRequest->agent_id);
		
		if(!$agent_id){
            $objError->message = $objTranslate->translate('Some thing going wrong.');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
        }
			
		
        $this->view->siteTitle = $objTranslate->translate('My Profile');
        
        $objModel = new Models_Agency();
        $agencyinfo = $objModel->getAgencyInfo($agent_id);
        
        //echo "<pre>";
		//print_r($agencyinfo);exit;
        $this->view->agencyinfo = $agencyinfo;        
		echo $this->view->sidebar = $this->view->partial('index/_sidebar.phtml', array());
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;		
        $objError->message  = "";
	}
	
	public function editagentAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        
		//getting the request values from URL
		$objRequest = $this->getRequest();
		
		//if parameter is not available then it will redirect to agency page
		if( !isset($objRequest->agent_id) && $objRequest->agent_id == '' ){
            $objError->message = $objTranslate->translate('Please send parameters!');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
        }
		
		//decoding parameter value and typecasting.
		$agent_id = (int)base64_decode($objRequest->agent_id);
		
		if(!$agent_id){
            $objError->message = $objTranslate->translate('Some thing going wrong.');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
        }
		
        $objModel = new Models_Agency();
		$objForm = new Models_Form_Editagent();
        $agentinfo = $objModel->getAgencyInfo($agent_id);
		
		if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {                                                
                //manage upload profile picture 
                $upload = new Zend_File_Transfer_Adapter_Http();			
                $upload->setDestination(ASSETS_ROOT_PATH);			                    
                $newFileName = uniqid('agent_', FALSE).'.'.pathinfo($upload->getFileName() ,PATHINFO_EXTENSION);    					
                $upload->addFilter('Rename', $newFileName);                    
                if($upload->receive()){
                    $formData['profile_pic'] = $newFileName;
                    //remove old file 
                    if( $agentinfo['profile_pic']!="" && file_exists(ASSETS_ROOT_PATH.$agentinfo['profile_pic']) ){
                        unlink(ASSETS_ROOT_PATH.$agentinfo['profile_pic']);
                    }
                }
				else
				{
					$formData['profile_pic'] = $agentinfo['profile_pic'];
				}
                
                $isvalid = $objModel->updateData($formData,$agent_id);
                if($isvalid){                                                                                                                        
                    $objError->message = $objTranslate->translate('profile updated!');
                    $objError->messageType = 'success';
                    $this->_redirect("/agency/agent/profile/agent_id/".$objRequest->agent_id);
                }
            }else {	
                $objForm->populate($formData);
                $objError->message = formatErrorMessage ( $objForm->getMessages () );
                $objError->messageType = 'error';
            }			
        } else {
            $objForm->populate($agentinfo);
        }
        
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objForm = $objForm;                        
        $this->view->agentinfo = $agentinfo;
		
	}
	
	public function deleteAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        
		//getting the request values from URL
		$objRequest = $this->getRequest();
		
		//if parameter is not available then it will redirect to agency page
		if( !isset($objRequest->agent_id) && $objRequest->agent_id == '' ){
            $objError->message = $objTranslate->translate('Please send parameters!');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
        }
		
		//decoding parameter value and typecasting.
		$agent_id = (int)base64_decode($objRequest->agent_id);
		
		if(!$agent_id){
            $objError->message = $objTranslate->translate('Some thing going wrong.');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
        }
		
        $objModel = new Models_Agency();
        $res = $objModel->delete_agent($agent_id);
		if($res)
		{
			$objError->message = $objTranslate->translate('Agent Deleted Successfully!');
            $objError->messageType = 'success';
            $this->_redirect("/agency/agent/index");
		}
        else
		{
			$objError->message = $objTranslate->translate('Some Error occurred!');
            $objError->messageType = 'error';
            $this->_redirect("/agency/agent/index");
		}
	}
	
	public function messageAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
		$_COOKIE['pagename'] = "Sent Messages";
        $this->view->siteTitle = $objTranslate->translate($this->getRequest()->getCookie('pagename'));
        
        $objModel = new Models_Message();		
        $messages = $objModel->messages($objSess->username);
        
        $this->view->messages = $messages;     
		$this->view->sidebar = $this->view->partial('index/_msg_slider.phtml', array());
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;		
        $objError->message  = "";
	}
	
	public function messageinfoAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objRequest = $this->getRequest();
		$_COOKIE['pagename'] = "My Message info";
        $this->view->siteTitle = $objTranslate->translate($this->getRequest()->getCookie('pagename'));
        
		$msg_id = base64_decode($objRequest->msg_id);
		
        $objModel = new Models_Message();
        $messageinfo = $objModel->get_msg_info($msg_id);
		$messageinfo['reply'] = $objModel->get_replys($msg_id);
		$messageinfo['participated_users'] = $objModel->participated_users($msg_id);
		
		$objForm = new Models_Form_Viewmsg();
		$objconvesationModel = new Models_Conversation();
		
		if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {
				//echo "<pre>";print_r($messageinfo);exit;
				$data['reply_by'] = $objSess->username;
				$data['msg_id']	= $messageinfo['id'];
				$data['reply']	= $formData['reply'];
				$data['date']	= time();
				
				$res = $objconvesationModel->insert_conversation($data);
				if($res)
				{
					$objError->message = $objTranslate->translate('Reply sent successfully');
					$objError->messageType = 'success';
					$this->_redirect("/agency/agent/messageinfo/msg_id/".$objRequest->msg_id);
				}
				else
				{
					$objError->message = $objTranslate->translate('Not sent, Please try again later');
					$objError->messageType = 'error';
					$this->_redirect("/agency/agent/messageinfo/msg_id/".$objRequest->msg_id);
				}
            }
        }
        
		$this->view->objForm = $objForm;
        $this->view->messageinfo = $messageinfo;     
		$this->view->sidebar = $this->view->partial('index/_msg_slider.phtml', array());
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;		
        $objError->message  = "";
	}
	
	public function composeAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $_COOKIE['pagename'] = "Compose message";
		$this->view->siteTitle = $objTranslate->translate($this->getRequest()->getCookie('pagename'));
		//getting the request values from URL
		$objRequest = $this->getRequest();
		
		
		
        $objModel = new Models_Message();
		$objSentmsgModel = new Models_Sentmessage();
		$objForm = new Models_Form_Composemessage();
        
		
		if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {
                
				//echo "<pre>";
				//print_r($formData);exit;
				$data['subject'] 	= $formData['subject'];
				$data['message']	= $formData['editor1'];
				$data['sender']		= $objSess->username;
				$data['sent_date']	= time();
				$msg_id = $objModel->insert_msg($data);
				if($msg_id)
				{
					$msg_sentto['msg_id'] 	= $msg_id;					
					
					$array_agencynames = explode(",",$formData['receiver_name']);
					foreach($array_agencynames as $agentname)
					{
						$msg_sentto['receiver_name'] = $agentname;					
						$res_conversation = $objSentmsgModel->insert_rec($msg_sentto);
					}
					if($res_conversation)
					{
						$objError->message = $objTranslate->translate('Message sent Successfully');
						$objError->messageType = 'success';
						$this->_redirect("/agency/agent/message");
					}
					else
					{
						$objError->message = $objTranslate->translate('Conversation Not inserted');
						$objError->messageType = 'error';
						$this->_redirect("/agency/agent/message");
					}
				}
				else
				{
					$objError->message = $objTranslate->translate('Message not inserted');
					$objError->messageType = 'error';
					$this->_redirect("/agency/agent/message");
				}				
            }
        }
		
		//$this->view->headScript()->appendFile($this->view->baseUrl().'/includes/ckeditor/ckeditor.js');
        $this->view->sidebar = $this->view->partial('index/_msg_slider.phtml', array());
		$this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $objError->messageType = '';
        $this->view->objForm = $objForm;
		
	}
	
	public function inboxAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        
		//creating new cookie..
        $_COOKIE['pagename'] = "My Messages";
		
		$pagename = $this->view->siteTitle = $objTranslate->translate($this->getRequest()->getCookie('pagename'));
        
        $objModel = new Models_Message();		
        $messages = $objModel->inbox_msg($objSess->username);
        
        $this->view->messages = $messages;     
		$this->view->sidebar = $this->view->partial('index/_msg_slider.phtml', array());
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;		
        $objError->message  = "";
	}
	
	public function contractorsAction()
	{
		$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);                                
        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        
		//getting the request values from URL
		$objRequest = $this->getRequest();
		
        $objModel = new Models_Agency();
		
		$objForm = new Models_Form_Slidercontractor();
		
		$contractorname = "";
		$type			= "";
		
		if ($objRequest->isPost ()) {
            $formData = $objRequest->getPost ();
            if ($objForm->isValid ( $formData )) {				
				$contractorname = $formData['find'];
				$type 			= $formData['type'];
			}
		}
		
		if($type=="2")
		{
			$contractors['type'] = "Employee";
		}
		else
		{
			$contractors['type'] = "Contractor";
		}
		
		$contractors['result'] = $objModel->get_all_contractors($contractorname,$type);
		
		$this->view->siteTitle = $objTranslate->translate($contractors['type']);
		
        $this->view->contractors = $contractors;		
		$this->view->find_contractors = $this->view->partial('index/find_contractor.phtml', array("value"=>$objForm));
        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;		
        $objError->message  = "";
	}
	
	
}