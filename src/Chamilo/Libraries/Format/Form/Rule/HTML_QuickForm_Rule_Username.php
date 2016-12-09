<?php
use Chamilo\Libraries\Utilities\StringUtilities;
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
        $filteredUsername = StringUtilities :: getInstance()->createString($username)->toAscii()->__toString();
        return $filteredUsername == $username;
    }
}
