<?php
class Models_Form_Job extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
        
        $objCategoryid = new Zend_Form_Element_Select('category_id');
        $objCategoryid
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Category id cannot be empty!'))))                
                ->addMultiOptions(array());                
        
        $objTitle = new Zend_Form_Element_Text('title');
        $objTitle
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Title cannot be empty!'))))
                ->addValidator('StringLength', true,array(5, 255,'messages'=>array(Zend_Validate_StringLength::TOO_SHORT =>$objTranslate->_('Job title \'%value%\' is too short. Min 5 characters expecting'),Zend_Validate_StringLength::TOO_LONG  =>$objTranslate->_('Job Title Max 255, You have entered string too long'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');				
		
        
        $objDescription = new Zend_Form_Element_Textarea('description');
        $objDescription
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Description')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Description cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                
        
        $objType = new Zend_Form_Element_Select('type');
        $objType
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Type cannot be empty!'))))
                ->addMultiOptions(array(
        		"1"=>"fixed cost job",
        		"2"=>"hourly cost job",
                ));
        
        $objHourlyRate = new Zend_Form_Element_Text('hourly_rate');
        $objHourlyRate
                //->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter hourly rate')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')  
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Hourly rate cannot be empty!'))))
		->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
                $validator=new Zend_Validate_Digits();
		$objHourlyRate->addValidator($validator,true);
		$validator=new Zend_Validate_GreaterThan(0);
		$objHourlyRate->addValidator($validator,true);
        
        $objEstimate = new Zend_Form_Element_Text('estimate');
        $objEstimate
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter estimate')
                ->addFilter('StripTags')				
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Estimate cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                
                $validator=new Zend_Validate_Digits();
		$objEstimate->addValidator($validator,true);
		$validator=new Zend_Validate_GreaterThan(1);
		$objEstimate->addValidator($validator,true);
        
       $objDurationStart = new Zend_Form_Element_Text('duration_start');
       $objDurationStart
                ->setRequired(true)
                ->setAttrib('class','form-control datepicker')
                ->setAttrib('placeholder','Enter duration From')
                ->addFilter('StripTags')				
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Duration start cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
       
       $objDurationEnd = new Zend_Form_Element_Text('duration_end');
       $objDurationEnd
                ->setRequired(true)
                ->setAttrib('class','form-control datepicker')
                ->setAttrib('placeholder','Enter duration To')
                ->addFilter('StripTags')				
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Duration end cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
                
        $objCurrencyId = new Zend_Form_Element_Select('currency_id');
        $objCurrencyId
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Currency id cannot be empty!'))))                
                ->addMultiOptions(array());              
        
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Save');
        $objSubmit                                
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       $this->addElements(array($objCategoryid, $objTitle, $objDescription, $objType, $objHourlyRate, $objEstimate, $objDurationStart, $objDurationEnd, $objCurrencyId, $objSubmit));
    }
    
    
}
