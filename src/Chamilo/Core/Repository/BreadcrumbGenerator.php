<?php
namespace Chamilo\Core\Repository;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

class BreadcrumbGenerator extends \Chamilo\Libraries\Format\Structure\BreadcrumbGenerator
{
    /**
     * Generates the breadcrumb for the package name
     */
    protected function generate_package_breadcrumb()
    {
        if ($this->getApplication()->get_user() instanceof User)
        {
            $breadcrumb_trail = $this->getBreadcrumbTrail();

            $workspace = $this->getApplication()->getCurrentWorkspace();

            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = 'Chamilo\Core\Repository\Workspace';

            $workspaceUrl = $this->getUrlGenerator()->fromParameters($parameters);

            $breadcrumb_trail->add(
                new Breadcrumb(
                    $workspaceUrl, Translation::getInstance()->getTranslation(
                    'Workspaces', null, 'Chamilo\Core\Repository\Workspace'
                )
                )
            );

            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = Manager::CONTEXT;

            if ($workspace instanceof Workspace)
            {
                $parameters[Manager::PARAM_WORKSPACE_ID] = $this->getApplication()->getCurrentWorkspace()->getId();
            }

            $workspaceInstanceUrl = $this->getUrlGenerator()->fromParameters($parameters);

            $breadcrumb_trail->add(
                new Breadcrumb($workspaceInstanceUrl, $this->getApplication()->getCurrentWorkspace()->getTitle())
            );
        }
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }
}