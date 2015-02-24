<?php

/**
 * Class for job table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Job extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'job'; 		
    
        
        
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
        
        $data['duration_start'] = date('Y-m-d H:i:s', strtotime($data['duration_start']));
        $data['duration_end'] = date('Y-m-d H:i:s', strtotime($data['duration_end']));        
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
        //_pr($data,1);
        
        $data['duration_start'] = date('Y-m-d H:i:s', strtotime($data['duration_start']));
        $data['duration_end'] = date('Y-m-d H:i:s', strtotime($data['duration_end']));
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
                            $strSort = "j.title ASC";
                     break;
            case 'D1':
                            $strSort = "j.title DESC";
                     break;            	                        		            
            default:			
                            $strSort = "j.id DESC";
                    break;
        }										    	    	    	   

        $search_txt = addslashes($searchText);
        $search_type = addslashes($searchType);	  
        $cond_string = "j.".$search_type." like '%".$search_txt."%'";

        $select = $this->select();
        $select->from(array('j' => 'job'), array('j.*','datediff(j.duration_end,j.duration_start) AS duration_day'));
        
            if($search_txt != '' && $search_type != '')
                    $select->where($cond_string);
            
            $objSess = new Zend_Session_Namespace ( PS_App_Auth );
                $select->where('j.employer_id = ?' ,$objSess->user_id);
            
            if(trim($sortby)=='')
                    $select->order('j.id ASC');
            else
                    $select->order($strSort);	 	 	
	                
        return $select;	 	    
    }
    
	
	/**
     * Get all job listing for contractor
     * 
     */    	
    public function getjobList($searchText='',$searchType='',$sortby='')
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
        //$cond_string = "category_id=".$search_type." and title like '%".$search_txt."%'";
		
		$objSess = new Zend_Session_Namespace ( PS_App_Auth );        
				
        $select = $this->select()
			//$select->setIntegrityCheck(false)
                ->from(array('j'=>'job'),array('j.*','datediff(j.duration_end,j.duration_start) AS duration_day'))
				->where("j.id not in(select job_id from bid where contractor_id=?)",$objSess->user_id);
            if($search_txt != '')
                    $select->where("title like '%".$search_txt."%'");
					
			if($search_type != '')
                    $select->where("category_id=".$search_type);
            
            if(trim($sortby)=='')
                    $select->order('id ASC');
            else
                    $select->order($strSort);	 	 	
	    
			$select = $this->fetchAll($select);
			
        return $select;	 	    
    }
    
    /**
     * Fetch Particular job Detail
     *    
     */    
    public function fetchEntry($id) {    	
        $select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('j'=>'job'),array('j.*'))				
                        ->where('j.id = ?' ,$id);										
    	
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
