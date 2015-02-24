<?php
class Models_Form_Register extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');

        $objEmail = new Zend_Form_Element_Text('email');
        $objEmail
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Email')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Email cannot be empty!'))))
                ->addValidator('EmailAddress', true)
                ->addValidator('Db_NoRecordExists', false, array('table' => 'user', 'field' => 'email', 'messages' => array('recordFound' => 'Email is already taken') ))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');          
        $objEmail->getValidator('emailAddress')->setMessage("'%value%' is not valid email address",Zend_Validate_EmailAddress::INVALID_FORMAT);        

        $objPassword = new Zend_Form_Element_Password('password');
        $objPassword
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Password can not be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objRetypepassword = new Zend_Form_Element_Password('retypepassword');
        $objRetypepassword
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Retype New Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(new Zend_Validate_Identical('password'))
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Retype password can not be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objUsername = new Zend_Form_Element_Text('username');
        $objUsername
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Username')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Username cannot be empty!'))))                
                ->addValidator('Db_NoRecordExists', false, array('table' => 'user', 'field' => 'username', 'messages' => array('recordFound' => 'Username is already taken') ))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objFirstname = new Zend_Form_Element_Text('firstname');
        $objFirstname
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter firstname')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Firstname cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objLastname = new Zend_Form_Element_Text('lastname');
        $objLastname
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter lastname')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Lastname cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objPhone = new Zend_Form_Element_Text('phone');
        $objPhone
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Phone')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Phone cannot be empty!'))))
                ->addValidator('Digits')
                ->addValidator('stringLength', false, array(10, 15))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objCountry = new Zend_Form_Element_Select('country');
        $objCountry
		->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
		->addMultiOption('','Select country')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Please select country!'))));                
        
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
        
        $objPostalcode = new Zend_Form_Element_Text('postalcode');
        $objPostalcode
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter zip code')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Zipcode cannot be empty!'))))
                ->addValidator('Digits')
                ->addValidator('stringLength', false, array(6, 6))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
                

        $objSubmit = new Zend_Form_Element_Submit('submit');
        $objSubmit                
                ->setAttrib('value', $objTranslate->_('Login'))
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       $this->addElements(array($objEmail ,$objPassword ,$objRetypepassword ,$objUsername ,$objFirstname ,$objLastname ,$objPhone ,$objCountry ,$objCity ,$objPostalcode ,$objSubmit));
    }
    
    
}
