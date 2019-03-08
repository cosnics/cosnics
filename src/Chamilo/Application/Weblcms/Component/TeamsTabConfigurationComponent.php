<?php

namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TabConfigurationComponent
 * @todo: location
 */
class TeamsTabConfigurationComponent extends Manager implements NoAuthenticationSupport
{

    use ContainerAwareTrait;

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run(): string
    {
        return $this->getTwig()->render(
            'Chamilo\Application\Weblcms:TeamsTabConfiguration.html.twig'
        );
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}