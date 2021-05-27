<?php
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
     * @param string[] $file Uploaded file
     * @param string[] $extensions Allowed extensions
     * @return boolean True if filetype is allowed
     */
    public function validate($file, $extensions = [])
    {
        $parts = explode('.', $file['name']);
        if (count($parts) < 2)
        {
            return false;
        }
        $ext = $parts[count($parts) - 1];
        return in_array($ext, $extensions);
    }
}
