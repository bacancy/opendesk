<?php

/**
 * Class for agency table related database operations.
 *
 * @category   Agency
 * @package    Models_Agent
 * @copyright  Copyright (c) 2010 - 2013 
 */

class Models_Sentmessage extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'sent_message'; 		
    
	public function insert_rec($data)
	{
		return $this->insert($data);	
	}
	
	public function updateData(array $data,$where) {
        
        $fields = $this->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) {
                if (!in_array($field, $fields)) {
                        unset($data[$field]);
                }
        }        
        return $this->update($data,$where);
    }
}
?>
