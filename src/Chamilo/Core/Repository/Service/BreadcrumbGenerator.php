<?php
namespace Chamilo\Core\Repository\Service;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;

class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGenerator
{
    protected function generatePackageBreadcrumb(Application $application): void
    {
        if ($application instanceof Manager && $application->getUser() instanceof User)
        {
            $breadcrumb_trail = $this->getBreadcrumbTrail();

            $workspace = $application->getWorkspace();

            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = 'Chamilo\Core\Repository\Workspace';

            $workspaceUrl = $this->getUrlGenerator()->fromParameters($parameters);

            $breadcrumb_trail->add(
                new Breadcrumb(
                    $workspaceUrl, $this->getTranslator()->trans(
                    'Workspaces', [], 'Chamilo\Core\Repository\Workspace'
                )
                )
            );

            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = Manager::CONTEXT;
            $parameters[Manager::PARAM_WORKSPACE_ID] = $workspace->getId();

            $workspaceInstanceUrl = $this->getUrlGenerator()->fromParameters($parameters);

            $breadcrumb_trail->add(
                new Breadcrumb($workspaceInstanceUrl, $workspace->getTitle())
            );
        }
    }
}