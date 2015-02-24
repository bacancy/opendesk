<?php

/**
 * Class for work_diary table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Workdiary extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'work_diary'; 		
    
        
        
    /**
    * Save data for new work_diary entry.
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

        $data['time_loged'] = date('Y-m-d H:i:s', strtotime($data['time_loged']));
        $data['created'] = date('Y-m-d H:i:s');
        $data['modified'] = date('Y-m-d H:i:s');                                   		
        return $this->insert($data);
    }
    
    
    /**
     * Save updated data for work_diary entry.
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
        
		if($data['time_loged'])
		{
			$data['time_loged'] = date('Y-m-d H:i:s', strtotime($data['time_loged']));
        }
		$data['modified'] = date('Y-m-d H:i:s');       
        return $this->update($data,$where);
    }
    
    
    /**
     * Delete work_diary 
     *     
     */      	
    public function deleteData($id) {
        $objData = $this->fetchRow('id = '.$id);		
        return $objData->delete();
        unset($objData);
    }
    
    
    /**
     * Get all work_diary listing.
     * 
     */    	
    public function getList($searchText='',$searchType='',$sortby='',$objRequest)
    {         
        switch($sortby){
            case 'A1':
                            $strSort = "time_loged ASC";
                     break;
            case 'D1':
                            $strSort = "time_loged DESC";
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
            
            if($objRequest->weekly == '1')
                $select->where('time_loged > DATE_SUB(NOW(), INTERVAL 1 WEEK)');            
            
            $select->where('contract_id = ?' ,$objRequest->contract_id);
            
            if(trim($sortby)=='')
                    $select->order('time_loged DESC');
            else
                    $select->order($strSort);	 	 	
	       
		   return $select;	 	    
    }
    
    
    /**
     * Fetch Particular work_diary Detail
     *    
     */    
    public function fetchEntry($id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('w'=>'work_diary'),array('w.*'))                
                ->where('w.id = ?' ,$id);							
    	
    	$select = $this->fetchRow($select);        
	$select = $select->toArray();    			
				
	if($select)
            return $select;
	else
            return null;    
    }
    
    
    /**
     * get all work_diary Detail
     *    
     */    
    public function fetchData($contract_id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('w'=>'work_diary'),array('w.*'))                
                ->where('w.contract_id = ?' ,$contract_id);							
    	
    	$select = $this->fetchAll($select);        
	$select = $select->toArray();    			
				
	if($select)
            return $select;
	else
            return null;    
    }
	
    /**
     * get all work_diary Detail from current week
     *    
     */    
    public function currentweek_workdairyData($contract_id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('w'=>'work_diary'),array('w.id'))
                ->where('w.contract_id = ? and time_loged > DATE_SUB(NOW(), INTERVAL 1 WEEK)' ,$contract_id);							
    	
    	$select = $this->fetchAll($select);        
	$select = $select->toArray();    			
				
	if($select)
            return $select;
	else
            return null;    
    }
	
	/**
     * get all work_diary status from current week
     *    
     */    
    public function statusof_currentweek($contract_id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('w'=>'work_diary'),array('w.status'))
                ->where('w.contract_id = ? and time_loged > DATE_SUB(NOW(), INTERVAL 1 WEEK)' ,$contract_id);							
    	
    	$select = $this->fetchRow($select);        
		$select = $select->toArray();    			
					
		if($select)
			return $select;
		else
			return null;    
    }
}
?>
