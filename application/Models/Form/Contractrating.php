<?php
class Models_Form_Contractrating extends PS_Form
{

    public function init()
    {
        parent::init();        
    }
    
    
    public function contractor()
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
                						        
        $objContractorreview = new Zend_Form_Element_Textarea('contractor_review');
        $objContractorreview
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter review')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Review cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                
        
        $objContractorrating = new Zend_Form_Element_Select('contractor_rating');
        $objContractorrating
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Rating cannot be empty!'))))
                ->addMultiOptions(array(
        		"0"=>"0",
                        "1"=>"1",
        		"2"=>"2",
                        "3"=>"3",
                        "4"=>"4",
                        "5"=>"5",
                ));
                                      
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Save');
        $objSubmit                                
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    

        
       $this->addElements(array($objContractorreview, $objContractorrating, $objSubmit));
    }
    
    
    public function employer()
    {
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
                						        
        $objEmployerreview = new Zend_Form_Element_Textarea('employer_review');
        $objEmployerreview
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter review')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Review cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                
        
        $objEmployerrating = new Zend_Form_Element_Select('employer_rating');
        $objEmployerrating
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Rating cannot be empty!'))))
                ->addMultiOptions(array(
        		"0"=>"0",
                        "1"=>"1",
        		"2"=>"2",
                        "3"=>"3",
                        "4"=>"4",
                        "5"=>"5",
                ));
                                      
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Save');
        $objSubmit                                
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    

        
       $this->addElements(array($objEmployerreview, $objEmployerrating, $objSubmit));
    }
    
    
}
