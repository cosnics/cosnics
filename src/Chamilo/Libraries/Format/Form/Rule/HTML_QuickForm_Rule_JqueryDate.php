<?php


use Chamilo\Libraries\Platform\Translation;
/**
 * @package common.html.formvalidator.Rule
 */

/**
 * QuickForm rule to check a date has the format dd-mm-yyyy
 */
class HTML_QuickForm_Rule_JqueryDate extends HTML_QuickForm_Rule
{

    /**
     * Function to check a date
     * @see HTML_QuickForm_Rule
     * @param $date An string representing a date
     * @return boolean True if date is valid
     */
    public function validate($date)
    {

        $language = Translation :: getInstance()->getLanguageIsocode();

        if($language == 'nl')
        {
            if (preg_match('/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[012])-(20\d\d)$/', $date))
            {
                list($day, $month, $year) = explode('-', $date);
                return checkdate($month, $day, $year);
            }
            else
            {

                return false;
            }
        }

        if($language == 'en')
        {
            if (preg_match('/^(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])\/(20\d\d)$/', $date))
            {
                list($month, $day, $year) = explode('/', $date);
                return checkdate($month, $day, $year);
            }
            else
            {

                return false;
            }
        }
    }

}
