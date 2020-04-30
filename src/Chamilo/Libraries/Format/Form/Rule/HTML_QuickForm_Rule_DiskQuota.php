<?php
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Session;
/**
 * QuickForm rule to check if uploading a document is possible compared to the available disk quota.
 *
 * @package Chamilo\Libraries\Format\Form\Rule
 */
class HTML_QuickForm_Rule_DiskQuota extends HTML_QuickForm_Rule
{

    /**
     * Function to check if an uploaded file can be stored in the repository
     *
     * @param string[] $file Uploaded file (array)
     * @return boolean True if the filesize doesn't cause a disk quota overflow
     */
    function validate($file)
    {
        $size = $file['size'];
        $calculator = new Calculator(
            DataManager::retrieve_by_id(
                User::class,
                (int) Session::get_user_id()));

        return $calculator->canUpload($size);
    }
}