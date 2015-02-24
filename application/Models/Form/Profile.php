<?php
class Models_Form_Profile extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
        
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
				
	$objProfiletitle = new Zend_Form_Element_Text('profile_title');
        $objProfiletitle
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Profile title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Profile title cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
		
	$objProfileDesc = new Zend_Form_Element_Textarea('profile_desc');
        $objProfileDesc
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Your Description')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Profile Description cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objProfileHourlyRate = new Zend_Form_Element_Text('profile_hourly_rate');
        $objProfileHourlyRate
                //->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Profile title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                //->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Hourly Rate cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
                        
        $objPortrait = new Zend_Form_Element_File('portrait');
        $objPortrait
                //->setRequired(true)                
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                //->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('portrait cannot be empty!'))))
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


       $this->addElements(array($objFirstname ,$objLastname ,$objCountry ,$objCity ,$objPortrait ,$objSubmit ,$objProfiletitle ,$objProfileDesc ,$objProfileHourlyRate));
	   
	
    }
    
    
}
