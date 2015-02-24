<?php
class Models_User extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user';
    protected $_adapter;
    protected $_auth; 
    
    /*
     * veryfy login information 
     */
    public function verifyLoginInfo($data) {
        
    	$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);

    	$dbAdapter = Zend_Registry::get(PS_App_DbAdapter);

        $this->_adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $this->_adapter->setTableName('user');
        $this->_adapter->setIdentityColumn('email');
        $this->_adapter->setCredentialColumn('password');
        $this->_adapter->setCredentialTreatment('SHA1(?) AND status = "1"');
		        
        //set the formData        
        $this->_adapter->setIdentity($data['email']);
        $this->_adapter->setCredential($data['password']);

        // get Zend_Auth object
        $this->_auth = Zend_Auth::getInstance();
		
        //now authenticate it
        $result = $this->_auth->authenticate($this->_adapter);
        if($result->isValid()) {
            return true;
        }

        return false;
    }
    
    /*
     * veryfy login information for api
     */
    public function apiverifyLoginInfo($data) {
        
    	$objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);

    	$dbAdapter = Zend_Registry::get(PS_App_DbAdapter);

        $this->_adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $this->_adapter->setTableName('user');
        $this->_adapter->setIdentityColumn('email');
        $this->_adapter->setCredentialColumn('password');
        $this->_adapter->setCredentialTreatment('SHA1(?) AND status = "1" AND type!=2');
		        
        //set the formData        
        $this->_adapter->setIdentity($data['email']);
        $this->_adapter->setCredential($data['password']);

        // get Zend_Auth object
        $this->_auth = Zend_Auth::getInstance();
		
        //now authenticate it
        $result = $this->_auth->authenticate($this->_adapter);
        if($result->isValid()) {
            return true;
        }

        return false;
    }
    
    /*
     * Set session after successful login    
     */
    public function setSession() {
       	        
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $objError = new Zend_Session_Namespace(PS_App_Error);

        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $storage = new Zend_Auth_Storage_Session();

        $arrData = array('id','email','type','username','firstname','lastname','portrait');
        $objRow = $this->_adapter->getResultRowObject($arrData);
        $storage->write($objRow);		
        $objUser = $this->_auth->getIdentity();		
        $objSess->user_id       = $objUser->id;
        $objSess->email 	= $objUser->email;        
        $objSess->user_type 	= $objUser->type;
        $objSess->username 	= $objUser->username;
        $objSess->firstname 	= $objUser->firstname;
        $objSess->lastname 	= $objUser->lastname;
        $objSess->portrait 	= $objUser->portrait;        
        $objSess->isUserLogin  = true;        
             
        $objError->message 	= $objTranslate->_("successful login");        
    }
    
    /*
     * Clear session after logout     
     */
    public function clearSession() {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        
        Zend_Auth::getInstance()->clearIdentity();

        $objSess = new Zend_Session_Namespace(PS_App_Auth);
        $objSess->user_id       = "";
        $objSess->email   	= "";
        $objSess->user_type 	= "";
        $objSess->username 	= "";
        $objSess->firstname 	= "";
        $objSess->lastname 	= "";
        $objSess->portrait 	= "";
        $objSess->isUserLogin  = false;
        $objSess->__unset(PS_App_Auth);
        $objError = new Zend_Session_Namespace(PS_App_Error);
        $objError->message = $objTranslate->translate('successfully logout');
        $objError->messageType = 'confirm';
    }
    
    /*
     * check email exist or not     
     */
    public function checkeEmail($email)
    {    	
        $select = $this->select();
        $select->where("email = '".$email."' AND status = '1'");
        
        if( $this->fetchRow($select) )
            return $this->fetchRow($select)->toArray();
        else
            return false;
    }
    
    
    /*
     * Set token     
     */   
    public function setToken($token,$email) { 
        $where = "email = '".$email."'";        
        $data = array();
        $data['token'] = $token;
        $data['modified'] = date('Y-m-d H:i:s');        	
      return $this->update($data,$where);
    }
    
    /*
     * Check token     
     */   
    public function checkToken($token) { 
        $select = $this->select();
        $select->where("token = '".$token."'");
        
        if( $this->fetchRow($select) )
            return $this->fetchRow($select)->toArray();
        else
            return false;
    }
    
    
    /*
     * Set password      
     */   
    public function setPassword($token,$password) { 
        $where = "token = '".$token."'";        
        $data = array();
		$data['token']		= "";
        $data['password'] 	= sha1($password);
        $data['modified'] 	= date('Y-m-d H:i:s');        	
      return $this->update($data,$where);
    }
            
    
    /*
     * change status of user       
     */   
    public function changeStatus($token) { 
        $where = "token = '".$token."'";        
        $data = array();
        $data['status'] = '1';
        $data['modified'] = date('Y-m-d H:i:s');        	
      return $this->update($data,$where);
    }
    
    
    /*
     * Save data    
     */
    public function saveData(array $data) {
        $fields = $this->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) 
        {
            if (!in_array($field, $fields)) 
            {
                unset($data[$field]);
            }
        }
        $data['password'] = sha1($data['password']);
        $data['status'] = 0;
        $data['created'] = date('Y-m-d H:i:s');
        $data['modified'] = date('Y-m-d H:i:s');                                                  

        return $this->insert($data);
    }
     
    
    /*
     * Update data    
     */   
    public function updateData(array $data,$user_id) { 
        $where = "id = " . $user_id;
        
        $fields = $this->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) {
                if (!in_array($field, $fields)) {
                        unset($data[$field]);
                }
        }        
        $data['modified'] = date('Y-m-d H:i:s');        

        if(isset($data['password']) && $data['password'] != "") {
            $encPwd = sha1($data['password']);
            $data['password'] = $encPwd;
        }		
		
	return $this->update($data,$where);
    }
    
    
    // get user information        
    public function getUserInfo($id) { 
        $select = $this->select();
        $select->where("id = '".$id."'");
        
        if( $this->fetchRow($select) )
            return $this->fetchRow($select)->toArray();
        else
            return false;
    } 
    
    public function getContractorInfo($id) {         
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('u'=>'user'),array("u.*"))
                ->joinLeft(array('c' => 'country') ,'u.country = c.id',array('c.nicename AS country_name'))
                ->joinLeft(array('co' => 'contract') ,'u.id = co.contractor_id',array())
                ->joinLeft(array('cr' => 'contract_rating') ,'co.id = cr.contract_id',array('AVG(cr.contractor_rating) AS avg_rating'))
                ->where("u.id = '".$id."'");    
        
        if( $this->fetchRow($select) )
            return $this->fetchRow($select)->toArray();
        else
            return false;
    }
    
    public function getContractorContractsInfo($id) {        
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('u'=>'user'),array())                
                ->joinLeft(array('co' => 'contract') ,'u.id = co.contractor_id',array('co.*'))
                ->joinLeft(array('cr' => 'contract_rating') ,'co.id = cr.contract_id',array('cr.*'))
                ->where("u.id = '".$id."'");
        
        if( $this->fetchAll($select) )
            return $this->fetchAll($select)->toArray();
        else
            return false;
    }
    
    public function getEmployerInfo($id) {        
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('u'=>'user'),array("u.*"))
                ->joinLeft(array('c' => 'country') ,'u.country = c.id',array('c.nicename AS country_name'))
                ->joinLeft(array('co' => 'contract') ,'u.id = co.employer_id',array())
                ->joinLeft(array('cr' => 'contract_rating') ,'co.id = cr.contract_id',array('AVG(cr.employer_rating) AS avg_rating'))
                ->where("u.id = '".$id."'");    
        
        if( $this->fetchRow($select) )
            return $this->fetchRow($select)->toArray();
        else
            return false;
    }
    
    public function getEmployerContractsInfo($id) {        
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('u'=>'user'),array())                
                ->joinLeft(array('co' => 'contract') ,'u.id = co.employer_id',array('co.*'))
                ->joinLeft(array('cr' => 'contract_rating') ,'co.id = cr.contract_id',array('cr.*'))
                ->where("u.id = '".$id."'");
        
        if( $this->fetchAll($select) )
            return $this->fetchAll($select)->toArray();
        else
            return false;
    }
    
    /**
     * return all contractor user list 
     *    
     */    
    public function getContractorList($searchText='',$searchType='',$sortby='',$job_id='') {    	
        switch($sortby){
            case 'A1':
                            $strSort = "u.email ASC";
                     break;
            case 'D1':
                            $strSort = "u.email DESC";
                     break;            	                        		            
            default:			
                            $strSort = "u.id DESC";
                    break;
        }										    	    	    	   

        $search_txt = addslashes($searchText);
        $search_type = addslashes($searchType);	  
        $cond_string = 'u'.$search_type." like '%".$search_txt."%'";
                
        $select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('b'=>'bid'),array())        
                        ->join(array('u' => 'user') ,'b.contractor_id = u.id',array('u.*'))
                        ->where('b.job_id = ?', $job_id);
                        
            if($search_txt != '' && $search_type != '')
                    $select->where($cond_string);                                               
            
            if(trim($sortby)=='')
                    $select->order('u.id ASC');
            else
                    $select->order($strSort);	 	 	
	                
        return $select;    
    }
    
	
	function getcountry_list()
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('c'=>'country'),array("c.id","c.name"));
        $select->order('c.name ASC');
		//echo $select;exit;
		$select = $this->fetchAll($select);
		$select = $select->toArray();
		
		if($select)
		{
            $data = array();
            foreach($select as $row){
                $data[$row['id']] = $row['name'];   
            }
            return $data;
        }else{
            return null;    
        }
    
		return $select;
	}
	
	/*
     * web service login authentication  
     */
    public function check_login($data) {        
    	
        $objError = new Zend_Session_Namespace(PS_App_Error);

    	$dbAdapter = Zend_Registry::get(PS_App_DbAdapter);

        $this->_adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $this->_adapter->setTableName('user');
        $this->_adapter->setIdentityColumn('email');
        $this->_adapter->setCredentialColumn('password');
        $this->_adapter->setCredentialTreatment('SHA1(?) AND status = "1"');
		        
        //set the formData        
        $this->_adapter->setIdentity($data['email']);
        $this->_adapter->setCredential($data['password']);

        // get Zend_Auth object
        $this->_auth = Zend_Auth::getInstance();
		
        //now authenticate it
        $result = $this->_auth->authenticate($this->_adapter);
		print_r($result);exit;
        /*if($result->isValid()) {
            return true;
        }*/

        return false;
    }
         
    
    
}


