<?php

/**
 * Class for job table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Contractrating extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'contract_rating'; 		
    
        
        
    /**
    * Save data for new contract rating entry.
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
     * Save updated data for contract rating entry.
     *     
     */         	
    public function updateData(array $data,$id) {
        $where = "contract_id = ".$id;
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
     * Delete contract rating 
     *     
     */      	
    public function deleteData($id) {
        $objData = $this->fetchRow('id = '.$id);		
        $objData->delete();
        unset($objData);
    }            		
      
   
	
}
?>
