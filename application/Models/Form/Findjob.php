<?php
class Models_Form_Findjob extends PS_Form
{
    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
        
        $objCategory = new Zend_Form_Element_Select('category');
        $objCategory
                ->setRequired(true)
                ->setAttrib('class','form-control')
				->addMultiOption('','Select')
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
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Search');
        $objSubmit
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

       $this->addElements(array($objCategory, $objTitle, $objSubmit));
    }
}
