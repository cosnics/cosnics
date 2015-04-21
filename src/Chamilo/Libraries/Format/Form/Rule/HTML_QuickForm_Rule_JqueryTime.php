<?php

/**
 * QuickForm rule to check a string to macht a time format hh:mm
 */
class HTML_QuickForm_Rule_JqueryTime extends HTML_QuickForm_Rule
{

    /**
     * Function to check a date
     * 
     * @see HTML_QuickForm_Rule
     * @param string $time
     * @return boolean True if time is valid
     */
    public function validate($time)
    {
        if (preg_match('/^([0][0-9]|[1][0-9]|2[0-4]):([0-5][0-9])$/', $time))
        {
            return true;
        }
        else
        {
            
            return false;
        }
    }
}
