<?php
namespace Chamilo\Libraries\Rights\Domain;

use Chamilo\Core\Rights\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Libraries\Rights\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class RightsLocation extends NestedSet
{
    public const PROPERTY_IDENTIFIER = 'identifier';
    public const PROPERTY_INHERIT = 'inherit';
    public const PROPERTY_LOCKED = 'locked';
    public const PROPERTY_TREE_IDENTIFIER = 'tree_identifier';
    public const PROPERTY_TREE_TYPE = 'tree_type';
    public const PROPERTY_TYPE = 'type';

    /**
     * @throws \Exception
     */
    public function disinherit()
    {
        $this->set_inherit(0);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
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
     * @return int
     */
    public function get_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
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
        $parent = $this->getParentId();

        return ($parent == 0);
    }

    /**
     * @throws \Exception
     */
    public function lock()
    {
        $this->set_locked(true);
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     */
    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    /**
     * @param int $identifier
     *
     * @throws \Exception
     */
    public function set_identifier($identifier)
    {
        $this->setDefaultProperty(self::PROPERTY_IDENTIFIER, $identifier);
    }

    /**
     * @param int $inherit
     *
     * @throws \Exception
     */
    public function set_inherit($inherit)
    {
        $this->setDefaultProperty(self::PROPERTY_INHERIT, $inherit);
    }

    /**
     * @param int $locked
     *
     * @throws \Exception
     */
    public function set_locked($locked)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCKED, $locked);
    }

    /**
     * @param int $tree_identifier
     *
     * @throws \Exception
     */
    public function set_tree_identifier($tree_identifier)
    {
        $this->setDefaultProperty(self::PROPERTY_TREE_IDENTIFIER, $tree_identifier);
    }

    /**
     * @param int $tree_type
     *
     * @throws \Exception
     */
    public function set_tree_type($tree_type)
    {
        $this->setDefaultProperty(self::PROPERTY_TREE_TYPE, $tree_type);
    }

    /**
     * @deprecated Use RightsLocation::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * @param $object
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function set_type_from_object($object)
    {
        $this->setType(ClassnameUtilities::getInstance()->getClassnameFromObject($object, true));
    }

    /**
     * @throws \Exception
     */
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

    /**
     * @throws \Exception
     */
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

    /**
     * @throws \Exception
     */
    public function unlock()
    {
        $this->set_locked(false);
    }
}
