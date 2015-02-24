<?php

class PS_Form extends Zend_Form {

    protected $_fileTransferAdapter;

    function __construct($options = null) {
        parent::__construct($options);
    }

    public function init()
    {
        parent::init();
		$this->addElementPrefixPath('PS_Form_Decorator','PS/Form/Decorator','decorator');
        $this->_fileTransferAdapter = new PS_File_Transfer_Adapter_Http();
    }
    
    /**
     * Add a new element
     *
     * $element may be either a string element type, or an object of type
     * Zend_Form_Element. If a string element type is provided, $name must be
     * provided, and $options may be optionally provided for configuring the
     * element.
     *
     * If a Zend_Form_Element is provided, $name may be optionally provided,
     * and any provided $options will be ignored.
     *
     * @param  string|Zend_Form_Element $element
     * @param  string $name
     * @param  array|Zend_Config $options
     * @return PS_Form
     */
    public function addElement($element, $name = null, $options = null)
    {
        $element
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->removeDecorator('Errors')
            ->removeDecorator('DtDdWrapper')
            ->removeDecorator('Label')
            ->removeDecorator('HtmlTag');
            /*->addDecorator('PSWrapper');*/
        return parent::addElement($element, $name, $options);
    }    
}