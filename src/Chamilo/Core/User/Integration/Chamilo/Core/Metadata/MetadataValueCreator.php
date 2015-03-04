<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Metadata;

use Chamilo\Core\Metadata\Value\Storage\DataClass\AttributeValue;
use Chamilo\Core\Metadata\Value\Storage\DataClass\ElementValue;
use Chamilo\Core\Metadata\Value\ValueCreator;
use Chamilo\Core\User\Integration\Chamilo\Core\Metadata\Storage\DataClass\MetadataAttributeValue;
use Chamilo\Core\User\Integration\Chamilo\Core\Metadata\Storage\DataClass\MetadataElementValue;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Implementation of the ValueCreator interface to store metadata values for users
 * 
 * @package user\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataValueCreator implements ValueCreator
{

    /**
     * The group to create the metadata to
     * 
     * @var \core\user\User
     */
    private $user;

    /**
     * Constructor
     * 
     * @param \core\user\storage\data_class\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns a new instance of the context implementation of the ElementValue object
     * 
     * @return ElementValue
     */
    public function create_element_value_object()
    {
        $group_metadata_element_value = new MetadataElementValue();
        $group_metadata_element_value->set_user_id($this->user->get_id());
        
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
        $group_metadata_attribute_value->set_user_id($this->user->get_id());
        
        return $group_metadata_attribute_value;
    }
}