<?php
class PS_Form_Decorator_PSWrapper extends Zend_Form_Decorator_Abstract
{
    /**
     * Default placement: surround content
     * @var string
     */
    protected $_placement = null;

    /**
     * Render
     *
     * Renders as the following:
     * <dt></dt>
     * <dd>$content</dd>
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $allowedTags = "<input> <textarea> <select> <option> <optgroup> <isindex> <object> <button> <fieldset> <legend>";
        $strContent = strip_tags($content,$allowedTags);
        //echo htmlentities($content);
        return $strContent;
    }
}
