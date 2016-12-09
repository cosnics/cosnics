<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Menu\TreeMenu\GenericTree;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class provides a navigation menu to allow a user to browse through repository categories
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RepositoryCategoryTreeMenu extends GenericTree
{
    const ROOT_NODE_CLASS = 'category';
    const CATEGORY_CLASS = 'category';

    /**
     *
     * @var \Chamilo\Core\Repository\Manager
     */
    private $parent;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspaceImplementation;

    private $additional_items;

    /**
     * Creates a new category navigation menu.
     * 
     * @param $parent - the parent component
     * @param array $additional_items An array of extra tree items, added to the root.
     */
    public function __construct(WorkspaceInterface $workspaceImplementation, $parent, $additional_items = array())
    {
        $this->workspaceImplementation = $workspaceImplementation;
        $this->parent = $parent;
        $this->additional_items = $additional_items;
        
        parent::__construct();
    }

    /**
     * Builds the tree and adds additional items
     */
    public function build_tree()
    {
        parent::build_tree();
        
        foreach ($this->additional_items as $additional_item)
        {
            $this->tree[] = $additional_item;
        }
    }

    /**
     * Returns the url of a node
     * 
     * @param int $node_id
     *
     * @return string
     */
    public function get_node_url($node_id)
    {
        $url_param[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_CONTENT_OBJECTS;
        $url_param[DynamicTabsRenderer::PARAM_SELECTED_TAB] = array(Manager::TABS_FILTER => Manager::TAB_CATEGORY);
        $url_param[FilterData::FILTER_CATEGORY] = null;
        
        return $this->parent->get_url($url_param) . '&' . FilterData::FILTER_CATEGORY . '=' . $node_id;
    }

    public function get_current_node_id()
    {
        return FilterData::getInstance($this->workspaceImplementation)->get_filter_property(
            FilterData::FILTER_CATEGORY);
    }

    public function get_node($node_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_ID), 
            new StaticConditionVariable($node_id));
        $child = DataManager::retrieve_categories($condition)->next_result();
        return $child;
    }

    public function get_node_children($parent_node_id)
    {
        return DataManager::retrieve_categories(
            $this->get_retrieve_condition($parent_node_id), 
            null, 
            null, 
            new OrderBy(
                new PropertyConditionVariable(
                    RepositoryCategory::class_name(), 
                    RepositoryCategory::PROPERTY_DISPLAY_ORDER)));
    }

    public function node_has_children($parent_node_id)
    {
        return (DataManager::count(RepositoryCategory::class_name(), $this->get_retrieve_condition($parent_node_id)) > 0);
    }

    public function get_search_url()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::package(), 
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_CATEGORY_MENU_FEED));
        return $redirect->getUrl();
    }

    public function get_url_format()
    {
        return $this->get_node_url('%d');
    }

    public function get_root_node_class()
    {
        return self::ROOT_NODE_CLASS;
    }

    public function get_node_class($node)
    {
        return self::CATEGORY_CLASS;
    }

    public function get_root_node_title()
    {
        return $this->workspaceImplementation->getTitle();
    }

    public function get_node_title($node)
    {
        return $node->get_name();
    }

    public function get_node_safe_title($node)
    {
        return $this->get_node_title($node);
    }

    public function get_node_id($node)
    {
        return $node->get_id();
    }

    public function get_node_parent($node)
    {
        return $node->get_parent();
    }

    public function get_breadcrumbs()
    {
        return null;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the retrieve condition
     * 
     * @param int $parent_node_id
     *
     * @return Condition
     */
    protected function get_retrieve_condition($parent_node_id)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_PARENT), 
            new StaticConditionVariable($parent_node_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID), 
            new StaticConditionVariable($this->workspaceImplementation->getId()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE), 
            new StaticConditionVariable($this->workspaceImplementation->getWorkspaceType()));
        
        return new AndCondition($conditions);
    }
}
