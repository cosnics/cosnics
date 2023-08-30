<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.libraries.protocol.microsoft.graph';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Libraries\Protocol\Microsoft\Graph' => ['package.xml', 'services.xml']];
    }
}