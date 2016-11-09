<?php
namespace Chamilo\Core\Rights;

use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 * New version of rights utilities to work with entities and the application specific location tables
 *
 * @author Pieterjan Broekaert
 */
class RightsUtil
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    // Types
    const TREE_TYPE_ROOT = 0;
    const TYPE_ROOT = 0;

    private static $instance;

    private $rights_cache;

    private $rights_cache_specific_entity;

    private $location_cache;

    private $entities_condition_cache;

    private $locked_parent_cache;

    private $entity_item_condition_cache;

    /*
     * Use get_instance to make use of caching
     */
    public static function getInstance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    public function create_location($context, $type = self :: TYPE_ROOT, $identifier = 0, $inherit = 0, $parent = 0, $locked = 0,
        $tree_identifier = 0, $tree_type = self :: TREE_TYPE_ROOT, $return_location = false, $create_in_batch = false)
    {
        $rights_location_class = $context . '\Storage\DataClass\RightsLocation';
        if (! class_exists($rights_location_class))
        {
            $rights_location_class = self :: context() . '\Storage\DataClass\RightsLocation';
        }

        $location = new $rights_location_class();
        $location->set_parent_id($parent);
        $location->set_context($context);
        $location->set_type($type);
        $location->set_identifier($identifier);
        $location->set_inherit($inherit);
        $location->set_locked($locked);
        $location->set_tree_identifier($tree_identifier);
        $location->set_tree_type($tree_type);

        $succes = $location->create(null, $create_in_batch);

        if ($return_location && $succes)
        {
            return $location;
        }
        else
        {
            return $succes;
        }
    }

    public function is_allowed_on_location($right, $context, $user_id, $entities, $location)
    {
        $rights_array = DataManager :: retrieve_granted_rights_array(
            $location,
            $this->get_entities_condition($context, $user_id, $entities));
        return in_array($right, $rights_array);
    }

    public function is_allowed($right, $context, $user_id, $entities, $identifier = 0, $type = self :: TYPE_ROOT, $tree_identifier = 0,
        $tree_type = self :: TREE_TYPE_ROOT)
    {

        // //todo: make inherit optional check
        $user_id = $user_id ? $user_id : Session :: get_user_id();

        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            (int) $user_id);

        if ($user->is_platform_admin())
        {
            return true;
        }

        $location = $this->get_location_by_identifier($context, $type, $identifier, $tree_identifier, $tree_type);

        if (! $location)
        {
            // todo: refactor to translation
            throw new Exception(
                Translation :: get('NoLocationFound') . $context . ';type=' . $type . ';identifier=' . $identifier .
                     ';tree_id=' . $tree_identifier . ';tree_type=' . $tree_type);
        }

        // if ($this->get_locked_parent($location))
        // {
        // $location = $this->get_locked_parent($location);
        // }

        return $this->is_allowed_on_location($right, $context, $user_id, $entities, $location);
    }

    public function get_locked_parent($location)
    {
        if ($this->locked_parent_cache[$location->get_id()] == - 1)
        {
            return false;
        }
        else
        {
            if (! isset($this->locked_parent_cache[$location->get_id()]))
            {
                $locked_parent = $location->get_locked_parent();
                if (! $locked_parent)
                {
                    $this->locked_parent_cache[$location->get_id()] = - 1;
                    return false;
                }
                else
                {
                    $this->locked_parent_cache[$location->get_id()] = $locked_parent;
                }
            }
        }

        return $this->locked_parent_cache[$location->get_id()];
    }

    private function get_entities_condition($context, $user_id, $entities, $to_string = false)
    {
        if (! empty($entities))
        {
            $entitiesHash = md5(serialize($entities));

            if (is_null($this->entities_condition_cache[$user_id][$entitiesHash][(int) $to_string]))
            {
                $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');

                $or_conditions = array();

                foreach ($entities as $entity)
                {
                    $and_conditions = array();

                    $and_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            $context_entity_right,
                            $context_entity_right :: PROPERTY_ENTITY_TYPE),
                        new StaticConditionVariable($entity->get_entity_type()));
                    $and_conditions[] = new InCondition(
                        new PropertyConditionVariable($context_entity_right, $context_entity_right :: PROPERTY_ENTITY_ID),
                        $entity->retrieve_entity_item_ids_linked_to_user($user_id));

                    $or_conditions[] = new AndCondition($and_conditions);
                }

                // add everyone 'entity'

                $and_conditions = array();

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable($context_entity_right, $context_entity_right :: PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(0));

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable($context_entity_right, $context_entity_right :: PROPERTY_ENTITY_ID),
                    new StaticConditionVariable(0));

                $or_conditions[] = new AndCondition($and_conditions);

                $condition = new OrCondition($or_conditions);

                // if ($to_string)
                // {
                // $condition = DataManager :: translateCondition($condition);
                // }

                $this->entities_condition_cache[$user_id][$entitiesHash][(int) $to_string] = $condition;
            }

            return $this->entities_condition_cache[$user_id][$entitiesHash][(int) $to_string];
        }
    }

    private $location_ids_cache;

    /**
     * Retrieves the identifiers where you have a certain right granted inside a location
     *
     * @param <type> $right_id
     * @param <type> $context
     * @param <type> $parent_location
     * @param <type> $retrieve_type
     * @param <type> $user_id
     * @param <type> $entities
     * @return <type>
     *
     */
    public function get_identifiers_with_right_granted($right_id, $context, $parent_location, $retrieve_type, $user_id,
        $entities)
    {
        $user_id = $user_id ? $user_id : Session :: get_user_id();

        $context_location = ($context . '\Storage\DataClass\RightsLocation');

        if (is_null($this->location_ids_cache[$user_id][$right_id][$parent_location->get_id()]))
        {
            // if no parent location => items beneath root
            $parent_has_right = $this->is_allowed_on_location(
                $right_id,
                $context,
                $user_id,
                $entities,
                $parent_location);

            $entities_condition = $this->get_entities_condition($context, $user_id, $entities);

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location :: PROPERTY_PARENT_ID),
                new StaticConditionVariable($parent_location->get_id()));

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location :: PROPERTY_TYPE),
                new StaticConditionVariable($retrieve_type));

            $condition = new AndCondition($conditions);

            $this->location_ids_cache[$user_id][$right_id][$parent_location->get_id()] = DataManager :: retrieve_identifiers_with_right_granted(
                $right_id,
                $context,
                $entities_condition,
                $condition,
                $parent_has_right);
        }
        return $this->location_ids_cache[$user_id][$right_id][$parent_location->get_id()];
    }

    /**
     * Retrieves the locations where a specific right entity is linked to Returns an array with for every retrieve type
     * the identifiers
     */
    public function get_location_overview_with_rights_granted($context, $user_id, $entities, $right_ids = array(),
        $retrieve_types = array(), $tree_type = null, $tree_identifier = null)
    {
        $user_id = $user_id ? $user_id : Session :: get_user_id();
        $entities_condition = $this->get_entities_condition($context, $user_id, $entities);

        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_location_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        foreach ($right_ids as $right_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location_entity_right :: class_name(),
                    $context_location_entity_right :: PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id));
        }
        foreach ($retrieve_types as $retrieve_type)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_TYPE),
                new StaticConditionVariable($retrieve_type));
        }
        if (! is_null($tree_type))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_TREE_TYPE),
                new StaticConditionVariable($tree_type));
        }
        if (! is_null($tree_identifier))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location :: class_name(),
                    $context_location :: PROPERTY_TREE_IDENTIFIER),
                new StaticConditionVariable($tree_identifier));
        }
        if (! is_null($conditions))
        {
            $condition = new AndCondition($conditions);
        }

        $locations = DataManager :: retrieve_location_overview_with_rights_granted(
            $context,
            $condition,
            $entities_condition);

        $overview = array();
        while ($location = $locations->next_result())
        {
            $overview[$location[$context_location :: PROPERTY_TYPE]][] = $location[$context_location :: PROPERTY_IDENTIFIER];
        }

        return $overview;
    }

    public function count_location_overview_with_rights_granted($context, $user_id, $entities, $right_ids = array(),
        $retrieve_types = array(), $tree_type = null, $tree_identifier = null)
    {
        $user_id = $user_id ? $user_id : Session :: get_user_id();
        $entities_condition = $this->get_entities_condition($context, $user_id, $entities);

        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_location_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        foreach ($right_ids as $right_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location_entity_right :: class_name(),
                    $context_location_entity_right :: PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id));
        }
        foreach ($retrieve_types as $retrieve_type)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_TYPE),
                new StaticConditionVariable($retrieve_type));
        }
        if (! is_null($tree_type))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_TREE_TYPE),
                new StaticConditionVariable($tree_type));
        }
        if (! is_null($tree_identifier))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location :: class_name(),
                    $context_location :: PROPERTY_TREE_IDENTIFIER),
                new StaticConditionVariable($tree_identifier));
        }
        if (! is_null($conditions))
        {
            $condition = new AndCondition($conditions);
        }

        return DataManager :: count_location_overview_with_rights_granted($context, $condition, $entities_condition);
    }

    // PERFORMANCE-TWEAKS-START

    /**
     * Filters given identifiers and returns those which the given user has access rights to.
     * Why this function?: This function is an accelerated version of is_allowed(...) when called many times after each
     * other. The number
     * of database queries is minimized by processing identifiers all at once.
     * Steps:
     * -# Retrieve all locations belonging to any of the identifiers.
     * -# Retrieve all parent location recursively of all locations found in step 1. Store them in a simple array
     * mapping child onto parent location ID's.
     * -# Concatenate all locations ID's of step 1 and all parent ID's of step 2 into an array.
     * -# Remove those location ID's which user has not access right to.
     * -# Loop over all locations retrieved in step 1: recursively visit all parent locations using the array created in
     * step 2, and check
     * is user has access to any of them. If yes, add the corresponding identifier to the result array.
     * -# Return collected identifiers.
     *
     * @return array of identifiers.
     */
    public function filter_location_identifiers_by_granted_right($context, $user, $entities, $right, $identifiers, $type)
    {
        if ($user->is_platform_admin())
        {
            return $identifiers;
        }

        $location_ids = DataManager :: retrieve_location_ids_by_identifiers($context, $identifiers, $type);
        $location_parent_ids = $this->get_location_parent_ids_recursive($context, $location_ids);

        $all_location_ids = array_merge(array_values($location_ids), array_values($location_parent_ids));
        $entities_condition = $this->get_entities_condition($context, $user->get_id(), $entities);
        $all_location_ids_with_granted_right = DataManager :: filter_location_identifiers_by_granted_right(
            $context,
            $right,
            $entities_condition,
            $all_location_ids);

        $identifiers_with_granted_right = array();

        foreach ($identifiers as $identifier)
        {
            if ($this->has_right_recursive(
                $location_ids[$identifier],
                $location_parent_ids,
                $all_location_ids_with_granted_right))
            {
                $identifiers_with_granted_right[] = $identifier;
            }
        }

        return $identifiers_with_granted_right;
    }

    /**
     * Returns whether given location or any of its ancestors is in array $location_ids_with_granted_right.
     *
     * @param int $location_id location we check whether user has access rigth to.
     * @param array $location_parent_ids mapping of child location ID's onto parent location ID's. @see
     *            get_location_parent_ids_recursive(...)
     * @param array $location_ids_with_granted_right All location ID's which user has access rigth to. Keys: location
     *            ID's Values: True.
     * @see DataManager :: filter_location_identifiers_by_granted_right.
     * @return boolean
     */
    private function has_right_recursive($location_id, $location_parent_ids, $location_ids_with_granted_right)
    {
        if (isset($location_ids_with_granted_right[$location_id]))
        {
            return true;
        }

        if (! isset($location_parent_ids[$location_id]))
        {
            return false;
        }

        return $this->has_right_recursive(
            $location_parent_ids[$location_id],
            $location_parent_ids,
            $location_ids_with_granted_right);
    }

    /**
     * Returns an array mapping child location ID's onto parent location ID's.
     * Idea: Retrieve the child-parent relation of location with as few queries as possible and store them in the
     * memory. The function
     * has_right_recursive(...) will loop over the child-parent tree, which is much faster than the recursive function
     * calls to DataManager
     * :: retrieve_granted_rights_array(...). This function actually retrieves the location tree level-by-level starting
     * with the leaf
     * level, followed by parent level, then grandparents until an empty level is found.
     * Result is a flat array mapping each ID in $location_ids onto its parent ID and each parent onto its grand parent
     * ID, etc.
     * Result will only contain child location ID's if the 'inherit' property of the location is true and the parent is
     * not null.
     *
     * @return array Keys: child location ID's Values: parent location ID's.
     */
    public function get_location_parent_ids_recursive($context, $location_ids)
    {
        $all_location_parent_ids = array();

        $location_parent_ids = $location_ids;

        $context_location = ($context . '\Storage\DataClass\RightsLocation');

        while (true)
        {
            $conditions = array();
            $conditions[] = new InCondition(
                new PropertyConditionVariable($context_location, $context_location :: PROPERTY_ID),
                array_unique($location_parent_ids));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location :: PROPERTY_INHERIT),
                new StaticConditionVariable(1));
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        $context_location,
                        $context_location :: PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)));
            $condition = new AndCondition($conditions);

            $location_parent_ids = DataManager :: retrieve_location_parent_ids($context, $condition);

            if (count($location_parent_ids) == 0)
            {
                break;
            }

            $all_location_parent_ids = $all_location_parent_ids + $location_parent_ids;
        }

        return $all_location_parent_ids;
    }
    // PERFORMANCE-TWEAKS-END

    /**
     *
     * @param integer $right_id
     * @param string $context
     * @param integer $identifier
     * @param integer $type
     * @param integer $tree_identifier
     * @param integer $tree_type
     * @throws Exception
     * @return \Chamilo\Core\Rights\Storage\<array>
     */
    public function get_target_entities($right_id, $context, $identifier = 0, $type = self :: TYPE_ROOT, $tree_identifier = 0,
        $tree_type = self :: TREE_TYPE_ROOT)
    {
        $location = $this->get_location_by_identifier($context, $type, $identifier, $tree_identifier, $tree_type);

        if (! $location)
            Throw new Exception(
                Translation :: get('NoLocationFound') . $context . ';type=' . $type . ';identifier=' . $identifier .
                     ';tree_id=' . $tree_identifier . ';tree_type=' . $tree_type);

        return $this->get_target_entities_for_location($right_id, $context, $location);
    }

    public function get_target_entities_for_location($right_id, $context, $location)
    {
        return DataManager :: retrieve_target_entities_array($right_id, $context, $location);
    }

    public function get_rights_for_location_and_entity($context, $location_id, $entity_id, $entity_type)
    {
        $conditions = array();

        $class_name = $context . '\Storage\DataClass\\' . RightsLocationEntityRight :: class_name(false);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity_type));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entity_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id));

        $condition = new AndCondition($conditions);

        $right_ids = array();

        $location_entity_rights = DataManager :: retrieve_rights_location_rights($context, $condition);
        while ($location_entity_right = $location_entity_rights->next_result())
        {
            $right_ids[] = $location_entity_right->get_right_id();
        }

        return $right_ids;
    }

    public function get_location_by_identifier($context, $type, $identifier, $tree_identifier = '0',
        $tree_type = self :: TREE_TYPE_ROOT)
    {
        return DataManager :: retrieve_rights_location_by_identifier(
            $context,
            $type,
            $identifier,
            $tree_identifier,
            $tree_type);
    }

    public function get_location_id_by_identifier($context, $type, $identifier, $tree_identifier = '0',
        $tree_type = self :: TREE_TYPE_ROOT)
    {
        $location = $this->get_location_by_identifier($context, $type, $identifier, $tree_identifier, $tree_type);
        if ($location)
        {
            return $location->get_id();
        }
        else
        {
            return 0;
        }
    }

    public function create_subtree_root_location($context, $tree_identifier, $tree_type, $return_location = false)
    {
        return $this->create_location(
            $context,
            self :: TYPE_ROOT,
            0,
            0,
            0,
            0,
            $tree_identifier,
            $tree_type,
            $return_location);
    }

    public function get_root_id($context, $tree_type = self :: TREE_TYPE_ROOT, $tree_identifier = 0)
    {
        $root = $this->get_root($context, $tree_type, $tree_identifier);
        if ($root)
        {
            return $root->get_id();
        }
        else
        {
            return false;
        }
    }

    public function get_root($context, $tree_type = self :: TREE_TYPE_ROOT, $tree_identifier = 0)
    {
        $class = $context . '\Storage\DataClass\RightsLocation';

        $root_conditions = array();
        $root_conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class, RightsLocation :: PROPERTY_PARENT_ID),
            new StaticConditionVariable(0));

        $root_conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class, RightsLocation :: PROPERTY_TREE_TYPE),
            new StaticConditionVariable($tree_type));

        $root_conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class, RightsLocation :: PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($tree_identifier));

        $root_condition = new AndCondition($root_conditions);

        $root = DataManager :: retrieve_rights_location($context, $root_condition);

        if ($root)
        {
            return $root;
        }
        else
        {
            return false;
        }
    }

    public function invert_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        if (! is_null($entity_id) && ! is_null($entity_type) && ! empty($right) && ! empty($location_id))
        {
            $location_entity_right = DataManager :: retrieve_rights_location_entity_right(
                $context,
                $right,
                $entity_id,
                $entity_type,
                $location_id);

            if ($location_entity_right)
            {
                return $location_entity_right->delete();
            }
            else
            {
                $class = $context . '\Storage\DataClass\RightsLocationEntityRight';
                DataClassCache :: truncate($class);
                return $this->create_rights_location_entity_right(
                    $context,
                    $right,
                    $entity_id,
                    $entity_type,
                    $location_id);
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Enables a right for a specific entity on a specific location
     *
     * @param String $context
     * @param int $right
     * @param int $entity_id
     * @param int $entity_type
     * @param int $location_id
     * @return boolean
     */
    public function set_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        if (! is_null($entity_id) && ! is_null($entity_type) && ! empty($right) && ! empty($location_id))
        {
            $location_entity_right = DataManager :: retrieve_rights_location_entity_right(
                $context,
                $right,
                $entity_id,
                $entity_type,
                $location_id);

            if ($location_entity_right)
            {
                return true;
            }
            else
            {
                return $this->create_rights_location_entity_right(
                    $context,
                    $right,
                    $entity_id,
                    $entity_type,
                    $location_id);
            }
        }
        else
        {
            return false;
        }
    }

    public function unset_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        if (! is_null($entity_id) && ! is_null($entity_type) && ! empty($right) && ! empty($location_id))
        {
            $location_entity_right = DataManager :: retrieve_rights_location_entity_right(
                $context,
                $right,
                $entity_id,
                $entity_type,
                $location_id);

            if ($location_entity_right)
            {
                return $location_entity_right->delete();
            }
            else
            {
                return true;
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
    private function create_rights_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        $location_entity_right = new $context_class();
        $location_entity_right->set_context($context);
        $location_entity_right->set_location_id($location_id);
        $location_entity_right->set_right_id($right);
        $location_entity_right->set_entity_id($entity_id);
        $location_entity_right->set_entity_type($entity_type);

        return $location_entity_right->create();
    }

    public function get_rights_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        $location_entity_right = new $context_class();
        $location_entity_right->set_context($context);
        $location_entity_right->set_location_id($location_id);
        $location_entity_right->set_right_id($right);
        $location_entity_right->set_entity_id($entity_id);
        $location_entity_right->set_entity_type($entity_type);
        $location_entity_right->create();
        return $location_entity_right;
    }

    /**
     * Helper function to delete all the location entity right records for a given entity on a given location
     *
     * @param RightsLocation $location
     * @param int $entity_id
     * @param int $entity_type
     */
    public function delete_location_entity_right_for_entity($location, $entity_id, $entity_type)
    {
        return DataManager :: delete_rights_location_entity_rights($location, $entity_type, $entity_id);
    }

    /*
     * check the right for a specific entity type and id Caches other rights for future reference
     */
    public function is_allowed_for_rights_entity_item($context, $entity_type, $entity_id, $right, $location)
    {
        $granted_rights = $this->get_granted_rights_for_rights_entity_item(
            $context,
            $entity_type,
            $entity_id,
            $location);

        if (in_array($right, $granted_rights))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_granted_rights_for_rights_entity_item($context, $entity_type, $entity_id, $location)
    {
        if (! is_null($this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type]))
        {
            $rights_array = $this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type];
        }
        else
        {
            $rights_array = DataManager :: retrieve_granted_rights_array(
                $location,
                $this->get_entity_item_condition($context, $entity_type, $entity_id, $location->get_id()));
            $this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type] = $rights_array;
        }
        return $this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type];
    }

    public function get_entity_item_condition($context, $type, $id, $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        if (is_null($this->entity_item_condition_cache[$context][$location_id][$id][$type]))
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($type));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_ENTITY_ID),
                new StaticConditionVariable($id));
            $condition = new AndCondition($conditions);
            $this->entity_item_condition_cache[$context][$location_id][$id][$type] = $condition;
        }

        return $this->entity_item_condition_cache[$context][$location_id][$id][$type];
    }

    /*
     * todo: add caching
     */
    public function is_allowed_for_rights_entity_item_no_inherit($context, $entity_type, $entity_id, $right_id,
        $location_id)
    {
        $conditions = array();

        $class_name = $context . '\Storage\DataClass\\' . RightsLocationEntityRight :: class_name(false);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity_type));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entity_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_RIGHT_ID),
            new StaticConditionVariable($right_id));

        $condition = new AndCondition($conditions);

        if (DataManager :: retrieve_rights_location_rights($context, $condition)->size() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
