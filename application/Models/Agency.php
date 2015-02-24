<?php

/**
 * Class for agency table related database operations.
 *
 * @category   Agency
 * @package    Models_Agent
 * @copyright  Copyright (c) 2010 - 2013 
 */

class Models_Agency extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'agency'; 		
    

	/*
     * get agent information 
     */   
    public function getAgencyInfo($id) { 
        $select = $this->select();
        $select->where("id = '".$id."'");
        
        if( $this->fetchRow($select) )
            return $this->fetchRow($select)->toArray();
        else
            return false;
    }
    
	/**
	* using this function we can display all agencies
	*/
	public function getAllAgencies($id) { 
        $select = $this->select();
        $select->where("user_id = '".$id."' and status=0" );
        $select->order('id ASC');
        			
		return $this->fetchAll($select)->toArray();
		
    }
	
    /**
    * Save data for new agent entry.
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
     * Save updated data for agent entry.
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
        $data['modified'] = time();
        return $this->update($data,$where);
    }
    
    
    /**
     * Delete Agent 
     *     
     */      	
    public function delete_agent($id) {
        $where = "id = ".$id;
        $data['status'] = 1;
        $data['modified'] = time();
        return $this->update($data,$where);
    }
	
	public function messages($sender)
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('m'=>'message')
                    ,array('m.message')
				)
                ->join(array('c' => 'conversation') ,'c.msg_id = m.id' 
                    ,array('c.c_id','c.date')
                )
                ->join(array('u' => 'user') ,'c.sender = u.username' 
                    ,array('u.username')
                )
                ->where('c.sender = ?' ,$sender)
				->group("c.sender");
    	
    	$select = $this->fetchAll($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	
	public function get_msg_info($msg_id)
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('m'=>'message')
                    ,array('m.message')
				)
                ->join(array('c' => 'conversation') ,'c.msg_id = m.id' 
                    ,array('c.c_id','c.date')
                )
                ->join(array('u' => 'user') ,'c.sender = u.username' 
                    ,array('u.username')
                )
                ->where('c.c_id = ?' ,$msg_id);
    	
    	$select = $this->fetchRow($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	
	public function get_all_contractors($contractor="",$type="")
	{
		//echo $contractor.",".$type;exit;
		if($contractor!="" || $type!="")
		{
			$where = "u.status = 1 and u.type='$type' and (u.username LIKE '%$contractor%' or u.firstname LIKE '%$contractor%' or u.lastname LIKE '%$contractor%')";
		}
		else
		{
			$where = 'u.status = 1 and u.type=1';
		}
		//echo $where;exit;
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('u'=>'user')
                    ,array('u.id','u.email','u.type','u.username','u.firstname','u.lastname','u.country','u.portrait','u.profile_title','profile_desc')
					)
                ->where($where);
    	
    	$select = $this->fetchAll($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	
	/**
     * Get all job listing.
     * 
     */    	
    public function getList($searchText='',$searchType='',$sortby='',$contractor_id)
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
        $select->setIntegrityCheck(false)
				->from(array("c"=>"contract"),array("c.id","c.contractor_id","c.employer_id","c.job_id","c.title","c.description","c.status","c.manual_allowed","c.created","c.modified"));
				
            if($search_txt != '' && $search_type != '')
                    $select->where($cond_string);
            
            $objSess = new Zend_Session_Namespace ( PS_App_Auth );
                $select->where('c.contractor_id = ?' ,$contractor_id);
            
            if(trim($sortby)=='')
                    $select->order('id ASC');
            else
                    $select->order($strSort);	 	 	
	                
        return $select;	 	    
    }
	
	
}
?>