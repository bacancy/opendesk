<?php

/**
 * Class for job table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Jobcategory extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'job_category'; 		
    
        
        
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
                $select->where('employer_id = ?' ,$objSess->user_id);
            
            if(trim($sortby)=='')
                    $select->order('id ASC');
            else
                    $select->order($strSort);	 	 	
	                
        return $select;	 	    
    }
    
    
    /**
     * Fetch Particular job Detail
     *    
     */    
    public function fetchEntry($id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('j'=>'job_category'),array('j.*'))				
                        ->where('j.id = ?' ,$id);										
    	
    	$select = $this->fetchRow($select);
	$select = $select->toArray();    			
				
	if($select)
            return $select;
	else
            return null;    
    }	
	
    
    /**
     * Fetch Particular job Detail
     *    
     */    
    public function getListdata() {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('j'=>'job_category'),array('j.*'));                        										
    	
    	$select = $this->fetchAll($select);
	$select = $select->toArray();    			
	//_pr($select ,1);
        
	if($select){
            $data = array();
            foreach($select as $row){
                $data[$row['id']] = $row['name'];   
            }
            return $data;
        }else{
            return null;    
        }    
    }
   
	
}
?>
