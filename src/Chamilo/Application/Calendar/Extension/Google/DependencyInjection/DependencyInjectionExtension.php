<?php
namespace Chamilo\Application\Calendar\Extension\Google\DependencyInjection;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Libraries\DependencyInjection\Architecture\Domain\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias(): string
    {
        return 'chamilo.application.calendar.extension.google';
    }

    public function getConfigurationFiles(): array
    {
        return [
            Manager::CONTEXT => [
                'application.php',
                'implementation.calendar.php',
                'service.php',
                'storage.php'
            ]
        ];
    }
}