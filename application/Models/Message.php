<?php

/**
 * Class for agency table related database operations.
 *
 * @category   Agency
 * @package    Models_Agent
 * @copyright  Copyright (c) 2010 - 2013 
 */

class Models_Message extends PS_Database_Table
{  
    
    /**
     * The table name.
     *
     * @var array|string
     */
	protected $_name = 'message'; 		
    
	public function insert_msg($data)
	{
		$data['sent_date'] = time();
		return $this->insert($data);
	}
	
	public function messages($sender)
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('m'=>'message')
                    ,array('m.id','m.message','m.subject','m.sender','m.sent_date')
				)
                /*->join(array('c' => 'conversation') ,'c.msg_id = m.id' 
                    ,array('c.c_id','c.date')
                )*/
                ->join(array('u' => 'user') ,'m.sender = u.username' 
                    ,array('u.username')
                )
                ->where('m.sender = ?' ,$sender);
				
    	
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
                    ,array('m.id','m.message','m.subject','m.sender','m.sent_date')
				)				
                ->where('m.id = ?' ,$msg_id);
    	
    	$select = $this->fetchRow($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	
	public function get_replys($msg_id)
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('c'=>'conversation')
                    ,array('c.reply_by','c.reply','c.date')
				)                
                ->where('c.msg_id = ?' ,$msg_id);
    	
    	$select = $this->fetchAll($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	
	public function participated_users($msg_id)
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('s'=>'sent_message')
                    ,array('s.receiver_name')
				)                
                ->where('s.msg_id = ?' ,$msg_id);
    	
    	$select = $this->fetchAll($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	
	public function inbox_msg($username)
	{
		$select = $this->select();
        $select->setIntegrityCheck(false)
                ->from(array('sm'=>'sent_message')
                    ,array('sm.receiver_name'))				
				->join(array('m' => 'message') ,'sm.msg_id = m.id'
                    ,array('m.id', 'm.sender', 'm.message', 'm.subject', 'm.sent_date'))
                ->where('sm.receiver_name = ?' ,$username);
    	
    	$select = $this->fetchAll($select);
		$select = $select->toArray();

		if($select)
			return $select;
		else
			return null;
	}
	// SELECT m.id, m.sender, m.message, m.subject, m.sent_date FROM `sent_message` `sm` , `message` `m` WHERE sm.msg_id = m.id AND sm.receiver_name = "ayyappa" LIMIT 0 , 30
}
?>
