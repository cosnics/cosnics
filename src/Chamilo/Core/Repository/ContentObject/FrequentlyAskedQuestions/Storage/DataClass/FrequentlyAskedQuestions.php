<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectDisclosure;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;

/**
 * Portfolio constent object
 *
 * @package repository\content_object\portfolio$Portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FrequentlyAskedQuestions extends ContentObject implements ComplexContentObjectSupport,
    ComplexContentObjectDisclosure
{

    /**
     *
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }

    /**
     *
     * @see \libraries\architecture\ComplexContentObjectSupport::get_allowed_types()
     */
    public function get_allowed_types()
    {
        $classNameUtilities = ClassnameUtilities :: getInstance();
        $configuration = Configuration :: get_instance();

        $registrations = $configuration->getIntegrationRegistrations(self :: package());
        $types = array();

        foreach ($registrations as $registration)
        {
            $type = $registration[Registration :: PROPERTY_TYPE];
            $parentContext = $classNameUtilities->getNamespaceParent($type);
            $parentRegistration = $configuration->get_registration($parentContext);

            if ($parentRegistration[Registration :: PROPERTY_TYPE] ==
                 \Chamilo\Core\Repository\Manager :: context() . '\ContentObject')
            {
                $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent(
                    $registration[Registration :: PROPERTY_CONTEXT],
                    6);
                $types[] = $namespace . '\Storage\DataClass\\' .
                     ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($namespace);
            }
        }

        return $types;
    }
}
