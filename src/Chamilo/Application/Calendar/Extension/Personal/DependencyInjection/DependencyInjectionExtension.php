<?php
namespace Chamilo\Application\Calendar\Extension\Personal\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.application.calendar.extension.personal';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Calendar\Extension\Personal' => ['services.xml']];
    }
}