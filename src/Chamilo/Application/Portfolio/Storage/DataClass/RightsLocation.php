<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Portfolio\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_NODE_ID = 'node_id';
    public const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     * @var \repository\ComplexContentObjectPathNode
     */
    private $node;

    /**
     * @var string
     */
    private $parent_id;

    /**
     * Clear the given right for this location
     *
     * @param int $right_id
     *
     * @return bool
     */
    public function clear_right($right_id)
    {
        return DataManager::delete_rights_location_entity_rights($this, null, null, $right_id);
    }

    /**
     * Clear all configured rights for this location
     *
     * @return bool
     */
    public function clear_rights()
    {
        return DataManager::delete_rights_location_entity_rights($this);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $default_property_names = [];
        $default_property_names[] = self::PROPERTY_ID;
        $default_property_names[] = self::PROPERTY_PUBLICATION_ID;
        $default_property_names[] = self::PROPERTY_NODE_ID;
        $default_property_names[] = self::PROPERTY_INHERIT;

        return $default_property_names;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'portfolio_rights_location';
    }

    /**
     * @return \repository\ComplexContentObjectPathNode
     */
    public function get_node()
    {
        return $this->node;
    }

    /**
     * @return string
     */
    public function get_node_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_NODE_ID);
    }

    /**
     * @return string
     */
    public function get_parent_id(): int
    {
        return $this->parent_id;
    }

    /**
     * @return int
     */
    public function get_publication_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * @param ComplexContentObjectPathNode $node
     */
    public function set_node(ComplexContentObjectPathNode $node)
    {
        $this->node = $node;
    }

    /**
     * @param string $node_id
     */
    public function set_node_id($node_id)
    {
        $this->setDefaultProperty(self::PROPERTY_NODE_ID, $node_id);
    }

    /**
     * @param string $parent_id
     */
    public function set_parent_id($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * @see \libraries\storage\DataClass::update()
     */
    public function update(): bool
    {
        if ($this->inherits())
        {
            $conditions = [];
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($this->get_publication_id())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_NODE_ID),
                new StaticConditionVariable($this->get_node_id())
            );
            $condition = new AndCondition($conditions);

            return DataManager::deletes(RightsLocation::class, $condition);
        }
        else
        {
            return DataClass::create();
        }
    }
}
