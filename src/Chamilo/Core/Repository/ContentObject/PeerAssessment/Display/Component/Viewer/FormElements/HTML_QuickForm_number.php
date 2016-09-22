<?php
require_once 'HTML/QuickForm/input.php';
class HTML_QuickForm_number extends HTML_QuickForm_input
{

    /**
     * HTML5 number input
     * 
     * @param string $elementName
     * @param string $elementLabel
     * @param integer $min
     * @param integer $max
     * @param integer $step
     * @param array $attributes
     */
    function __construct($elementName = null, $elementLabel = null, $min = null, $max = null, $step = null, array $attributes = null)
    {
        parent :: __construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->setType('number');
        if (isset($min))
            $this->setMin($min);
        if (isset($max))
            $this->setMax($max);
        if (isset($step))
            $this->setStep($step);
        
        // dump($this->getAttributes()); exit;
    }

    function setMin($min)
    {
        $this->updateAttributes(array('min' => $min));
    }

    function setMax($max)
    {
        $this->updateAttributes(array('max' => $max));
    }

    function setStep($step)
    {
        $this->updateAttributes(array('step' => $step));
    }
}