<?php
namespace Chamilo\Core\Group\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\Group\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.core.group';
    }

    public function getConfigurationFiles(): array
    {
        return [
            'Chamilo\Core\Group' => [
                'architecture.eventDispatcher.php',
                'implementation.admin.php',
                'implementation.user.php',
                'service.php',
                'storage.php',
                'userInterface.menu.php',
                'userInterface.table.php'
            ]
        ];
    }
}