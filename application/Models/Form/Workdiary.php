<?php
class Models_Form_Workdiary extends PS_Form
{

    public function init()
    {
        parent::init();
        $objTranslate = Zend_Registry::get(PS_App_Zend_Translate);
        $this->setMethod('post');       
        
        $objMemo = new Zend_Form_Element_Text('memo');
        $objMemo
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter Memo')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Memo cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                
        
        $objTimeloged = new Zend_Form_Element_Text('time_loged');
        $objTimeloged
                ->setRequired(true)
                ->setAttrib('class','form-control')
                ->setAttrib('placeholder','Enter time loged')                
                ->addFilter('StripTags')
                ->addFilter('StringTrim')                
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Timeloged cannot be empty!'))))
                ->removeDecorator('Errors')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');
        
//        $objkeystrokecount = new Zend_Form_Element_Text('keystroke_count');
//        $objkeystrokecount
//                ->setRequired(true)
//                ->setAttrib('class','form-control')
//                ->setAttrib('placeholder','Enter keystroke count')
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim')                
//                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('keystrokecount cannot be empty!'))))
//                ->removeDecorator('Errors')
//                ->removeDecorator('DtDdWrapper')
//                ->removeDecorator('Label');
//        
//        $objMousestrokecount = new Zend_Form_Element_Text('mousestroke_count');
//        $objMousestrokecount
//                ->setRequired(true)
//                ->setAttrib('class','form-control')
//                ->setAttrib('placeholder','Enter mousestroke count')
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim')                
//                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('Mousestrokecount cannot be empty!'))))
//                ->removeDecorator('Errors')
//                ->removeDecorator('DtDdWrapper')
//                ->removeDecorator('Label');
        
//        $objScreenshot = new Zend_Form_Element_File('screenshot');
//        $objScreenshot
//                //->setRequired(true)                
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim')                
//                //->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $objTranslate->_('screenshot cannot be empty!'))))
//                ->removeDecorator('Errors')
//                ->removeDecorator('DtDdWrapper')
//                ->removeDecorator('Label');
        
        $objSubmit = new Zend_Form_Element_Submit('submit' ,'Save');
        $objSubmit                                
                ->setAttrib('class', 'btn btn-default')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');                    


       $this->addElements(array($objMemo ,$objTimeloged /*,$objkeystrokecount ,$objMousestrokecount ,$objScreenshot*/ ,$objSubmit));
    }
    
    
}
