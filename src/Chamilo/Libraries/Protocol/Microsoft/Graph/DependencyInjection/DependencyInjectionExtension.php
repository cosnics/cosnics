<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.libraries.protocol.microsoft.graph';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Protocol\Microsoft\Graph' => ['services.xml']];
    }
}