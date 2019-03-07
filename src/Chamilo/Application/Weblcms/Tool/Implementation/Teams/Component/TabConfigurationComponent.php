<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\CourseTeamService;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\GraphException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class TabConfigurationComponent
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component
 */
class TabConfigurationComponent extends Manager implements NoAuthenticationSupport
{

    use ContainerAwareTrait;

    /**
     * @return string
     */
    public function run(): string
    {
        return 'Hello World!';
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}