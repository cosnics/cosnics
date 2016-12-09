<?php
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * QuickForm rule to check if uploading a document is possible compared to the available disk quota.
 */
class HTML_QuickForm_Rule_DiskQuota extends \HTML_QuickForm_Rule
{

    /**
     * Function to check if an uploaded file can be stored in the repository
     *
     * @see HTML_QuickForm_Rule
     * @param mixed $file Uploaded file (array)
     * @return boolean True if the filesize doesn't cause a disk quota overflow
     */
    public function validate($file)
    {
        $size = $file['size'];

        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) Session :: get_user_id()));

        return $calculator->canUpload($size);
    }
}
