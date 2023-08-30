<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Configuration\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.weblcms.tool.implementation.assignment';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment' => [
                'package.xml',
                'services.xml',
                'repository.xml'
            ]
        ];
    }
}