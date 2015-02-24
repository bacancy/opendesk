<?php

/**
 * Class for job table related database operations.
 *
 * @category   NIC
 * @package    Models_Job
 * @copyright  Copyright (c) 2010 - 2012 
 */

class Models_Currency extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'currency'; 		
                        
    
    
    /**
     * 
     *     
     */         	
    public function getCurrencylist() {        
        $select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('c'=>'currency'),array('c.*'));                        										
    	
    	$select = $this->fetchAll($select);
        	    							
	if($select){
            $select = $select->toArray();
            $data = array();
            foreach($select as $row){
                $data[$row['id']] = $row['name'];   
            }
            return $data;
        }else{
            return null;    
        }        
    }
    
    
    /**
     * 
     *     
     */         	
    public function getCurrencySymbol() {        
        $select = $this->select();
        $select->setIntegrityCheck(false)
                        ->from(array('c'=>'currency'),array('c.*'));                        										
    	
    	$select = $this->fetchAll($select);
        	    							
	if($select){
            $select = $select->toArray();
            $data = array();
            foreach($select as $row){
                $data[$row['id']] = $row['symbol'];   
            }
            return $data;
        }else{
            return null;    
        }        
    }
    
    
    
    
}
?>
