<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{
    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.ephorus';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Tool\Implementation\Ephorus' => ['services.xml']];
    }
}