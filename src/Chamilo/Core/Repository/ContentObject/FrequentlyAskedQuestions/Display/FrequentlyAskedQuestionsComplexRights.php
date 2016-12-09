<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;

/**
 * Interface that indicates an implementer supports rights configuration on individual portfolio items and provides the
 * mehtods that should be implemented to achieve that support
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface FrequentlyAskedQuestionsComplexRights
{

    /**
     * Get the RightsLocations corresponding with the given ComplexContentObjectPathNodes
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode $nodes
     * @return \rights\RightsLocation[]
     */
    public function get_locations($nodes);

    /**
     * Get the different rights which are supported by the implementer
     *
     * @return int[string]
     */
    public function get_available_rights();

    /**
     * Get the different types a rights-entities which are supported by the implementer
     *
     * @return \rights\RightsEntity[]
     */
    public function get_entities();

    /**
     * Get the selected entities for a given ComplexContentObjectPathNode
     *
     * @param \core\repository\common\path\ComplexContentObjectPathNode $node
     * @return \libraries\storage\ResultSet
     */
    public function get_selected_entities(ComplexContentObjectPathNode $node);

    /**
     * Invert the set rights value for a given right, entity id, entity type and location id
     *
     * @param int $right_id
     * @param int $entity_id
     * @param int $entity_type
     * @param string $location_id
     * @return boolean
     */
    public function invert_location_entity_right($right_id, $entity_id, $entity_type, $location_id);

    /**
     * Determine whether or not the use is allowed to configure portfolio item rights
     */
    public function is_allowed_to_set_content_object_rights();

    /**
     * Retrieve a set of users which can be emulated in the implementing context
     *
     * @param \libraries\storage\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \libraries\ObjectTableOrder[] $order_property
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_frequently_asked_questions_possible_view_users($condition, $count, $offset, $order_property);

    /**
     * Count the set of users which can be emulated in the implementing context
     *
     * @param \libraries\storage\Condition $condition
     */
    public function count_frequently_asked_questions_possible_view_users($condition);

    /**
     * Set the virtual user id
     *
     * @param int $virtual_user_id
     * @return boolean
     */
    public function set_frequently_asked_questions_virtual_user_id($virtual_user_id);

    /**
     * Returns the virtual user (if any)
     *
     * @return \core\user\User
     */
    public function get_frequently_asked_questions_virtual_user();

    /**
     * Clear the virtual user from storage
     */
    public function clear_virtual_user_id();
}
