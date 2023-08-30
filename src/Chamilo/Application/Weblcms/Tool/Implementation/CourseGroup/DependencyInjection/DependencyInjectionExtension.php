<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Interfaces\ICompilerPassExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
    implements ExtensionInterface, ICompilerPassExtension
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.course_group';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup' => ['package.xml', 'services.xml']];
    }

    public function registerCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CourseGroupDecoratorsCompilerPass());
    }
}