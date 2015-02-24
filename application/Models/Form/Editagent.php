<?php
class Models_Form_Editagent extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
        
        /*$objName = new Zend_Form_Element_Text('name');
        $objName
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Your Name')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Name cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objEmail = new Zend_Form_Element_Text('email');
        $objEmail
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Email')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Email cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');*/
		
		$objMobile = new Zend_Form_Element_Text('mobile');
        $objMobile
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Mobile')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Mobile cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
				
        $objAddress = new Zend_Form_Element_Text('address');
        $objAddress
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Address')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Address cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
		
		$objCity = new Zend_Form_Element_Text('city');
        $objCity
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter city')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('City cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
		
		$objState = new Zend_Form_Element_Text('state');
        $objState
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter State')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('State cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
		
        $objCountry = new Zend_Form_Element_Text('country');
        $objCountry
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter country')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Country cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
		$objWorkprofile = new Zend_Form_Element_Text('workprofile');
        $objWorkprofile
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Workprofile')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Workprofile cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
		
		$objDesc = new Zend_Form_Element_Text('desc');
        $objDesc
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Desc')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Desc cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objProfile_pic = new Zend_Form_Element_File('profile_pic');
        $objProfile_pic
                ->setRequired(true)                
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Profile Pic cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Save');
        $objSubmit                                
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       //$this->addElements(array($objName,$objEmail,$objMobile,$objAddress,$objCity,$objState,$objCountry,$objWorkprofile,$objDesc,$objProfile_pic,$objSubmit));
	   $this->addElements(array($objMobile,$objAddress,$objCity,$objState,$objCountry,$objWorkprofile,$objDesc,$objProfile_pic,$objSubmit));
    }
    
    
}
