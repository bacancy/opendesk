<?php

/**
 * Class for contract table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Contract extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'contract'; 		
    
        
        
    /**
    * Save data for new job entry.
    *    
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

        $data['created'] = date('Y-m-d H:i:s');
        $data['modified'] = date('Y-m-d H:i:s');                                   		
        return $this->insert($data);
    }
    
    
    /**
     * Save updated data for job entry.
     *     
     */         	
    public function updateData(array $data,$id) {
        $where = "id = ".$id;
        $fields = $this->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) {
                if (!in_array($field, $fields)) {
                        unset($data[$field]);
                }
        }

        $data['modified'] = date('Y-m-d H:i:s');       
        return $this->update($data,$where);
    }
    
    
    /**
     * Delete job 
     *     
     */      	
    public function deleteData($id) {
        $objData = $this->fetchRow('id = '.$id);		
        $objData->delete();
        unset($objData);
    }
    
	
	/**
     * Delete record based on column
     *     
     */      	
    public function deleteDatabyColumn($id) {
		
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('c'=>'contract'),array('c.*'))                    
                ->where('c.job_id = ?' ,$id);										
    	
    	$select = $this->fetchRow($select);        
		if($select)
		{
			$select->delete();
		}		
        unset($select);
    }
    
    /**
     * Get all job listing.
     * 
     */    	
    public function getList($searchText='',$searchType='',$sortby='')
    { 
        switch($sortby){
            case 'A1':
                            $strSort = "title ASC";
                     break;
            case 'D1':
                            $strSort = "title DESC";
                     break;            	                        		            
            default:			
                            $strSort = "id DESC";
                    break;
        }										    	    	    	   

        $search_txt = addslashes($searchText);
        $search_type = addslashes($searchType);	  
        $cond_string = $search_type." like '%".$search_txt."%'";

        $select = $this->select();
        
            if($search_txt != '' && $search_type != '')
                    $select->where($cond_string);
            
            $objSess = new Zend_Session_Namespace ( PS_App_Auth );
            if($objSess->user_type == '1'){
                $select->where('contractor_id = ?' ,$objSess->user_id);
            }else if($objSess->user_type == '2'){
                $select->where('employer_id = ?' ,$objSess->user_id);
            }                
            
            if(trim($sortby)=='')
                    $select->order('id ASC');
            else
                    $select->order($strSort);	 	 	
	
        return $select;	 	    
    }
    
    
    /**
     * Fetch Particular contract Detail
     *    
     */    
    public function fetchEntry($id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('c'=>'contract')
                    ,array('c.id','c.contractor_id','c.employer_id','c.job_id','c.title as contract_title','c.description as contract_description','c.status as contract_status','c.manual_allowed as contract_manual_allowed','c.created as contract_created','c.modified as contract_modified'))				
                ->join(array('co' => 'user') ,'c.contractor_id = co.id' 
                    ,array('co.email as contractor_email','co.firstname as contractor_firstname','co.lastname as contractor_lastname','co.portrait as contractor_portrait')
                )
                ->join(array('em' => 'user') ,'c.employer_id = em.id' 
                    ,array('em.email as employer_email','em.firstname as employer_firstname','em.lastname as employer_lastname','em.portrait as employer_portrait')
                )
                ->join(array('j' => 'job') ,'c.job_id = j.id' 
                    ,array('j.title as job_title','j.description as job_description','j.type as job_type','j.estimate as job_estimate')
                )
                ->joinLeft(array('cr' => 'contract_rating') ,'c.id = cr.contract_id' 
                    ,array('cr.contractor_rating','cr.contractor_review','cr.employer_rating','cr.employer_review',)
                )
                ->where('c.id = ?' ,$id);										
    	
    	$select = $this->fetchRow($select);        
	    						
	if($select){
            $select = $select->toArray();
            return $select;
        }else{
            return null;    
        }
    }
    
    
    /**
     * Fetch Particular job Detail
     *    
     */    
    public function fetchContract($id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('c'=>'contract'),array('c.*'))                    
                ->where('c.id = ?' ,$id);										
    	
    	$select = $this->fetchRow($select);        
	$select = $select->toArray();    			
				
	if($select)
            return $select;
	else
            return null;    
    }
    
    
    /**
     * check contract is already strated 
     *    
     */    
    public function checkContract($job_id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('c'=>'contract'),array('c.*'))                    
                ->where('c.job_id = ?' ,$job_id);										    
    	$select = $this->fetchRow($select);        
	    			
				
	if($select){
            $select = $select->toArray();
            return $select;
        }else{
            return null;    
        }
    }
    
	
}
?>
