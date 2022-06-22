<?php
namespace Chamilo\Core\User\Picture\Provider\Platform\DependencyInjection;

use Chamilo\Libraries\DependencyInjection\AbstractDependencyInjectionExtension;

/**
 * @package Chamilo\Core\User\Picture\Provider\Platform\DependencyInjection
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DependencyInjectionExtension extends AbstractDependencyInjectionExtension
{

    public function getAlias()
    {
        return 'chamilo.core.user.picture.provider.platform';
    }

    public function getConfigurationFiles(): array
    {
        return ['Chamilo\Core\User\Picture\Provider\Platform' => ['services.xml']];
    }
}