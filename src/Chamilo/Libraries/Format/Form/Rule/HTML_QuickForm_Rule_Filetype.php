<?php
namespace Chamilo\Libraries\Format\Form\Rule;

use HTML_QuickForm_Rule;

/**
 * QuickForm rule to check if a filetype
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_Filetype extends HTML_QuickForm_Rule
{

    /**
     * Function to check if a filetype is allowed
     *
     * @param string[] $value   Uploaded file
     * @param string[] $options Allowed extensions
     *
     * @return bool True if filetype is allowed
     */
    public function validate($value, $options = []): bool
    {
        $parts = explode('.', $value['name']);

        if (count($parts) < 2)
        {
            return false;
        }

        $ext = $parts[count($parts) - 1];

        return in_array($ext, $options);
    }
}
