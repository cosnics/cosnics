<?php
namespace Chamilo\Core\Rights;

use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\NestedTreeNode;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package rights.lib
 * @author  Hans de Bisschop
 */
class RightsLocation extends NestedTreeNode
{
    // Keep track of the context so we know which table to call
    const PROPERTY_IDENTIFIER = 'identifier';

    const PROPERTY_INHERIT = 'inherit';

    const PROPERTY_LOCKED = 'locked';

    const PROPERTY_TREE_IDENTIFIER = 'tree_identifier';

    const PROPERTY_TREE_TYPE = 'tree_type';

    const PROPERTY_TYPE = 'type';

    private $context;

    /**
     * @param integer $right_id
     *
     * @deprecated Use RightsService::deleteRightsLocationEntityRightsForLocationAndRight() now
     *
     * @return mixed
     */
    public function clear_right($right_id)
    {
        return DataManager::delete_rights_location_entity_rights($this, null, null, $right_id);
    }

    /**
     * on this location
     * @deprecated Use RightsService::deleteRightsLocationEntityRightsForLocation() now
     */
    public function clear_rights()
    {
        return DataManager::delete_rights_location_entity_rights($this);
    }

    /**
     * @return boolean
     * @deprecated Use RightsService::deleteRightsLocationEntityRightsForLocation() now
     */
    public function delete_related_content()
    {
        return $this->clear_rights();
    }

    public function disinherit()
    {
        $this->set_inherit(0);
    }

    public function get_context()
    {
        if (!isset($this->context))
        {
            $this->context = $this->package();
        }

        return $this->context;
    }

    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     * Get the default properties of all users.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_TYPE, self::PROPERTY_IDENTIFIER, self::PROPERTY_TREE_IDENTIFIER,
                self::PROPERTY_TREE_TYPE, self::PROPERTY_INHERIT, self::PROPERTY_LOCKED
            )
        );
    }

    public function get_identifier()
    {
        return $this->get_default_property(self::PROPERTY_IDENTIFIER);
    }

    public function get_inherit()
    {
        return $this->get_default_property(self::PROPERTY_INHERIT);
    }

    public function get_locked()
    {
        return $this->get_default_property(self::PROPERTY_LOCKED);
    }

    /**
     * @deprecated No longer used
     * @return mixed
     */
    public function get_locked_parent()
    {
        $locked_parent_conditions = $this->get_nested_set_condition_array();

        $locked_parent_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_LOCKED), new StaticConditionVariable(true)
        );

        $locked_parent_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE),
            ComparisonCondition::LESS_THAN, new StaticConditionVariable($this->get_left_value())
        );

        $locked_parent_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_RIGHT_VALUE),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($this->get_right_value())
        );

        $locked_parent_condition = new AndCondition($locked_parent_conditions);
        $order = array(new OrderBy(new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEFT_VALUE)));

        $datamanager = self::package() . '\Storage\DataManager';

        return $datamanager::retrieve(
            self::class_name(), new DataClassRetrieveParameters($locked_parent_condition, $order)
        );
    }

    /**
     * Inherited method which specifies how to identify the tree this location is situated in.
     * Should be used as the
     * basic set of condition whenever one makes a query.
     */
    public function get_nested_set_condition_array()
    {
        $conditions = parent::get_nested_set_condition_array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($this->get_tree_type())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($this->get_tree_identifier())
        );

        return $conditions;
    }

    /**
     * Retrieves the rights entities linked to this location
     *
     * @param int $right_id - [OPTIONAL] default null
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     * @deprecated Use RightsService::findRightsLocationRightsEntitiesForLocationAndRight() now
     */
    public function get_rights_entities($right_id = null)
    {
        $conditions = array();

        $class_name = static::package() . '\Storage\DataClass\\' . RightsLocationEntityRight::class_name(false);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($this->get_id())
        );

        if (!is_null($right_id))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id)
            );
        }

        $condition = new AndCondition($conditions);

        $entity_rights = DataManager::retrieve_rights_location_rights($this->get_context(), $condition);
        if ($entity_rights)
        {
            return $entity_rights->as_array();
        }
        else
        {
            throw new \Exception(Translation::get('InvalidDataRetrievedFromDatabase'));
        }
    }

    public function get_tree_identifier()
    {
        return $this->get_default_property(self::PROPERTY_TREE_IDENTIFIER);
    }

    public function get_tree_type()
    {
        return $this->get_default_property(self::PROPERTY_TREE_TYPE);
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    public function inherit()
    {
        $this->set_inherit(1);
    }

    public function inherits()
    {
        return $this->get_inherit();
    }

    public function is_locked()
    {
        return $this->get_locked();
    }

    public function is_root()
    {
        $parent = $this->get_parent();

        return ($parent == 0);
    }

    public function lock()
    {
        $this->set_locked(true);
    }

    public function set_identifier($identifier)
    {
        $this->set_default_property(self::PROPERTY_IDENTIFIER, $identifier);
    }

    public function set_inherit($inherit)
    {
        $this->set_default_property(self::PROPERTY_INHERIT, $inherit);
    }

    public function set_locked($locked)
    {
        $this->set_default_property(self::PROPERTY_LOCKED, $locked);
    }

    public function set_tree_identifier($tree_identifier)
    {
        $this->set_default_property(self::PROPERTY_TREE_IDENTIFIER, $tree_identifier);
    }

    public function set_tree_type($tree_type)
    {
        $this->set_default_property(self::PROPERTY_TREE_TYPE, $tree_type);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public function set_type_from_object($object)
    {
        $this->set_type(ClassnameUtilities::getInstance()->getClassnameFromObject($object, true));
    }

    public function switch_inherit()
    {
        if ($this->inherits())
        {
            $this->set_inherit(false);
        }
        else
        {
            $this->set_inherit(true);
        }
    }

    public function switch_lock()
    {
        if ($this->is_locked())
        {
            $this->unlock();
        }
        else
        {
            $this->lock();
        }
    }

    public function unlock()
    {
        $this->set_locked(false);
    }
}
