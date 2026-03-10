<?php
namespace Chamilo\Core\Group\DependencyInjection;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
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
            Manager::CONTEXT => [
                'application.php',
                'architecture.php',
                'implementation.admin.php',
                'implementation.user.php',
                'service.php',
                'storage.php',
                'userInterface.php'
            ]
        ];
    }
}