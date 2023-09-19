<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosureInterface;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Portfolio extends ContentObject implements ComplexContentObjectSupportInterface, ComplexContentObjectDisclosureInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Portfolio';

    public function get_allowed_types(): array
    {
        $classnameUtilities = $this->getClassnameUtilities();

        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Portfolio', Manager::CONTEXT . '\ContentObject'
        );

        $types = [];

        foreach ($registrations as $registration)
        {
            $namespace = $classnameUtilities->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 6
            );
            $classname = $classnameUtilities->getPackageNameFromNamespace($namespace);
            $types[] = $namespace . '\Storage\DataClass\\' . $classname;
        }

        return $types;
    }
}
