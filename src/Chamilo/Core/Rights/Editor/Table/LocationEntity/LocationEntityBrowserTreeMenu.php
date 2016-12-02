<?php
namespace Chamilo\Core\Rights\Editor\Table\LocationEntity;

use Chamilo\Libraries\Format\Menu\TreeMenu\GenericTree;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class provides a navigation menu to browse through the entities
 * 
 * @author Sven Vanpoucke
 * @package application\weblcms
 */
class LocationEntityBrowserTreeMenu extends GenericTree
{
    const TREE_NAME = __CLASS__;
    const ROOT_NODE_CLASS = 'category';
    const NODE_CLASS = 'category';

    private $parent;

    /**
     * The selected entity
     * 
     * @var RightsEntity
     */
    private $entity;

    public function __construct($parent, $entity)
    {
        $this->parent = $parent;
        $this->entity = $entity;
        parent::__construct(false, $entity->get_root_ids());
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    public function get_entity()
    {
        return $this->entity;
    }

    public function set_entity($entity)
    {
        $this->entity = $entity;
    }

    public function get_node_url($node_id)
    {
        $parameters = array();
        $parameters[\Chamilo\Core\Rights\Editor\Manager::PARAM_ENTITY_ID] = $node_id;
        
        return $this->get_parent()->get_url($parameters);
    }

    public function get_current_node_id()
    {
        return $this->parent->get_selected_entity_id();
    }

    public function get_node($node_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->entity->data_class_class_name(), $this->entity->get_id_property()), 
            new StaticConditionVariable($node_id));
        return $this->entity->retrieve_entity_items($condition)->next_result();
    }

    public function get_node_children($parent_node_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->entity->data_class_class_name(), $this->entity->get_parent_property()), 
            new StaticConditionVariable($parent_node_id));
        return $this->entity->retrieve_entity_items($condition);
    }

    public function node_has_children($node_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->entity->data_class_class_name(), $this->entity->get_parent_property()), 
            new StaticConditionVariable($node_id));
        $count = $this->entity->count_entity_items($condition);
        
        return ($count > 0);
    }

    public function get_search_url()
    {
        return $this->entity->get_xml_feed();
    }

    public function get_url_format()
    {
        $parameters = array();
        $parameters[\Chamilo\Core\Rights\Editor\Manager::PARAM_ENTITY_ID] = null;
        
        $url_format = $this->get_parent()->get_url($parameters);
        $url_format .= '&' . \Chamilo\Core\Rights\Editor\Manager::PARAM_ENTITY_ID . '=';
        
        return $url_format;
    }

    public function get_root_node_class()
    {
        return self::ROOT_NODE_CLASS;
    }

    public function get_node_class($node)
    {
        return self::NODE_CLASS;
    }

    public function get_root_node_title()
    {
        return Translation::get('Root');
    }

    public function get_node_title($node)
    {
        $property = $this->entity->get_title_property();
        return $node->get_default_property($property);
    }

    public function get_node_safe_title($node)
    {
        return $this->get_node_title($node);
    }

    public function get_node_id($node)
    {
        $property = $this->entity->get_id_property();
        return $node->get_default_property($property);
    }

    public function get_node_parent($node)
    {
        $property = $this->entity->get_parent_property();
        return $node->get_default_property($property);
    }
}
