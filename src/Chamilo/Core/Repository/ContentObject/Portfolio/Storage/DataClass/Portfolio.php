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
 * Portfolio constent object
 * 
 * @package repository\content_object\portfolio$Portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Portfolio extends ContentObject implements ComplexContentObjectSupport, ComplexContentObjectDisclosure
{

    /**
     *
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    /**
     *
     * @see \libraries\architecture\ComplexContentObjectSupport::get_allowed_types()
     */
    public function get_allowed_types()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Portfolio', 
            Manager::package() . '\ContentObject');
        $types = array();
        
        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 
                6);
            $classname = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
            $types[] = $namespace . '\Storage\DataClass\\' . $classname;
        }
        
        return $types;
    }
}
