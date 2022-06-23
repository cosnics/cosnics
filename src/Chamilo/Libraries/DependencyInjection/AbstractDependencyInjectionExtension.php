<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractDependencyInjectionExtension extends Extension
{

    public function getPathBuilder(): PathBuilder
    {
        return new PathBuilder(new ClassnameUtilities(new StringUtilities()), ChamiloRequest::createFromGlobals());
    }

}