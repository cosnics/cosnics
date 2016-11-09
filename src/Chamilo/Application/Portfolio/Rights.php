<?php
namespace Chamilo\Application\Portfolio;

use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 * Portfolio rights
 *
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Rights
{
    const VIEW_RIGHT = 1;
    const VIEW_FEEDBACK_RIGHT = 2;
    const GIVE_FEEDBACK_RIGHT = 3;
    const EDIT_RIGHT = 4;

    /**
     *
     * @var Rights
     */
    private static $instance;

    /**
     *
     * @var boolean[]
     */
    private $entities_condition_cache;

    /**
     *
     * @var int[]
     */
    private $granted_rights_cache;

    /**
     *
     * @return Rights
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @return string[]
     */
    public static function get_available_rights()
    {
        return array(
            Translation::get('ViewRight') => self::VIEW_RIGHT,
            Translation::get('ViewFeedbackRight') => self::VIEW_FEEDBACK_RIGHT,
            Translation::get('GiveFeedbackRight') => self::GIVE_FEEDBACK_RIGHT,
            Translation::get('EditRight') => self::EDIT_RIGHT);
    }

    /**
     *
     * @param int $right
     * @param int $entity_id
     * @param int $entity_type
     * @param string $location_id
     * @param int $publication_id
     * @return boolean
     */
    public function invert_location_entity_right($right, $entity_id, $entity_type, $location_id, $publication_id)
    {
        if (! is_null($entity_id) && ! is_null($entity_type) && ! empty($right) && ! empty($location_id) &&
             ! empty($publication_id))
        {
            $location_entity_right = DataManager::retrieve_rights_location_entity_right(
                $right,
                $entity_id,
                $entity_type,
                $location_id,
                $publication_id);

            if ($location_entity_right)
            {
                return $location_entity_right->delete();
            }
            else
            {
                DataClassCache::truncate(RightsLocationEntityRight::class_name());
                return $this->create_rights_location_entity_right(
                    $right,
                    $entity_id,
                    $entity_type,
                    $location_id,
                    $publication_id);
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Helper function to create a rights location entity right
     *
     * @param String $context
     * @param int $right
     * @param int $entity_id
     * @param int $entity_type
     * @param int $location_id
     * @return boolean
     */
    private function create_rights_location_entity_right($right, $entity_id, $entity_type, $location_id, $publication_id)
    {
        $location_entity_right = new RightsLocationEntityRight();
        $location_entity_right->set_location_id($location_id);
        $location_entity_right->set_publication_id($publication_id);
        $location_entity_right->set_right_id($right);
        $location_entity_right->set_entity_id($entity_id);
        $location_entity_right->set_entity_type($entity_type);

        return $location_entity_right->create();
    }

    /**
     *
     * @param int $right
     * @param RightsLocation $location
     * @param int $user_id
     * @return boolean
     */
    public function is_allowed($right, $location, $user_id)
    {
        $user_id = $user_id ? $user_id : Session::get_user_id();

        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            (int) $user_id);

        if ($user->is_platform_admin())
        {
            return true;
        }

        if (! $location instanceof RightsLocation)
        {
            return false;
        }

        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();

        return $this->is_allowed_on_location($right, $user_id, $entities, $location);
    }

    /**
     *
     * @param int $right
     * @param int $user_id
     * @param \rights\RightsEntity[] $entities
     * @param RightsLocation $location
     * @return boolean
     */
    public function is_allowed_on_location($right, $user_id, $entities, $location)
    {
        $rights_array = $this->retrieve_granted_rights_array(
            $location,
            $this->get_entities_condition($user_id, $entities));

        return in_array($right, $rights_array);
    }

    /**
     *
     * @param int $user_id
     * @param \rights\RightsEntity[] $entities
     * @param boolean $to_string
     * @return \libraries\storage\Condition
     */
    private function get_entities_condition($user_id, $entities)
    {
        if (! empty($entities))
        {
            $entitiesHash = md5(serialize($entities));

            if (is_null($this->entities_condition_cache[$user_id][$entitiesHash]))
            {
                $or_conditions = array();
                foreach ($entities as $entity)
                {
                    $entity_type_condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRight::class_name(),
                            RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($entity->get_entity_type()));

                    foreach ($entity->retrieve_entity_item_ids_linked_to_user($user_id) as $entity_item_id)
                    {
                        $and_conditions = array();
                        $and_conditions[] = $entity_type_condition;

                        $and_conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                RightsLocationEntityRight::class_name(),
                                RightsLocationEntityRight::PROPERTY_ENTITY_ID),
                            new StaticConditionVariable($entity_item_id));

                        $or_conditions[] = new AndCondition($and_conditions);
                    }
                }

                // add everyone 'entity'

                $and_conditions = array();

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class_name(),
                        RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(0));

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class_name(),
                        RightsLocationEntityRight::PROPERTY_ENTITY_ID),
                    new StaticConditionVariable(0));

                $or_conditions[] = new AndCondition($and_conditions);

                $condition = new OrCondition($or_conditions);

                $this->entities_condition_cache[$user_id][$entitiesHash] = $condition;
            }

            return $this->entities_condition_cache[$user_id][$entitiesHash];
        }
    }

    /**
     * Retrieves the granted rights for a location
     *
     * @param RightsLocation $location
     * @param \libraries\storage\Condition $entities_condition
     * @return int[]
     */
    public function retrieve_granted_rights_array($location, $entities_condition)
    {
        $hash = (is_object($entities_condition) ? spl_object_hash($entities_condition) : md5($entities_condition));

        if (is_null($this->granted_rights_cache[$location->get_node_id()][$hash]))
        {
            $conditions = array();
            $conditions[] = $entities_condition;
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(),
                    RightsLocationEntityRight::PROPERTY_LOCATION_ID),
                new StaticConditionVariable($location->get_node_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(),
                    RightsLocationEntityRight::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($location->get_publication_id()));
            $condition = new AndCondition($conditions);

            $records = DataManager::records(
                RightsLocationEntityRight::class_name(),
                new RecordRetrievesParameters(
                    new DataClassProperties(
                        array(
                            new PropertyConditionVariable(
                                RightsLocationEntityRight::class_name(),
                                RightsLocationEntityRight::PROPERTY_RIGHT_ID))),
                    $condition));

            $granted_rights = array();

            while ($record = $records->next_result())
            {
                $granted_rights[] = $record[RightsLocationEntityRight::PROPERTY_RIGHT_ID];
            }

            if ($location->inherits())
            {

                $parent_location = $this->get_location(
                    $location->get_node()->get_parent(),
                    $location->get_publication_id());

                if ($parent_location)
                {
                    $parent_rights = $this->retrieve_granted_rights_array($parent_location, $entities_condition);
                    $granted_rights = array_merge($granted_rights, $parent_rights);
                }
            }

            $this->granted_rights_cache[$location->get_node_id()][$hash] = $granted_rights;
        }
        return $this->granted_rights_cache[$location->get_node_id()][$hash];
    }

    /**
     *
     * @param ComplexContentObjectPath $node
     * @param int $publication_id
     * @return \application\portfolio\RightsLocation
     */
    public function get_location(ComplexContentObjectPathNode $node = null, $publication_id)
    {
        if (! $node)
        {
            return null;
        }

        $node_id = $node->get_hash();

        $location = new RightsLocation();

        $location->set_node_id($node_id);
        $location->set_publication_id($publication_id);

        // Check inherit

        if ($node->is_root())
        {
            $location->set_inherit(0);
        }

        else
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class_name(), RightsLocation::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($location->get_publication_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class_name(), RightsLocation::PROPERTY_NODE_ID),
                new StaticConditionVariable($location->get_node_id()));
            $condition = new AndCondition($conditions);

            try
            {
                $record = DataManager::record(
                    RightsLocation::class_name(),
                    new RecordRetrieveParameters(
                        new DataClassProperties(
                            array(
                                new PropertyConditionVariable(
                                    RightsLocation::class_name(),
                                    RightsLocation::PROPERTY_INHERIT))),
                        $condition));

                if ($record === false)
                {
                    $location->set_inherit(1);
                }
                else
                {
                    $location->set_inherit($record[RightsLocation::PROPERTY_INHERIT]);
                }
            }
            catch (\Exception $exception)
            {
                $location->set_inherit(1);
            }
        }

        $location->set_node($node);

        if ($node->is_root())
        {
            $location->set_parent_id(null);
        }
        else
        {
            $location->set_parent_id($node->get_parent()->get_hash());
        }

        return $location;
    }
}
