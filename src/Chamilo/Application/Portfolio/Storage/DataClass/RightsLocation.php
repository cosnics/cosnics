<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * A structural representation of a portfolio item in the context of it's portfolio and the portfolio application
 * 
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{
    // DataClass properties
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_NODE_ID = 'node_id';

    /**
     *
     * @var \repository\ComplexContentObjectPathNode
     */
    private $node;

    /**
     *
     * @var string
     */
    private $parent_id;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $default_property_names = array();
        $default_property_names[] = self :: PROPERTY_ID;
        $default_property_names[] = self :: PROPERTY_PUBLICATION_ID;
        $default_property_names[] = self :: PROPERTY_NODE_ID;
        $default_property_names[] = self :: PROPERTY_INHERIT;
        
        return $default_property_names;
    }

    /**
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     *
     * @return string
     */
    public function get_node_id()
    {
        return $this->get_default_property(self :: PROPERTY_NODE_ID);
    }

    /**
     *
     * @param string $node_id
     */
    public function set_node_id($node_id)
    {
        $this->set_default_property(self :: PROPERTY_NODE_ID, $node_id);
    }

    /**
     *
     * @return DataManager
     */
    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    /**
     *
     * @return \repository\ComplexContentObjectPathNode
     */
    public function get_node()
    {
        return $this->node;
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     */
    public function set_node(ComplexContentObjectPathNode $node)
    {
        $this->node = $node;
    }

    /**
     *
     * @return string
     */
    public function get_parent_id()
    {
        return $this->parent_id;
    }

    /**
     *
     * @param string $parent_id
     */
    public function set_parent_id($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update()
    {
        if ($this->inherits())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation :: class_name(), RightsLocation :: PROPERTY_PUBLICATION_ID), 
                new StaticConditionVariable($this->get_publication_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation :: class_name(), RightsLocation :: PROPERTY_NODE_ID), 
                new StaticConditionVariable($this->get_node_id()));
            $condition = new AndCondition($conditions);
            
            return DataManager :: deletes(RightsLocation :: class_name(), $condition);
        }
        else
        {
            return DataClass :: create();
        }
    }

    /**
     * Clear all configured rights for this location
     * 
     * @return boolean
     */
    public function clear_rights()
    {
        return DataManager :: delete_rights_location_entity_rights($this);
    }

    /**
     * Clear the given right for this location
     * 
     * @param int $right_id
     * @return boolean
     */
    public function clear_right($right_id)
    {
        return DataManager :: delete_rights_location_entity_rights($this, null, null, $right_id);
    }
}
