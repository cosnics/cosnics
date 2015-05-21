<?php
class HTML_QuickForm_advmultiselect_chamilo extends HTML_QuickForm_advmultiselect
{

    function HTML_QuickForm_advmultiselect_chamilo($elementName = null, $elementLabel = null, $options = null, $attributes = null, 
        $sort = null)
    {
        HTML_QuickForm_advmultiselect :: HTML_QuickForm_advmultiselect(
            $elementName, 
            $elementLabel, 
            $options, 
            $attributes, 
            $sort);
    }

    function getElementJs($raw = true, $min = false)
    {
        $js = __DIR__ . '/../../../../../../vendor/pear-pear.php.net/HTML_QuickForm_advmultiselect/data' .
             DIRECTORY_SEPARATOR . 'HTML_QuickForm_advmultiselect/HTML/QuickForm' . DIRECTORY_SEPARATOR;
        
        if ($min)
        {
            $js .= 'qfamsHandler-min.js';
        }
        else
        {
            $js .= 'qfamsHandler.js';
        }
        
        if (file_exists($js))
        {
            $js = file_get_contents($js);
        }
        else
        {
            $js = '';
        }
        
        if ($raw !== true)
        {
            $js = '<script type="text/javascript">' . PHP_EOL . '//<![CDATA[' . PHP_EOL . $js . PHP_EOL . '//]]>' .
                 PHP_EOL . '</script>' . PHP_EOL;
        }
        return $js;
    }
}