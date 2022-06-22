<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.application.weblcms.course.open_course';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Course\OpenCourse' => ['repository.xml', 'services.xml']];
    }
}