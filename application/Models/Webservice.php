<?php
class Models_Webservice extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';    
    private $data = array();   
    
     
    public function getUserDetail($id) {                 
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('u'=>'user'),array("u.id AS user_id","u.email AS user_email","u.firstname AS user_firstname","u.lastname AS user_lastname","CONCAT('http://".$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getBaseUrl()."/assets/',u.portrait) AS user_portrait","u.type AS user_type"))               
            ->where("u.email = '".$id."'");
        if($this->fetchRow($select)){
            $this->data['user'] = $this->fetchRow($select)->toArray();
            $user_id = $this->data['user']['user_id'];
            $user_type = $this->data['user']['user_type'];
            //For contractor user type
            if(isset($user_type) && $user_type=='1'){
                $this->getContractorContractList($user_id);
            }
            //For employer user type
            if(isset($user_type) && $user_type=='2'){
                $this->getEmployerContractList($user_id);
            }
            //For agency user type
            if(isset($user_type) && $user_type=='3'){
                $this->getContractorContractList($user_id);
                $this->getEmployerContractList($user_id);
            }                       
        }    
        //_pr($this->data ,1);
        
        if( $this->data )
            return $this->data;
        else
            return false;
    }                
        
    public function getContractorContractList($user_id) {                         
        $select = $this->select(); 
        $select->setIntegrityCheck(false)        
            ->from(array('c'=>'contract'),array('c.id AS contract_id','c.title AS contract_title','c.description AS contract_description','DATE_FORMAT(c.created,"%Y-%m-%d") AS contract_starte_date'))            
            ->joinLeft(array('j'=>'job'),'c.job_id = j.id',array("j.title AS job_title"))            
            ->joinLeft(array('u'=>'user'),'c.employer_id = u.id',array("u.id AS employer_id","u.company_name AS employer_company_name"))    
            ->where("c.contractor_id = ?", $user_id)    
            ->where("j.type = 2")                
            ->order("c.created DESC");        
        
        if($this->fetchAll($select)){
            $this->data['user']['contract'] = $this->fetchAll($select)->toArray();                                    
        }
    }
    
    public function getEmployerContractList($user_id) {                         
        $select = $this->select(); 
        $select->setIntegrityCheck(false)        
            ->from(array('c'=>'contract'),array('c.id AS contract_id','c.title AS contract_title','c.description AS contract_description','DATE_FORMAT(c.created,"%Y-%m-%d") AS contract_starte_date'))            
            ->joinLeft(array('j'=>'job'),'c.job_id = j.id',array("j.title AS job_title")) 
            ->joinLeft(array('u'=>'user'),'c.contractor_id = u.id',array("u.id AS contractor_id","u.company_name AS contractor_company_name"))    
            ->where("c.employer_id = ?", $user_id)
            ->where("j.type = 2")                
            ->order("c.created DESC");                
        
        if($this->fetchAll($select)){
            $this->data['user']['contract'] = $this->fetchAll($select)->toArray();                                    
        }
    }
    
    
    
}


