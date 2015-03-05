<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Metadata;

use Chamilo\Core\Group\Integration\Chamilo\Core\Metadata\Storage\DataClass\MetadataAttributeValue;
use Chamilo\Core\Group\Integration\Chamilo\Core\Metadata\Storage\DataClass\MetadataElementValue;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Metadata\Value\Storage\DataClass\AttributeValue;
use Chamilo\Core\Metadata\Value\Storage\DataClass\ElementValue;
use Chamilo\Core\Metadata\Value\ValueCreator;

/**
 * Implementation of the ValueCreator interface to store metadata values for groups
 * 
 * @package group\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupMetadataValueCreator implements ValueCreator
{

    /**
     * The group to create the metadata to
     * 
     * @var \core\group\Group
     */
    private $group;

    /**
     * Constructor
     * 
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Returns a new instance of the context implementation of the ElementValue object
     * 
     * @return ElementValue
     */
    public function create_element_value_object()
    {
        $group_metadata_element_value = new MetadataElementValue();
        $group_metadata_element_value->set_group_id($this->group->get_id());
        
        return $group_metadata_element_value;
    }

    /**
     * Returns a new instance of the context implementation of the AttributeValue object
     * 
     * @return AttributeValue
     */
    public function create_attribute_value_object()
    {
        $group_metadata_attribute_value = new MetadataAttributeValue();
        $group_metadata_attribute_value->set_group_id($this->group->get_id());
        
        return $group_metadata_attribute_value;
    }
}