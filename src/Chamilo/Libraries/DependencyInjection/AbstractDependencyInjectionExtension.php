<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractDependencyInjectionExtension extends Extension
{

    public function getPathBuilder(): SystemPathBuilder
    {
        return new SystemPathBuilder(new ClassnameUtilities(new StringUtilities()));
    }

}