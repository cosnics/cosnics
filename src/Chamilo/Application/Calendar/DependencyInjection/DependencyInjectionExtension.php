<?php
namespace Chamilo\Application\Calendar\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 *
 * @package Chamilo\Application\Calendar\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.application.calendar';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Calendar' => ['services.xml']];
    }
}