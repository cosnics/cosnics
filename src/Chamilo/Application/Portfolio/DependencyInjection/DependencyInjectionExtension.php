<?php
namespace Chamilo\Application\Portfolio\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 *
 * @package Chamilo\Application\Portfolio\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.application.portfolio';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Application\Portfolio' => ['services.xml']];
    }
}