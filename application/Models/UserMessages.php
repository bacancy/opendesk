<?php

/**
 * Class for user_messages table related database operations.
 *
 * @category   NIC
 * @package    Models_UserMessages
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_UserMessages extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'user_messages'; 		
    
        
        
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
     * Delete job 
     *     
     */      	
    public function deleteData($id) {
        $objData = $this->fetchRow('id = '.$id);		
        $objData->delete();
        unset($objData);
    }
    
    
    /**
     * Get all user messages listing.
     * 
     */    	
    public function getList( $objRequest )
    { 
        $objSess = new Zend_Session_Namespace ( PS_App_Auth );									    	    	    	   
                                                
        $select = $this->select(); 
        $select->setIntegrityCheck(false)        
            ->from(array('um'=>'user_messages'),array('um.id AS msg_id','um.content AS msg_content','um.date AS msg_date','um.is_read AS msg_is_read'))            
            ->joinLeft(array('us'=>'user'),'um.sender_id = us.id',array("us.id AS sender_id","us.email AS sender_email","us.firstname AS sender_firstname","us.lastname AS sender_lastname","us.portrait AS sender_portrait"))
            ->joinLeft(array('ur'=>'user'),'um.receiver_id = ur.id',array("ur.id AS receiver_id","ur.email AS receiver_email","ur.firstname AS receiver_firstname","ur.lastname AS receiver_lastname","ur.portrait AS receiver_portrait"))
            ->where("um.belong_to = ?", $objSess->user_id)
            ->where("um.receiver_id = ".$objSess->user_id." OR um.sender_id = ".$objSess->user_id)    
            ->order("um.date DESC");                                                                                                    
        
        //_pr($this->fetchAll($select)->toArray(),1);        
        return $select;	 	    
    }                                       
	
      
   
	
}
?>
