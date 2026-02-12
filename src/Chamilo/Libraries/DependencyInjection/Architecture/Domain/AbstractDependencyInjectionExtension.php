<?php
namespace Chamilo\Libraries\DependencyInjection\Architecture\Domain;

use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @package Chamilo\Libraries\DependencyInjection\Architecture\Domain
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractDependencyInjectionExtension extends Extension
{
    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return new SystemPathBuilder(new ClassnameUtilities(new StringUtilities()));
    }
}