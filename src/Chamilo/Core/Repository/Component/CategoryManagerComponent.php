<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Category\Interfaces\CategorySupport;
use Chamilo\Configuration\Category\Interfaces\ImpactViewSupport;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ImpactView\ImpactViewTable;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: category_manager.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class CategoryManagerComponent extends Manager implements ImpactViewSupport, TableSupport, CategorySupport
{

    private $impact_view_table_condition;

    protected $impact_view_selected_categories;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Configuration\Category\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        $component = $factory->getComponent();
        $component->set_subcategories_allowed(true);
        return $component->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_category_manager');
    }

    public function get_category_parameters()
    {
        return array();
    }

    /**
     * Returns the condition for a table
     * 
     * @param string $class_name
     *
     * @return Condition
     */
    public function get_table_condition($class_name)
    {
        return $this->impact_view_table_condition;
    }

    /**
     * Renders the impact view
     * 
     * @param int[] $selected_category_ids - [OPTIONAL] default: array
     * @return string
     */
    public function render_impact_view($selected_category_ids = array())
    {
        $this->impact_view_selected_categories = $selected_category_ids;

        $conditions = array();
        foreach ($selected_category_ids as $selected_category_id)
        {
            $this->get_categories_condition($selected_category_id, $conditions);
        }
        
        $condition = new AndCondition(
            new OrCondition($conditions), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE), 
                new StaticConditionVariable(ContentObject::STATE_NORMAL)));
        
        $this->impact_view_table_condition = $condition;
        $impact_view_table = new ImpactViewTable($this);
        
        return $impact_view_table->as_html();
    }

    public function has_impact($selected_category_ids = array())
    {
        $conditions = array();
        foreach ($selected_category_ids as $selected_category_id)
        {
            $this->get_categories_condition($selected_category_id, $conditions);
        }
        
        $condition = new AndCondition(
            new OrCondition($conditions), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE), 
                new StaticConditionVariable(ContentObject::STATE_NORMAL)));
        
        $parameters = new DataClassCountParameters($condition);
        
        return DataManager::count_active_content_objects(ContentObject::class_name(), $parameters) > 0;
    }

    /**
     * Returns the category object
     * 
     * @return RepositoryCategory
     */
    public function get_category()
    {
        $category = new RepositoryCategory();
        $category->set_type($this->getWorkspace()->getWorkspaceType());
        $category->set_type_id($this->getWorkspace()->getId());
        
        return $category;
    }

    /**
     * Counts the categories for a given condition
     * 
     * @param Condition $condition
     *
     * @return int
     */
    public function count_categories($condition)
    {
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID), 
            new StaticConditionVariable($this->getWorkspace()->getId()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE), 
            new StaticConditionVariable($this->getWorkspace()->getWorkspaceType()));
        
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassCountParameters($condition);
        
        return DataManager::count(RepositoryCategory::class_name(), $parameters);
    }

    /**
     * Retrieves the categories for a given condition
     * 
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param int $order_property
     *
     * @return ResultSet<RepositoryCategory>
     */
    public function retrieve_categories($condition, $offset, $count, $order_property)
    {
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID), 
            new StaticConditionVariable($this->getWorkspace()->getId()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE), 
            new StaticConditionVariable($this->getWorkspace()->getWorkspaceType()));
        
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve_categories($condition, $offset, $count, $order_property);
    }

    /**
     * Returns the next display order for a given parent
     * 
     * @param int $parent_id
     *
     * @return int
     */
    public function get_next_category_display_order($parent_id)
    {
        return DataManager::select_next_category_display_order(
            $parent_id, 
            $this->getWorkspace()->getId(), 
            $this->getWorkspace()->getWorkspaceType());
    }

    /**
     * Returns whether or not the user can delete a category
     * 
     * @param int $category_id
     *
     * @return boolean
     */
    public function allowed_to_delete_category($category_id)
    {
        if ($this->getWorkspace() instanceof PersonalWorkspace)
        {
            $object_count = $this->count_category_objects($category_id);
            if ($object_count > 0)
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Count the objects in a category
     * 
     * @param int $category_id
     *
     * @return int
     */
    public function count_category_objects($category_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_PARENT_ID), 
            new StaticConditionVariable($category_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE), 
            new StaticConditionVariable(ContentObject::STATE_NORMAL));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count_active_content_objects(ContentObject::class_name(), $parameters);
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_edit_category()
     */
    public function allowed_to_edit_category($category_id)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_change_category_visibility()
     */
    public function allowed_to_change_category_visibility($category_id)
    {
        return true;
    }

    /**
     * Returns the type
     * 
     * @return int
     */
    public function get_type()
    {
        return $this->getWorkspace()->getWorkspaceType();
    }

    /**
     * Helper function that recursivly builds the categories condition
     * 
     * @param int $category_id
     * @param Condition[] $conditions
     */
    private function get_categories_condition($category_id, &$conditions = array())
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_PARENT_ID), 
            new StaticConditionVariable($category_id));
        
        // retrieve children
        $retrieve_children_condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_PARENT), 
            new StaticConditionVariable($category_id));
        $child_categories = DataManager::retrieve_categories($retrieve_children_condition);
        
        while ($child_category = $child_categories->next_result())
        {
            $this->get_categories_condition($child_category->get_id(), $conditions);
        }
    }

    public function allowed_to_add_category($parent_category_id)
    {
        return true;
    }

    public function get_parameters($include_search = false)
    {
        $extra_parameters = array();
        if(!empty($this->impact_view_selected_categories)) {
            $extra_parameters[\Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID] = $this->impact_view_selected_categories;
            $extra_parameters[\Chamilo\Configuration\Category\Manager::PARAM_ACTION] = \Chamilo\Configuration\Category\Manager::ACTION_IMPACT_VIEW;
        }

        return array_merge(
            $extra_parameters,
            parent::get_parameters($include_search));
    }
}
