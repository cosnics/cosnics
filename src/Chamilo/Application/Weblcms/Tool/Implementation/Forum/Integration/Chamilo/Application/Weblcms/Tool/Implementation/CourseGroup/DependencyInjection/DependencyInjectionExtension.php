<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.forum.integration.chamilo.application.weblcms.tool.implementation.course_group';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup' => ['services.xml']];
    }

}