<?php

/**
 * IndexController is the default controller for this application
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class IndexController extends PS_Controller_Action {
    
    private $recaptcha_pubKey = '6Ld5YOwSAAAAAMxFhGRDE2Bosyki0JELN7u46vti';
    private $recaptcha_privKey = '6Ld5YOwSAAAAAAioIVDkTPxnE4WW3qdUztfsdc58';
    
    function init() {                
        parent::init();
    }

    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/index
     *
     * @return void
     */
    public function indexAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);

        $this->view->siteTitle = $objTranslate->translate('Home');

        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
    }

    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/register
     *
     * @return void
     */
    public function registerAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);
        $objRequest = $this->getRequest();
        $this->view->siteTitle = $objTranslate->translate('Register');

        $objModel = new Models_User();
        $objForm = new Models_Form_Register();

        //$country = $objModel->getcountry_list();
        //_pr($country,1);
        $objForm->country->addMultiOptions($objModel->getcountry_list());

        if (!isset($objRequest->member)) {
            $this->_redirect("/default/index/index");
        }
        $userType = array('contractor' => '1', 'employer' => '2');
        
        //genearate google recapta 
        $recaptcha = new Zend_Service_ReCaptcha($this->recaptcha_pubKey, $this->recaptcha_privKey);                
        $notempty = new Zend_Validate_NotEmpty();
        
        if ($objRequest->isPost()) {
        $formData = $objRequest->getPost();                  
          if($notempty->isValid($formData['recaptcha_response_field'])){          
          $validatecaptcha = $recaptcha->verify($formData['recaptcha_challenge_field'],$formData['recaptcha_response_field']);            
          if ($validatecaptcha->isValid()) {
            if ($objForm->isValid($formData)) {
                $formData['token'] = md5(uniqid(rand(), true));
                $formData['type'] = $userType[$objRequest->member];
                $isValid = $objModel->saveData($formData);
                if ($isValid) {
                    //send token to mail
                    $bodyHtml = $this->view->partial('email/_register.phtml', array('token' => $formData['token'], "site_name" => $this->getSiteVar('SITE_TITLE'), "username" => $formData['username']));
                    $mail = new Zend_Mail('utf-8');
                    $res = $mail->addTo($formData['email'])
                            ->setSubject('connectica - register confirmation')
                            ->setFrom('support@connectica.de', 'Connectica support')
                            ->setBodyHtml($bodyHtml)
                            ->send();

                    if ($res) {
                        $objError->message = $objTranslate->translate('You are successfully registered! and conformation link sent to your email.');
                        $objError->messageType = 'success';
                        $this->_redirect("/default/index/login");
                    } else {
                        $objError->message = $objTranslate->translate('Registration successfully completed but Activation mail not sent!');
                        $objError->messageType = 'warning';
                        $this->_redirect("/default/index/login");
                    }
                } else {
                    $objError->message = $objTranslate->translate('Registration failed, try again!');
                    $objError->messageType = 'error';
                    $this->_redirect("/default/index/register");
                }
            } else {
                $objForm->populate($formData);
                $objError->message = formatErrorMessage($objForm->getMessages());
                $objError->messageType = 'error';
            }
          } else{
             $objForm->populate($formData);
             $objError->message = $objTranslate->translate('captcha value is invalid');
             $objError->messageType = 'error'; 
          }
         }else{
             $objForm->populate($formData);
             $objError->message = $objTranslate->translate('captcha value is invalid');
             $objError->messageType = 'error';
         }
        }


        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";
        $this->view->objForm = $objForm;
        $this->view->objForm = $objForm;
        $this->view->recaptcha = $recaptcha;
    }
            
    
    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/confirmaccount
     *
     * @return void
     */
    public function confirmaccountAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);
        $objRequest = $this->getRequest();

        $objModel = new Models_User();

        if ($objModel->checkToken($objRequest->token)) {
            $objModel->changeStatus($objRequest->token);

            $objError->message = $objTranslate->translate('your account is successfully confirmed');
            $objError->messageType = 'success';
            $this->_redirect("/default/index/login");
        } else {
            $objError->message = $objTranslate->translate('Invalid request!');
            $objError->messageType = 'error';
            $this->_redirect("/default/index/login");
        }
    }

    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/login
     *
     * @return void
     */
    public function loginAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);

        $this->view->messageType = $objError->messageType;
        $this->view->message = $objError->message;
        $this->view->siteTitle = $objTranslate->translate('Login');

        $objRequest = $this->getRequest();

        $objModel = new Models_User();
        $objForm = new Models_Form_Login();

        if ($objRequest->isPost()) {
            $formData = $objRequest->getPost();
            if ($objForm->isValid($formData)) {
                if (isset($formData['submit'])) {
                    $isValid = $objModel->verifyLoginInfo($formData);
                    if ($isValid) {
                        $objModel->setSession();
                        //redirection based on user type 
                        $objSess = new Zend_Session_Namespace(PS_App_Auth);
                        if ($objSess->user_type == '1') {
                            $this->_redirect("/contractor/index/index");
                        } elseif ($objSess->user_type == '2') {
                            $this->_redirect("/employer/index/index");
                        } elseif ($objSess->user_type == '3') {
                            $this->_redirect("/agency/index/index");
                        } else {
                            $objError->message = $objTranslate->translate('invalid user!');
                            $objError->messageType = 'error';
                            $this->_redirect("/default/index/index");
                        }
                    } else {
                        $objError->message = $objTranslate->translate('Authentication failed, try again!');
                        $objError->messageType = 'error';
                        $this->_redirect("/default/index/login");
                    }
                }
            } else {
                $objForm->populate($formData);
                $objError->message = formatErrorMessage($objForm->getMessages());
                $objError->messageType = 'error';
            }
        }

        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";

        $this->view->objForm = $objForm;
    }

    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/logout
     *
     * @return void
     */
    public function logoutAction() {
        $objModel = new Models_User();
        $objModel->clearSession();
        $this->_redirect("/");
    }

    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/forgotpassword
     *
     * @return void
     */
    public function forgotpasswordAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);
        $objRequest = $this->getRequest();

        $objModel = new Models_User();
        $objForm = new Models_Form_Forgotpassword();

        if ($objRequest->isPost()) {
            $formData = $objRequest->getPost();
            if ($objForm->isValid($formData)) {
                $isValid = $objModel->checkeEmail($formData['email']);
                if ($isValid) {
                    $token = md5(uniqid(rand(), true));
                    $objModel->setToken($token, $isValid['email']);
                    //send token to mail
                    $bodyHtml = $this->view->partial('email/_forgotpassword.phtml', array('token' => $token));
                    $mail = new Zend_Mail('utf-8');
                    $mail->addTo($isValid['email'])
                            ->setSubject('connectica - password reset')
                            ->setFrom('support@connectica.de', 'connectica support')
                            ->setBodyHtml($bodyHtml)
                            ->send();

                    $objError->message = $objTranslate->translate('password reset link sent to your email.');
                    $objError->messageType = 'success';
                    $this->_redirect("/default/index/login");
                } else {
                    $objError->message = $objTranslate->translate('This email not found in our database.');
                    $objError->messageType = 'error';
                    $this->_redirect("/default/index/forgotpassword");
                }
            } else {
                $objForm->populate($formData);
                $objError->message = formatErrorMessage($objForm->getMessages());
                $objError->messageType = 'error';
            }
        }

        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";

        $this->view->objForm = $objForm;
    }

    /**
     * Assuming the default route and default router, this action is dispatched 
     * via the following urls:
     *        
     *   /index/changeforgotpassword
     *
     * @return void
     */
    public function changeforgotpasswordAction() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);
        $objRequest = $this->getRequest();

        $objModel = new Models_User();
        $objForm = new Models_Form_Changeforgotpassword();

        if ($objModel->checkToken($objRequest->token)) {
            if ($objRequest->isPost()) {
                $formData = $objRequest->getPost();
                if ($objForm->isValid($formData)) {
                    $isValid = $objModel->setPassword($objRequest->token, $formData['password']);
                    if ($isValid) {
                        $objError->message = $objTranslate->translate('password successfully changed.');
                        $objError->messageType = 'success';
                        $this->_redirect("/default/index/login");
                    } else {
                        $objError->message = $objTranslate->translate('problem while update new password.');
                        $objError->messageType = 'error';
                        $this->_redirect("/default/index/changeforgotpassword");
                    }
                } else {
                    $objForm->populate($formData);
                    $objError->message = formatErrorMessage($objForm->getMessages());
                    $objError->messageType = 'error';
                }
            }
        } else {
            $objError->message = $objTranslate->translate('Invalid request!');
            $objError->messageType = 'error';
            $this->_redirect("/default/index/login");
        }

        $this->view->message = $objError->message;
        $this->view->messageType = $objError->messageType;
        $objError->message = "";

        $this->view->objForm = $objForm;
    }

    /*
      this function will work like as a web-service for login purpose
     */

    public function loginauthAction() {
        $request_data = $_REQUEST;
        extract($request_data, EXTR_SKIP);

        if (!isset($username) || !isset($password)) {
            echo json_encode(array("success" => false, "message" => "Please pass parameters (or) some parameters missing"));
            exit;
        }

        $success = false;
        $message = "";
        $userdata = "";

        if (trim($username) != "" && trim($password) != "") {
            $data['email'] = $username;
            $data['password'] = $password;

            $objModel = new Models_User();
            $isValid = $objModel->apiverifyLoginInfo($data);
            if ($isValid) {
                $this->_auth = Zend_Auth::getInstance();
                $user_data = $this->_auth->getIdentity();

                $objWebservice = new Models_Webservice();
                $user_data = $objWebservice->getUserDetail($user_data);
                $success = true;
                $userdata = $user_data;
                $message = "logged in successfully";
            } else {
                $message = "username or password wrong or you have no access to this area";
            }
        } else {
            $message = "you cannot pass null values";
        }

        $return_data = array("success" => $success, "message" => $message, "user_data" => $userdata, "screenshotpath" => SCREENSHOT_ROOT_PATH);
        echo json_encode($return_data);
        exit;
    }

    public function screenshotAction() {
        $request_data = $_REQUEST;
        extract($request_data, EXTR_SKIP);

        if (!isset($contract_id) || !isset($memo) || !isset($time_loged) || !isset($keystroke_count) || !isset($mousestroke_count)) {
            echo json_encode(array("success" => false, "message" => "Please pass parameters (or) some parameters missing"));
            exit;
        }

        $success = false;
        $message = "";

        if (trim($contract_id) != "" && trim($memo) != "" && trim($time_loged) != "" && trim($keystroke_count) != "" && trim($mousestroke_count) != "") {
            $data['contract_id'] = $contract_id;
            $data['memo'] = $memo;
            $data['time_loged'] = $time_loged;
            $data['keystroke_count'] = $keystroke_count;
            $data['mousestroke_count'] = $mousestroke_count;
            $data['created'] = date('Y-m-d H:i:s');

            if (isset($_FILES['screenshot'])) {
                //file uploading 
                $upload = new Zend_File_Transfer_Adapter_Http();
                //assigning file upload path
                $upload->setDestination(SCREENSHOT_ROOT_PATH);
                //here we are generating new file(unique file name)
                $newFileName = uniqid('screenshot_', FALSE) . '.' . pathinfo($upload->getFileName(), PATHINFO_EXTENSION);
                //renaming file
                $upload->addFilter('Rename', $newFileName);

                //here screen shot will upload into server
                if ($upload->receive()) {
                    //if file uplaod success then data will store into database
                    $data['screenshot'] = $newFileName;
                    $objModel = new Models_Workdiary();
                    $isvalid = $objModel->saveData($data);
                    if ($isvalid) {
                        $success = true;
                        $message = "successfully uploaded screenshot";
                    } else {
                        $message = "some error occored";
                    }
                } else {
                    $message = "file not uploaded.";
                }
            } else {
                $message = "File control not found.";
            }
        } else {
            $message = "you cannot pass null values.";
        }

        $return_data = array("success" => $success, "message" => $message);

        echo json_encode($return_data);
        exit;
    }

}
