<?php
class Models_Form_Changeforgotpassword extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');
                        
        $objPassword = new Zend_Form_Element_Password('password');
        $objPassword
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter New Password')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('New password can not be empty!'))))
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

        $objSubmit = new Zend_Form_Element_Submit('submit');
        $objSubmit                
                ->setAttrib('value', $objTranslate->_('change'))
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       $this->addElements(array($objPassword ,$objRetypepassword ,$objSubmit));
    }
    
    
}
