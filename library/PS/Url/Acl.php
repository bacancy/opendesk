<?php 
class PS_Url_Acl
{
	public function accessControl($Module,$Controller,$Action) {
		$acl = Zend_Registry::get(PS_App_Acl);
		
		$storage = new Zend_Auth_Storage_Session();			
		$usersNs = $storage->read();	
		
		if(!isset($usersNs->admin_type)){
			$roleName='guest';
		} else {
			$roleName=$usersNs->admin_type;
		}
		
		$privilageName = $Controller . '/' . $Action;
			
		if($acl->isAllowed($roleName,null,strtolower($privilageName))){
			return true;
		}
		
		return false;
	}
}
?>