<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;

/**
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Portfolio extends ContentObject implements ComplexContentObjectSupport, ComplexContentObjectDisclosure
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Portfolio';

    /**
     * @see \libraries\architecture\ComplexContentObjectSupport::get_allowed_types()
     */
    public function get_allowed_types(): array
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Portfolio', Manager::CONTEXT . '\ContentObject'
        );
        $types = [];

        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 6
            );
            $classname = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
            $types[] = $namespace . '\Storage\DataClass\\' . $classname;
        }

        return $types;
    }
}
