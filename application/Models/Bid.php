<?php

/**
 * Class for job table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Bid extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'bid'; 		
    
        
        
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

        $data['applied_date'] = date('Y-m-d H:i:s');
        
        return $this->insert($data);
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
        $cond_string = $search_type." like '%".$search_txt."%'";
		
		$objSess = new Zend_Session_Namespace ( PS_App_Auth );
		
        $select = $this->select();
			$select->setIntegrityCheck(false)
					->from(array('j'=>'job'),array('j.*'))
					->join(array('b' => 'bid'),'',"b.applied_date")
					->where("j.id=b.job_id and b.contractor_id=?",$objSess->user_id);
                /*->from(array('j'=>'job'),array('j.*'))
				->join(array('b' => 'bid'),'',"b.applied_date")
				->where("j.id in(select job_id from bid where contractor_id=?)",$objSess->user_id);*/
				
            if($search_txt != '' && $search_type != '')
                    $select->where($cond_string);
            
            if(trim($sortby)=='')
                    $select->order('id ASC');
            else
                    $select->order($strSort);
		
		$select->group("j.id");
	    //echo $select;exit;
        return $select;	 	    
    }
	
	/**
     * Delete record based on column
     *     
     */      	
    public function deleteDatabyColumn($id) {
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('b'=>'bid'),array('b.*'))                    
                ->where('b.job_id = ?' ,$id);										
    	
    	$select = $this->fetchRow($select);        
		if($select)
		{
			if($select->delete())
			{
				return true;
			}
			else
			{
				return false;
			}
		}		
        unset($select);
    }
	
	/**
     * Delete record based on column
     *     
     */      	
    public function deleteDatabycontractor($id) {
        $select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('b'=>'bid'),array('b.*'))                    
                ->where('b.contractor_id = ?' ,$id);										
    	
    	$select = $this->fetchRow($select);        
		if($select)
		{
			if($select->delete())
			{
				return true;
			}
			else
			{
				return false;
			}
		}		
        unset($select);
    }
}
?>
