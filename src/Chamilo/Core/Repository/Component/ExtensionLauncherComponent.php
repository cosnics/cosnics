<?php

namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Interfaces\WorkspaceExtensionSupport;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Exception;

/**
 * @package Chamilo\Core\Repository\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExtensionLauncherComponent extends Manager implements DelegateComponent
{
    const PARAM_EXTENSION_CONTEXT = 'ExtensionContext';

    /**
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    function run()
    {
        if (! RightsService::getInstance()->canViewContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $extensionContext = $this->getRequest()->getFromUrl(self::PARAM_EXTENSION_CONTEXT);
        if (empty($extensionContext))
        {
            throw new NoObjectSelectedException($this->getTranslator()->trans('Context', [], Manager::class));
        }

        $application = $this->getApplicationFactory()->getApplication(
            $extensionContext,
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        );

        if(!$application instanceof WorkspaceExtensionSupport)
        {
            throw new Exception(sprintf('The given context %s does not support the workspace extension', $extensionContext));
        }

        return $application->run();
    }
}