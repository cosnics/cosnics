<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Category\Interfaces\CategorySupport;
use Chamilo\Configuration\Category\Interfaces\ImpactViewSupport;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ImpactViewTableRenderer;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Component
 */
class CategoryManagerComponent extends Manager implements ImpactViewSupport, TableSupport, CategorySupport
{

    protected $impact_view_selected_categories;

    private $impact_view_table_condition;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Configuration\Category\Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );
        $component->set_subcategories_allowed(true);

        return $component->run();
    }

    public function allowed_to_add_category($parent_category_id)
    {
        return true;
    }

    public function allowed_to_change_category_visibility($category_id)
    {
        return true;
    }

    /**
     * Returns whether or not the user can delete a category
     *
     * @param int $category_id
     *
     * @return bool
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

    public function allowed_to_edit_category($category_id)
    {
        return true;
    }

    /**
     * Counts the categories for a given condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_categories($condition = null)
    {
        if ($condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->getWorkspace()->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($this->getWorkspace()->getWorkspaceType())
        );

        $condition = new AndCondition($conditions);

        $parameters = new DataClassCountParameters($condition);

        return DataManager::count(RepositoryCategory::class, $parameters);
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
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_PARENT_ID),
            new StaticConditionVariable($category_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
            new StaticConditionVariable(ContentObject::STATE_NORMAL)
        );
        $condition = new AndCondition($conditions);

        $parameters = new DataClassCountParameters($condition);

        return DataManager::count_active_content_objects(ContentObject::class, $parameters);
    }

    /**
     * Returns the category object
     *
     * @return RepositoryCategory
     */
    public function getCategory()
    {
        $category = new RepositoryCategory();
        $category->setType($this->getWorkspace()->getWorkspaceType());
        $category->set_type_id($this->getWorkspace()->getId());

        return $category;
    }

    public function getImpactViewTableRenderer(): ImpactViewTableRenderer
    {
        return $this->getService(ImpactViewTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * Helper function that recursivly builds the categories condition
     *
     * @param int $category_id
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $conditions
     */
    private function get_categories_condition($category_id, &$conditions = [])
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_PARENT_ID),
            new StaticConditionVariable($category_id)
        );

        // retrieve children
        $retrieve_children_condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($category_id)
        );
        $child_categories = DataManager::retrieve_categories($retrieve_children_condition);

        foreach ($child_categories as $child_category)
        {
            $this->get_categories_condition($child_category->get_id(), $conditions);
        }
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_edit_category()
     */

    public function get_category_parameters()
    {
        return [];
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_change_category_visibility()
     */

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
            $parent_id, $this->getWorkspace()->getId(), $this->getWorkspace()->getWorkspaceType()
        );
    }

    public function get_parameters(bool $include_search = false): array
    {
        $extra_parameters = [];
        if (!empty($this->impact_view_selected_categories))
        {
            $extra_parameters[\Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID] =
                $this->impact_view_selected_categories;
            $extra_parameters[\Chamilo\Configuration\Category\Manager::PARAM_ACTION] =
                \Chamilo\Configuration\Category\Manager::ACTION_IMPACT_VIEW;
        }

        return array_merge($extra_parameters, parent::get_parameters($include_search));
    }

    /**
     * Returns the condition for a table
     *
     * @param string $class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($class_name)
    {
        return $this->impact_view_table_condition;
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

    public function has_impact($selected_category_ids = [])
    {
        $conditions = [];
        foreach ($selected_category_ids as $selected_category_id)
        {
            $this->get_categories_condition($selected_category_id, $conditions);
        }

        $condition = new AndCondition(
            [
                new OrCondition($conditions),
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                    new StaticConditionVariable(ContentObject::STATE_NORMAL)
                )
            ]
        );

        $parameters = new DataClassCountParameters($condition);

        return DataManager::count_active_content_objects(ContentObject::class, $parameters) > 0;
    }

    /**
     * @param int[] $selected_category_ids
     *
     * @return string
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function render_impact_view($selected_category_ids = [])
    {
        $this->impact_view_selected_categories = $selected_category_ids;

        $conditions = [];
        foreach ($selected_category_ids as $selected_category_id)
        {
            $this->get_categories_condition($selected_category_id, $conditions);
        }

        $condition = new AndCondition(
            [
                new OrCondition($conditions),
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                    new StaticConditionVariable(ContentObject::STATE_NORMAL)
                )
            ]
        );

        $totalNumberOfItems = DataManager::count_active_content_objects(
            ContentObject::class, new DataClassCountParameters($condition)
        );

        $impactViewTableRenderer = $this->getImpactViewTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $impactViewTableRenderer->getParameterNames(), $impactViewTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassRetrievesParameters(
                $condition, $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
                $impactViewTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $impactViewTableRenderer->render($tableParameterValues, $contentObjects);
    }

    /**
     * Retrieves the categories for a given condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param int $order_property
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory>
     */
    public function retrieve_categories($condition, $offset = null, $count = null, $order_property = null)
    {
        if ($condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->getWorkspace()->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($this->getWorkspace()->getWorkspaceType())
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieve_categories($condition, $offset, $count, $order_property);
    }
}
