<?php

/**
 * Class for setting table related database operations.
 *
 * @category   NIC
 * @package    Models_GeneralSetting
 * @copyright  Copyright (c) 2010 - 2012
 */
class Models_GeneralSetting extends PS_Database_Table
{
	protected $_name = 'settings';
	protected $_adapter;
	protected $_auth;	
	
	public function getGeneralSettingRec()
	{
		$result =	$this->fetchAll()->toArray();
		return $result ;
	}

    /**
     * Update data for settings.
     * @param  $varname gives setting var name 
   	 * $setting_value set values
     * @return array
     *
     */         
	//---- Record update by settings_varname --------//	
	public function updateGernalSettingRec($varname, $setting_value) 
	{
		$data = array('settings_value' => $setting_value );
		$where = $this->getAdapter()->quoteInto('settings_varname =  ?', trim($varname));
		$this->update($data, $where);
		
	}		
} //-- class end
