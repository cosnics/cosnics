<?php
namespace Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar\DependencyInjection
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.calendar.extension.google.integration.chamilo.application.calendar';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Calendar\Extension\Google\Integration\Chamilo\Application\Calendar' => ['package.xml']];
    }
}