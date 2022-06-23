<?php
namespace Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;
use Chamilo\Libraries\DependencyInjection\Traits\ExtensionTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\DependencyInjection
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension implements ExtensionInterface
{
    use ExtensionTrait;

    public function getAlias()
    {
        return 'chamilo.core.user.integration.chamilo.libraries.rights';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\User\Integration\Chamilo\Libraries\Rights' => ['services.xml']];
    }
}