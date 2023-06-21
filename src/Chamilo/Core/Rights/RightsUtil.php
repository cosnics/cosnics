<?php
namespace Chamilo\Core\Rights;

use Chamilo\Core\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * New version of rights utilities to work with entities and the application specific location tables
 *
 * @author     Pieterjan Broekaert
 * @deprecated Use the \Chamilo\Libraries\Rights\Service\RightsService now to extend contextual implementations from now
 */
class RightsUtil
{
    // Types
    public const TREE_TYPE_ROOT = 0;
    public const TYPE_ROOT = 0;

    private static $instance;

    private $entities_condition_cache;

    private $entity_item_condition_cache;

    private $location_cache;

    private $location_ids_cache;

    private $locked_parent_cache;

    private $rights_cache;

    /*
     * Use get_instance to make use of caching
     */

    private $rights_cache_specific_entity;

    public function count_location_overview_with_rights_granted(
        $context, $user_id, $entities, $right_ids = [], $retrieve_types = [], $tree_type = null, $tree_identifier = null
    )
    {
        $user_id = $user_id ?: $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID);
        $entities_condition = $this->get_entities_condition($context, $user_id, $entities);

        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_location_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        foreach ($right_ids as $right_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location_entity_right, $context_location_entity_right::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right_id)
            );
        }
        foreach ($retrieve_types as $retrieve_type)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_TYPE),
                new StaticConditionVariable($retrieve_type)
            );
        }
        if (!is_null($tree_type))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_TREE_TYPE),
                new StaticConditionVariable($tree_type)
            );
        }
        if (!is_null($tree_identifier))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location, $context_location::PROPERTY_TREE_IDENTIFIER
                ), new StaticConditionVariable($tree_identifier)
            );
        }
        if (!is_null($conditions))
        {
            $condition = new AndCondition($conditions);
        }

        return DataManager::count_location_overview_with_rights_granted($context, $condition, $entities_condition);
    }

    public function create_location(
        $context, $type = self::TYPE_ROOT, $identifier = 0, $inherit = 0, $parent = 0, $locked = 0,
        $tree_identifier = 0, $tree_type = self::TREE_TYPE_ROOT, $return_location = false, $create_in_batch = false
    )
    {
        $rights_location_class = $context . '\Storage\DataClass\RightsLocation';
        if (!class_exists($rights_location_class))
        {
            $rights_location_class = static::CONTEXT . '\Storage\DataClass\RightsLocation';
        }

        $location = new $rights_location_class();
        $location->set_parent_id($parent);
        $location->set_context($context);
        $location->setType($type);
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

    /*
     * DONE
     */

    /**
     * Helper function to create a rights location entity right
     *
     * @param String $context
     * @param int $right
     * @param int $entity_id
     * @param int $entity_type
     * @param int $location_id
     *
     * @return bool
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

    /*
     * DONE
     */

    public function create_subtree_root_location($context, $tree_identifier, $tree_type, $return_location = false)
    {
        return $this->create_location(
            $context, self::TYPE_ROOT, 0, 0, 0, 0, $tree_identifier, $tree_type, $return_location
        );
    }

    /*
     * DONE - No longer used
     */

    /**
     * Helper function to delete all the location entity right records for a given entity on a given location
     *
     * @param RightsLocation $location
     * @param int $entity_id
     * @param int $entity_type
     */
    public function delete_location_entity_right_for_entity($location, $entity_id, $entity_type)
    {
        return DataManager::delete_rights_location_entity_rights($location, $entity_type, $entity_id);
    }

    /*
     * DONE
     */

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
    public function filter_location_identifiers_by_granted_right($context, $user, $entities, $right, $identifiers, $type
    )
    {
        if ($user->isPlatformAdmin())
        {
            return $identifiers;
        }

        $location_ids = DataManager::retrieve_location_ids_by_identifiers($context, $identifiers, $type);
        $location_parent_ids = $this->get_location_parent_ids_recursive($context, $location_ids);

        $all_location_ids = array_merge(array_values($location_ids), array_values($location_parent_ids));
        $entities_condition = $this->get_entities_condition($context, $user->get_id(), $entities);
        $all_location_ids_with_granted_right = DataManager::filter_location_identifiers_by_granted_right(
            $context, $right, $entities_condition, $all_location_ids
        );

        $identifiers_with_granted_right = [];

        foreach ($identifiers as $identifier)
        {
            if ($this->has_right_recursive(
                $location_ids[$identifier], $location_parent_ids, $all_location_ids_with_granted_right
            ))
            {
                $identifiers_with_granted_right[] = $identifier;
            }
        }

        return $identifiers_with_granted_right;
    }

    /*
     * DONE
     */

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            DataClassRepositoryCache::class
        );
    }

    /*
     * DONE
     */

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /*
     * DONE
     */

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    /*
     * DONE
     */

    private function get_entities_condition($context, $user_id, $entities, $to_string = false)
    {
        if (!empty($entities))
        {
            $entitiesHash = md5(serialize($entities));

            if (is_null($this->entities_condition_cache[$user_id][$entitiesHash][(int) $to_string]))
            {
                $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');

                $or_conditions = [];

                foreach ($entities as $entity)
                {
                    $and_conditions = [];

                    $and_conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            $context_entity_right, $context_entity_right::PROPERTY_ENTITY_TYPE
                        ), new StaticConditionVariable($entity->get_entity_type())
                    );
                    $and_conditions[] = new InCondition(
                        new PropertyConditionVariable($context_entity_right, $context_entity_right::PROPERTY_ENTITY_ID),
                        $entity->retrieve_entity_item_ids_linked_to_user($user_id)
                    );

                    $or_conditions[] = new AndCondition($and_conditions);
                }

                // add everyone 'entity'

                $and_conditions = [];

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable($context_entity_right, $context_entity_right::PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable(0)
                );

                $and_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable($context_entity_right, $context_entity_right::PROPERTY_ENTITY_ID),
                    new StaticConditionVariable(0)
                );

                $or_conditions[] = new AndCondition($and_conditions);

                $condition = new OrCondition($or_conditions);

                $this->entities_condition_cache[$user_id][$entitiesHash][(int) $to_string] = $condition;
            }

            return $this->entities_condition_cache[$user_id][$entitiesHash][(int) $to_string];
        }
    }

    // PERFORMANCE-TWEAKS-START

    /*
     * DONE
     */

    public function get_entity_item_condition($context, $type, $id, $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        if (is_null($this->entity_item_condition_cache[$context][$location_id][$id][$type]))
        {
            $conditions = [];
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class, $context_class::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($type)
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class, $context_class::PROPERTY_ENTITY_ID),
                new StaticConditionVariable($id)
            );
            $condition = new AndCondition($conditions);
            $this->entity_item_condition_cache[$context][$location_id][$id][$type] = $condition;
        }

        return $this->entity_item_condition_cache[$context][$location_id][$id][$type];
    }

    /*
     * DONE
     */

    public function get_granted_rights_for_rights_entity_item($context, $entity_type, $entity_id, $location)
    {
        if (!is_null($this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type]))
        {
            $rights_array =
                $this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type];
        }
        else
        {
            $rights_array = DataManager::retrieve_granted_rights_array(
                $location, $this->get_entity_item_condition($context, $entity_type, $entity_id, $location->get_id())
            );
            $this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type] =
                $rights_array;
        }

        return $this->rights_cache_specific_entity[$context][$location->get_id()][$entity_id][$entity_type];
    }

    /*
     * DONE
     */

    public function get_identifiers_with_right_granted(
        $right_id, $context, $parent_location, $retrieve_type, $user_id, $entities
    )
    {
        $user_id = $user_id ?: $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID);

        $context_location = ($context . '\Storage\DataClass\RightsLocation');

        if (is_null($this->location_ids_cache[$user_id][$right_id][$parent_location->get_id()]))
        {
            // if no parent location => items beneath root
            $parent_has_right = $this->is_allowed_on_location(
                $right_id, $context, $user_id, $entities, $parent_location
            );

            $entities_condition = $this->get_entities_condition($context, $user_id, $entities);

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_PARENT_ID),
                new StaticConditionVariable($parent_location->get_id())
            );

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_TYPE),
                new StaticConditionVariable($retrieve_type)
            );

            $condition = new AndCondition($conditions);

            $this->location_ids_cache[$user_id][$right_id][$parent_location->get_id()] =
                DataManager::retrieve_identifiers_with_right_granted(
                    $right_id, $context, $entities_condition, $condition, $parent_has_right
                );
        }

        return $this->location_ids_cache[$user_id][$right_id][$parent_location->get_id()];
    }
    // PERFORMANCE-TWEAKS-END

    /*
     * DONE
     */

    public function get_location_by_identifier(
        $context, $type, $identifier, $tree_identifier = '0', $tree_type = self::TREE_TYPE_ROOT
    )
    {
        return DataManager::retrieve_rights_location_by_identifier(
            $context, $type, $identifier, $tree_identifier, $tree_type
        );
    }

    /*
     * DONE
     */

    public function get_location_id_by_identifier(
        $context, $type, $identifier, $tree_identifier = '0', $tree_type = self::TREE_TYPE_ROOT
    )
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

    /*
     * DONE - No longer used?
     */

    public function get_location_overview_with_rights_granted(
        $context, $user_id, $entities, $right_ids = [], $retrieve_types = [], $tree_type = null, $tree_identifier = null
    )
    {
        $user_id = $user_id ?: $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID);
        $entities_condition = $this->get_entities_condition($context, $user_id, $entities);

        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_location_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        foreach ($right_ids as $right_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location_entity_right, $context_location_entity_right::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right_id)
            );
        }
        foreach ($retrieve_types as $retrieve_type)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_TYPE),
                new StaticConditionVariable($retrieve_type)
            );
        }
        if (!is_null($tree_type))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_TREE_TYPE),
                new StaticConditionVariable($tree_type)
            );
        }
        if (!is_null($tree_identifier))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_location, $context_location::PROPERTY_TREE_IDENTIFIER
                ), new StaticConditionVariable($tree_identifier)
            );
        }
        if (!is_null($conditions))
        {
            $condition = new AndCondition($conditions);
        }

        $locations = DataManager::retrieve_location_overview_with_rights_granted(
            $context, $condition, $entities_condition
        );

        $overview = [];
        foreach ($locations as $location)
        {
            $overview[$location[$context_location::PROPERTY_TYPE]][] =
                $location[$context_location::PROPERTY_IDENTIFIER];
        }

        return $overview;
    }

    /*
     * DONE
     */

    /**
     * Returns an array mapping child location ID's onto parent location ID's.
     * Idea: Retrieve the child-parent relation of location with as few queries as possible and store them in the
     * memory. The function
     * has_right_recursive(...) will loop over the child-parent tree, which is much faster than the recursive function
     * calls to DataManager::retrieve_granted_rights_array(...). This function actually retrieves the location tree
     * level-by-level starting with the leaf level, followed by parent level, then grandparents until an empty level is
     * found. Result is a flat array mapping each ID in $location_ids onto its parent ID and each parent onto its grand
     * parent ID, etc. Result will only contain child location ID's if the 'inherit' property of the location is true
     * and the parent is not null.
     *
     * @return array Keys: child location ID's Values: parent location ID's.
     */
    public function get_location_parent_ids_recursive($context, $location_ids)
    {
        $all_location_parent_ids = [];

        $location_parent_ids = $location_ids;

        $context_location = ($context . '\Storage\DataClass\RightsLocation');

        while (true)
        {
            $conditions = [];
            $conditions[] = new InCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_ID),
                array_unique($location_parent_ids)
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location, $context_location::PROPERTY_INHERIT),
                new StaticConditionVariable(1)
            );
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable($context_location, $context_location::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            );
            $condition = new AndCondition($conditions);

            $location_parent_ids = DataManager::retrieve_location_parent_ids($context, $condition);

            if (count($location_parent_ids) == 0)
            {
                break;
            }

            $all_location_parent_ids = $all_location_parent_ids + $location_parent_ids;
        }

        return $all_location_parent_ids;
    }

    /*
     * DONE
     */

    public function get_locked_parent($location)
    {
        if ($this->locked_parent_cache[$location->get_id()] == - 1)
        {
            return false;
        }
        else
        {
            if (!isset($this->locked_parent_cache[$location->get_id()]))
            {
                $locked_parent = $location->get_locked_parent();
                if (!$locked_parent)
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

    /*
     * DONE
     */

    public function get_rights_for_location_and_entity($context, $location_id, $entity_id, $entity_type)
    {
        $conditions = [];

        $class_name = $context . '\Storage\DataClass\RightsLocationEntityRight';

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity_type)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entity_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id)
        );

        $condition = new AndCondition($conditions);

        $right_ids = [];

        $location_entity_rights = DataManager::retrieve_rights_location_rights($context, $condition);
        foreach ($location_entity_rights as $location_entity_right)
        {
            $right_ids[] = $location_entity_right->get_right_id();
        }

        return $right_ids;
    }

    /*
     * DONE
     */

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

    /*
     * DONE
     */

    public function get_root($context, $tree_type = self::TREE_TYPE_ROOT, $tree_identifier = 0)
    {
        $class = $context . '\Storage\DataClass\RightsLocation';

        $root_conditions = [];
        $root_conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class, RightsLocation::PROPERTY_PARENT_ID), new StaticConditionVariable(0)
        );

        $root_conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class, RightsLocation::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($tree_type)
        );

        $root_conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class, RightsLocation::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($tree_identifier)
        );

        $root_condition = new AndCondition($root_conditions);

        $root = DataManager::retrieve_rights_location($context, $root_condition);

        if ($root)
        {
            return $root;
        }
        else
        {
            return false;
        }
    }

    /*
     * DONE
     */

    public function get_root_id($context, $tree_type = self::TREE_TYPE_ROOT, $tree_identifier = 0)
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

    /**
     * @param int $right_id
     * @param string $context
     * @param int $identifier
     * @param int $type
     * @param int $tree_identifier
     * @param int $tree_type
     *
     * @return \Chamilo\Core\Rights\Storage\<array>
     * @throws Exception
     */
    public function get_target_entities(
        $right_id, $context, $identifier = 0, $type = self::TYPE_ROOT, $tree_identifier = 0,
        $tree_type = self::TREE_TYPE_ROOT
    )
    {
        $location = $this->get_location_by_identifier($context, $type, $identifier, $tree_identifier, $tree_type);

        if (!$location)
        {
            throw new RightsLocationNotFoundException(
                Translation::get('NoLocationFound') . $context . ';type=' . $type . ';identifier=' . $identifier .
                ';tree_id=' . $tree_identifier . ';tree_type=' . $tree_type
            );
        }

        return $this->get_target_entities_for_location($right_id, $context, $location);
    }

    /*
     * DONE
     */

    public function get_target_entities_for_location($right_id, $context, $location)
    {
        return DataManager::retrieve_target_entities_array($right_id, $context, $location);
    }

    /*
     * DONE
     */

    /**
     * Returns whether given location or any of its ancestors is in array $location_ids_with_granted_right.
     *
     * @param int $location_id                       location we check whether user has access rigth to.
     * @param array $location_parent_ids             mapping of child location ID's onto parent location ID's. @see
     *                                               get_location_parent_ids_recursive(...)
     * @param array $location_ids_with_granted_right All location ID's which user has access rigth to. Keys: location
     *                                               ID's Values: True.
     *
     * @return bool
     * @see DataManager::filter_location_identifiers_by_granted_right.
     */
    private function has_right_recursive($location_id, $location_parent_ids, $location_ids_with_granted_right)
    {
        if (isset($location_ids_with_granted_right[$location_id]))
        {
            return true;
        }

        if (!isset($location_parent_ids[$location_id]))
        {
            return false;
        }

        return $this->has_right_recursive(
            $location_parent_ids[$location_id], $location_parent_ids, $location_ids_with_granted_right
        );
    }

    /*
     * DONE
     */

    public function invert_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        if (!is_null($entity_id) && !is_null($entity_type) && !empty($right) && !empty($location_id))
        {
            $location_entity_right = DataManager::retrieve_rights_location_entity_right(
                $context, $right, $entity_id, $entity_type, $location_id
            );

            if ($location_entity_right)
            {
                return $location_entity_right->delete();
            }
            else
            {
                $class = $context . '\Storage\DataClass\RightsLocationEntityRight';
                $this->getDataClassRepositoryCache()->truncate($class);

                return $this->create_rights_location_entity_right(
                    $context, $right, $entity_id, $entity_type, $location_id
                );
            }
        }
        else
        {
            return false;
        }
    }

    /*
     * DONE - No longer used
     */

    public function is_allowed(
        $right, $context, $user_id, $entities, $identifier = 0, $type = self::TYPE_ROOT, $tree_identifier = 0,
        $tree_type = self::TREE_TYPE_ROOT
    )
    {
        // //todo: make inherit optional check
        $user_id = $user_id ?: $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID);

        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class, (int) $user_id
        );

        if ($user->isPlatformAdmin())
        {
            return true;
        }

        $location = $this->get_location_by_identifier($context, $type, $identifier, $tree_identifier, $tree_type);

        if (!$location)
        {
            // todo: refactor to translation
            throw new RightsLocationNotFoundException(
                Translation::get('NoLocationFound') . $context . ';type=' . $type . ';identifier=' . $identifier .
                ';tree_id=' . $tree_identifier . ';tree_type=' . $tree_type
            );
        }

        // if ($this->get_locked_parent($location))
        // {
        // $location = $this->get_locked_parent($location);
        // }

        return $this->is_allowed_on_location($right, $context, $user_id, $entities, $location);
    }


    /*
     * DONE
     */

    /**
     * check the right for a specific entity type and id Caches other rights for future reference
     */
    public function is_allowed_for_rights_entity_item($context, $entity_type, $entity_id, $right, $location)
    {
        $granted_rights = $this->get_granted_rights_for_rights_entity_item(
            $context, $entity_type, $entity_id, $location
        );

        if (in_array($right, $granted_rights))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * DONE
     */

    public function is_allowed_for_rights_entity_item_no_inherit(
        $context, $entity_type, $entity_id, $right_id, $location_id
    )
    {
        $conditions = [];

        $class_name = $context . '\Storage\DataClass\RightsLocationEntityRight';
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity_type)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entity_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_RIGHT_ID),
            new StaticConditionVariable($right_id)
        );

        $condition = new AndCondition($conditions);

        if (DataManager::retrieve_rights_location_rights($context, $condition)->count() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * DONE
     */

    public function is_allowed_on_location($right, $context, $user_id, $entities, $location)
    {
        $rights_array = DataManager::retrieve_granted_rights_array(
            $location, $this->get_entities_condition($context, $user_id, $entities)
        );

        return in_array($right, $rights_array);
    }

    /*
     * todo: add caching
     */

    /**
     * Enables a right for a specific entity on a specific location
     *
     * @param String $context
     * @param int $right
     * @param int $entity_id
     * @param int $entity_type
     * @param int $location_id
     *
     * @return bool
     */
    public function set_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        if (!is_null($entity_id) && !is_null($entity_type) && !empty($right) && !empty($location_id))
        {
            $location_entity_right = DataManager::retrieve_rights_location_entity_right(
                $context, $right, $entity_id, $entity_type, $location_id
            );

            if ($location_entity_right)
            {
                return true;
            }
            else
            {
                return $this->create_rights_location_entity_right(
                    $context, $right, $entity_id, $entity_type, $location_id
                );
            }
        }
        else
        {
            return false;
        }
    }

    public function unset_location_entity_right($context, $right, $entity_id, $entity_type, $location_id)
    {
        if (!is_null($entity_id) && !is_null($entity_type) && !empty($right) && !empty($location_id))
        {
            $location_entity_right = DataManager::retrieve_rights_location_entity_right(
                $context, $right, $entity_id, $entity_type, $location_id
            );

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
}
