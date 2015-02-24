<?php
//class Model_Form_AdminLogin extends PS_Form
class Models_Form_Login extends PS_Form
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
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    

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

        $objSubmit = new Zend_Form_Element_Submit('submit');
        $objSubmit                
                ->setAttrib('value', $objTranslate->_('Login'))
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       $this->addElements(array($objEmail,$objPassword,$objSubmit));
    }
    
    
}
