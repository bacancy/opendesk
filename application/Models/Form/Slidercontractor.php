<?php
//class Model_Form_AdminLogin extends PS_Form
class Models_Form_Slidercontractor extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');
		
		$objType = new Zend_Form_Element_Select("type");
		$objType
				->setLabel("Find User type")  
				->setName("type")
				->setAttrib('class','form-control')
				->addMultiOption('1','Contractor')
				->addMultiOption('2','Employee')
				->setValue('Contractor');

		
        $objFind = new Zend_Form_Element_Text('find');
        $objFind
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Find user')
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


       $this->addElements(array($objType,$objFind,$objSubmit));
    }
    
    
}
