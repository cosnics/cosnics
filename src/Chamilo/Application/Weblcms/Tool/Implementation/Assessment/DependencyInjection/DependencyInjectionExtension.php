<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.assessment';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Tool\Implementation\Assessment' => ['tables.xml']];
    }
}