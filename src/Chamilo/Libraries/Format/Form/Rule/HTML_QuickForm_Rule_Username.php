<?php
use Chamilo\Libraries\Utilities\StringUtilities;
/**
 * QuickForm rule to check if a username is of the correct format
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_Username extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a username is of the correct format
     *
     * @param string $username Wanted username
     * @return boolean True if username is of the correct format
     */
    public function validate($username, $options = null)
    {
        $filteredUsername = StringUtilities::getInstance()->createString($username)->toAscii()->__toString();
        return $filteredUsername == $username;
    }
}
