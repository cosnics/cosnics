<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld;

use Chamilo\Core\MetadataOld\Value\Storage\DataClass\AttributeValue;
use Chamilo\Core\MetadataOld\Value\Storage\DataClass\ElementValue;
use Chamilo\Core\MetadataOld\Value\ValueCreator;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Storage\DataClass\ContentObjectMetadataAttributeValue;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Storage\DataClass\ContentObjectMetadataElementValue;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Implementation of the ValueCreator interface to store metadata values for content objects
 * 
 * @package repository\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectMetadataValueCreator implements ValueCreator
{

    /**
     * The content object to create the metadata to
     * 
     * @var \core\repository\ContentObject
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
     * Returns a new instance of the context implementation of the ElementValue object
     * 
     * @return ElementValue
     */
    public function create_element_value_object()
    {
        $content_object_metadata_element_value = new ContentObjectMetadataElementValue();
        $content_object_metadata_element_value->set_content_object_id($this->content_object->get_id());
        
        return $content_object_metadata_element_value;
    }

    /**
     * Returns a new instance of the context implementation of the AttributeValue object
     * 
     * @return AttributeValue
     */
    public function create_attribute_value_object()
    {
        $content_object_metadata_attribute_value = new ContentObjectMetadataAttributeValue();
        $content_object_metadata_attribute_value->set_content_object_id($this->content_object->get_id());
        
        return $content_object_metadata_attribute_value;
    }
}