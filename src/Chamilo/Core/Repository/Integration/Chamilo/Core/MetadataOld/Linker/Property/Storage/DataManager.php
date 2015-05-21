<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\PropertyProvider\PropertyProviderInterface;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * The DataManager for this package
 *
 * @package repository\integration\core\metadata\linker\property
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * Returns the implementation packages for this package
     *
     * @return string[]
     */
    public static function get_implementation_packages()
    {
        $pattern = 'Chamilo\Core\Repository\ContentObject*\\\MetadataPropertyLinker';

        $condition = new PatternMatchCondition(
            new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_CONTEXT),
            $pattern);

        $packages = array();

        $package_registrations = \Chamilo\Configuration\Storage\DataManager :: retrieves(
            Registration :: class_name(),
            $condition);
        while ($package_registration = $package_registrations->next_result())
        {
            $packages[] = $package_registration->get_context();
        }

        return $packages;
    }

    /**
     * Retrieves the property provider from the given package and checks if it's a valid class
     *
     * @param string $implementation_package
     *
     * @throws \InvalidArgumentException
     *
     * @return PropertyProvider
     */
    public static function get_property_provider_from_implementation($implementation_package)
    {
        $property_provider_class = $implementation_package . '\PropertyProvider';
        if (class_exists($property_provider_class))
        {
            $property_provider = new $property_provider_class();

            if ($property_provider instanceof PropertyProviderInterface)
            {
                return $property_provider;
            }
        }

        throw new \InvalidArgumentException(
            'The given implementation package ' . $implementation_package . ' does not have a valid property provider');
    }

    /**
     * Retrieves the ContentObjectPropertyRelMetadataElements from the given content object type
     *
     * @param string $content_object_type
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_content_object_property_rel_metadata_elements_by_content_object_type(
        $content_object_type = null)
    {
        $condition_value = $content_object_type ? new StaticConditionVariable($content_object_type) : null;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPropertyRelMetadataElement :: class_name(),
                ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE),
            $condition_value);

        return self :: retrieves(ContentObjectPropertyRelMetadataElement :: class_name(), $condition);
    }
}