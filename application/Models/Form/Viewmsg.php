<?php
//class Model_Form_AdminLogin extends PS_Form
class Models_Form_Viewmsg extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');
		
		$objReply = new Zend_Form_Element_Textarea('reply');
        $objReply
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Send Reply')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
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


       $this->addElements(array($objReply,$objSubmit));
    }
    
    
}
