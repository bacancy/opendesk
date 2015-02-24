<?php
class Models_Form_Contract extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
                            
        
        $objTitle = new Zend_Form_Element_Text('title');
        $objTitle
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Title cannot be empty!'))))
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
        
        $objStatus = new Zend_Form_Element_Select('status');
        $objStatus
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Status cannot be empty!'))))
                ->addMultiOptions(array(
        		"1"=>"active",
        		"2"=>"paused",
                        "3"=>"closed",
                        "4"=>"End",
                ));
        
        $objManualAllowed = new Zend_Form_Element_Select('manual_allowed');
        $objManualAllowed
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Manual allowed cannot be empty!'))))
                ->addMultiOptions(array(
        		"0"=>"no",
        		"1"=>"yes",
                        
                ));
        
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Save');
        $objSubmit                                
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       $this->addElements(array($objTitle, $objDescription, $objStatus, $objManualAllowed, $objSubmit));
    }
    
    
}
