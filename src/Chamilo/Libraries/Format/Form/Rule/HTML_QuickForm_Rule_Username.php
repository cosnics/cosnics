<?php
/**
 *
 * @package common.html.formvalidator.Rule
 */
/**
 * QuickForm rule to check if a username is of the correct format
 */
class HTML_QuickForm_Rule_Username extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is of the correct format
     * 
     * @see HTML_QuickForm_Rule
     * @param string $username Wanted username
     * @return boolean True if username is of the correct format
     */
    public function validate($username)
    {
        $filtered_username = eregi_replace(
            '[^a-z0-9_.-@]', 
            '_', 
            strtr(
                $username, 
                '�����������������������������������������������������', 
                'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn'));
        return $filtered_username == $username;
    }
}
