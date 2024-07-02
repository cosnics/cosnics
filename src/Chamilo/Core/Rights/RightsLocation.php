<?php
namespace Chamilo\Core\Rights;

use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package    Chamilo\Core\Rights
 * @deprecated Use \Chamilo\Libraries\Rights\Domain\RightsLocation now
 */
abstract class RightsLocation extends NestedSet
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_IDENTIFIER = 'identifier';
    public const PROPERTY_INHERIT = 'inherit';
    public const PROPERTY_LOCKED = 'locked';
    public const PROPERTY_TREE_IDENTIFIER = 'tree_identifier';
    public const PROPERTY_TREE_TYPE = 'tree_type';
    public const PROPERTY_TYPE = 'type';

    /**
     * @var string
     */
    private $context;

    /**
     * @param int $right_id
     *
     * @return mixed
     * @deprecated Use RightsService::deleteRightsLocationEntityRightsForLocationAndParameters() now
     */
    public function clear_right($right_id)
    {
        return DataManager::delete_rights_location_entity_rights(
            location: $this, right_id: $right_id
        );
    }

    /**
     * on this location
     *
     * @deprecated Use RightsService::deleteRightsLocationEntityRightsForLocationAndParameters() now
     */
    public function clear_rights()
    {
        return DataManager::delete_rights_location_entity_rights($this);
    }

    /**
     * @return bool
     * @deprecated Use RightsService::deleteRightsLocationEntityRightsForLocation() now
     */
    public function delete_related_content(): bool
    {
        return $this->clear_rights();
    }

    /**
     * @throws \Exception
     */
    public function disinherit()
    {
        $this->set_inherit(0);
    }

    /**
     * Get the default properties of all users.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_TYPE,
                self::PROPERTY_IDENTIFIER,
                self::PROPERTY_TREE_IDENTIFIER,
                self::PROPERTY_TREE_TYPE,
                self::PROPERTY_INHERIT,
                self::PROPERTY_LOCKED
            ]
        );
    }

    /**
     * @return string[]
     */
    public function getSubTreePropertyNames(): array
    {
        return [self::PROPERTY_TREE_TYPE, self::PROPERTY_TREE_IDENTIFIER];
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     * @return string
     */
    public function get_context()
    {
        if (!isset($this->context))
        {
            $this->context = static::CONTEXT;
        }

        return $this->context;
    }

    /**
     * @param string $context
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function get_identifier()
    {
        return $this->getDefaultProperty(self::PROPERTY_IDENTIFIER);
    }

    /**
     * @return bool
     */
    public function get_inherit()
    {
        return $this->getDefaultProperty(self::PROPERTY_INHERIT);
    }

    /**
     * @return bool
     */
    public function get_locked()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCKED);
    }

    /**
     * @return mixed
     * @deprecated No longer used
     */
    public function get_locked_parent()
    {
        $locked_parent_conditions = $this->get_nested_set_condition_array();

        $locked_parent_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_LOCKED), new StaticConditionVariable(true)
        );

        $locked_parent_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_LEFT_VALUE), ComparisonCondition::LESS_THAN,
            new StaticConditionVariable($this->get_left_value())
        );

        $locked_parent_conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_RIGHT_VALUE), ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($this->get_right_value())
        );

        $locked_parent_condition = new AndCondition($locked_parent_conditions);
        $order =
            new OrderBy([new OrderProperty(new PropertyConditionVariable(self::class, self::PROPERTY_LEFT_VALUE))]);

        return DataManager::retrieve(
            self::class, new DataClassParameters(condition: $locked_parent_condition, orderBy: $order)
        );
    }

    /**
     * Inherited method which specifies how to identify the tree this location is situated in.
     * Should be used as the
     * basic set of condition whenever one makes a query.
     */
    public function get_nested_set_condition_array(): array
    {
        $conditions = parent::get_nested_set_condition_array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($this->get_tree_type())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($this->get_tree_identifier())
        );

        return $conditions;
    }

    /**
     * Retrieves the rights entities linked to this location
     *
     * @param int $right_id - [OPTIONAL] default null
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \Exception
     * @deprecated Use RightsService::findRightsLocationRightsEntitiesForLocationAndRight() now
     */
    public function get_rights_entities($right_id = null)
    {
        $conditions = [];

        $class_name = static::CONTEXT . '\Storage\DataClass\RightsLocationEntityRight';

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
            return $entity_rights;
        }
        else
        {
            throw new Exception(Translation::get('InvalidDataRetrievedFromDatabase'));
        }
    }

    /**
     * @return int
     */
    public function get_tree_identifier()
    {
        return $this->getDefaultProperty(self::PROPERTY_TREE_IDENTIFIER);
    }

    /**
     * @return int
     */
    public function get_tree_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TREE_TYPE);
    }

    /**
     * @deprecated use RightsLocation::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @throws \Exception
     */
    public function inherit()
    {
        $this->set_inherit(1);
    }

    /**
     * @return bool
     */
    public function inherits()
    {
        return $this->get_inherit();
    }

    /**
     * @return bool
     */
    public function is_locked()
    {
        return $this->get_locked();
    }

    /**
     * @return bool
     */
    public function is_root(): bool
    {
        $parent = $this->get_parent();

        return ($parent == 0);
    }

    public function lock()
    {
        $this->set_locked(true);
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    /**
     * @param int $identifier
     */
    public function set_identifier($identifier)
    {
        $this->setDefaultProperty(self::PROPERTY_IDENTIFIER, $identifier);
    }

    /**
     * @param int $inherit
     */
    public function set_inherit($inherit)
    {
        $this->setDefaultProperty(self::PROPERTY_INHERIT, $inherit);
    }

    /**
     * @param int $locked
     */
    public function set_locked($locked)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCKED, $locked);
    }

    /**
     * @param int $tree_identifier
     */
    public function set_tree_identifier($tree_identifier)
    {
        $this->setDefaultProperty(self::PROPERTY_TREE_IDENTIFIER, $tree_identifier);
    }

    /**
     * @param int $tree_type
     */
    public function set_tree_type($tree_type)
    {
        $this->setDefaultProperty(self::PROPERTY_TREE_TYPE, $tree_type);
    }

    /**
     * @deprecated Use RightsLocation::setType() now
     * */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * @param $object
     *
     */
    public function set_type_from_object($object)
    {
        $this->setType(ClassnameUtilities::getInstance()->getClassnameFromObject($object, true));
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
