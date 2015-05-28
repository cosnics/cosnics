<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld;

use Chamilo\Core\MetadataOld\FixedElementsProvider;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * This class provides the fixed elements that are automatically generated for a content object
 *
 * @package repository\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectMetadataFixedElementsProvider implements FixedElementsProvider
{

    /**
     * The current content object
     *
     * @var ContentObject
     */
    private $content_object;

    /**
     * Constructor
     *
     * @param ContentObject $content_object
     */
    public function __construct(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     * Returns an array with the element id and the value for the fixed elements
     *
     * @return string[int]
     */
    public function get_fixed_elements()
    {
        $fixed_elements = array();

        foreach ($this->get_property_providers() as $property_provider)
        {
            $property_provider_object = new $property_provider();

            $properties = \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage\DataManager :: retrieve_content_object_property_rel_metadata_elements_by_content_object_type(
                ClassnameUtilities :: getInstance()->getNamespaceFromClassname($property_provider));

            while ($content_object_property_rel_metadata_element = $properties->next_result())
            {
                $fixed_elements[$content_object_property_rel_metadata_element->get_element_id()] = $property_provider_object->render_property(
                    $content_object_property_rel_metadata_element->get_property());
            }
        }
    }

    /**
     * Returns an array with the property providers for the current content object
     *
     * @return \core\metadata\PropertyProvider[]
     */
    protected function get_property_providers()
    {
        $property_providers = array();

        $property_provider_base_namespace = '\Integration\Chamilo\Core\Repository\ContentObjectMetadataElementLinker\PropertyProvider';

        $property_providers[] = '\Chamilo\Core\Repository' . $property_provider_base_namespace;

        $content_object_type = $this->content_object->get_type();
        $content_object_property_provider = $content_object_type . $property_provider_base_namespace;
        if (class_exists($content_object_property_provider))
        {
            $property_providers[] = $content_object_property_provider;
        }

        return $property_providers;
    }
}