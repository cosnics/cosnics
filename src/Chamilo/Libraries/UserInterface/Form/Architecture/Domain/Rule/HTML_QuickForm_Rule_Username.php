<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Rule;

use HTML_QuickForm_Rule;
use Stringy\Stringy;

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
     * @param string $value Wanted username
     *
     * @return bool True if username is of the correct format
     */
    public function validate($value, $options = null): bool
    {
        $filteredUsername = Stringy::create($value, 'UTF-8')->toAscii()->__toString();

        return $filteredUsername == $value;
    }
}
