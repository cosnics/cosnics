<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.application.weblcms.course.open_course';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Application\Weblcms\Course\OpenCourse' => [
                'package.xml',
                'repository.xml',
                'services.xml',
                'tables.xml'
            ]
        ];
    }
}