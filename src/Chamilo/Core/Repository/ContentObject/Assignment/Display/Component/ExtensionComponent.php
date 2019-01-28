<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExtensionComponent extends Manager
{
    const PARAM_EXTENSION = 'Extension';

    /**
     * @return string
     * @throws \Exception
     */
    function run()
    {
        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        $extensionNamespace = $this->getRequest()->getFromUrl(self::PARAM_EXTENSION);
        if (strpos($extensionNamespace, 'Chamilo\Core\Repository\ContentObject\Assignment\Extension') === false)
        {
            throw new \RuntimeException(
                'The given namespace ' . $extensionNamespace . ' is not a valid extension from assignment'
            );
        }

        return $this->getApplicationFactory()->getApplication($extensionNamespace, $applicationConfiguration)->run();
    }
}