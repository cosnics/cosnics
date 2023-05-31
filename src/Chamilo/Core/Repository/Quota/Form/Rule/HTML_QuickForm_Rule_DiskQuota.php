<?php
namespace Chamilo\Core\Repository\Quota\Form\Rule;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use HTML_QuickForm_Rule;

/**
 * QuickForm rule to check if uploading a document is possible compared to the available disk quota.
 */
class HTML_QuickForm_Rule_DiskQuota extends HTML_QuickForm_Rule
{

    public function getSessionUtilities(): SessionUtilities
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionUtilities::class);
    }

    /**
     * Function to check if an uploaded file can be stored in the repository
     *
     * @param mixed $file Uploaded file (array)
     *
     * @return bool True if the filesize doesn't cause a disk quota overflow
     * @throws \ReflectionException
     */
    public function validate($value, $options = null): bool
    {
        $size = $value['size'];

        $calculator = new Calculator(
            DataManager::retrieve_by_id(
                User::class, (int) $this->getSessionUtilities()->getUserId()
            )
        );

        return $calculator->canUpload($size);
    }
}
