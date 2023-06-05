<?php
namespace Chamilo\Core\Repository\Quota\Form\Rule;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use HTML_QuickForm_Rule;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * QuickForm rule to check if uploading a document is possible compared to the available disk quota.
 */
class HTML_QuickForm_Rule_DiskQuota extends HTML_QuickForm_Rule
{

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
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
                User::class, (int) $this->getSession()->get(Manager::class)
            )
        );

        return $calculator->canUpload($size);
    }
}
