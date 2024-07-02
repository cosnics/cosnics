<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Configuration\Category\Interfaces\CategorySupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package application.lib.weblcms.tool.component
 */
class CategoryManagerComponent extends Manager implements BreadcrumbLessComponentInterface, CategorySupport
{

    private $type;

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::MANAGE_CATEGORIES_RIGHT))
        {
            throw new NotAllowedException();
        }

        $request = $this->getRequest();

        $selectedCategoryId =
            $request->getFromRequestOrQuery(\Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID);

        if (!isset($selectedCategoryId))
        {
            $request->query->set(
                \Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID,
                $request->getFromRequestOrQuery(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY)
            );

            $this->getRequest()->request->set(
                \Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID,
                $request->getFromRequestOrQuery(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY)
            );
        }

        $component = $this->getApplicationFactory()->getApplication(
            \Chamilo\Configuration\Category\Manager::CONTEXT,
            new ApplicationConfiguration($request, $this->get_user(), $this)
        );
        $component->set_subcategories_allowed(true);

        return $component->run();
    }

    /**
     * Checks if a new category can be added
     *
     * @param int $parent_category_id
     *
     * @return bool
     */
    public function allowed_to_add_category($parent_category_id)
    {
        return $this->is_allowed(WeblcmsRights::MANAGE_CATEGORIES_RIGHT, null, $parent_category_id);
    }

    public function allowed_to_change_category_visibility($category_id)
    {
        return $this->get_course()->is_course_admin($this->getUser()) || $this->getUser()->isPlatformAdmin();
    }

    public function allowed_to_delete_category($category_id)
    {
        if (!$this->is_allowed(WeblcmsRights::MANAGE_CATEGORIES_RIGHT, null, $category_id))
        {
            return false;
        }

        $category = DataManager::retrieve_by_id(
            ContentObjectPublicationCategory::class, $category_id
        );

        if ($category)
        {
            if (!$category->get_allow_change())
            {
                return false;
            }
        }

        $count = $this->count_category_publications($category_id);
        if ($count > 0)
        {
            return false;
        }

        return !$this->have_subcategories_publications($category_id);
    }

    public function allowed_to_edit_category($category_id)
    {
        if (!$this->is_allowed(WeblcmsRights::MANAGE_CATEGORIES_RIGHT, null, $category_id))
        {
            return false;
        }

        $category = DataManager::retrieve_by_id(
            ContentObjectPublicationCategory::class, $category_id
        );

        if ($category)
        {
            if (!$category->get_allow_change())
            {
                return false;
            }
        }

        return true;
    }

    public function count_categories($condition = null)
    {
        if ($condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_parent()->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_parent()->get_tool_id())
        );

        $condition = new AndCondition($conditions);

        return DataManager::count(
            ContentObjectPublicationCategory::class, new DataClassParameters(condition: $condition)
        );
    }

    public function count_category_publications($category_id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($this->get_parent()->get_course_id())
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
            ), $category_id
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_parent()->get_tool_id())
        );
        $condition = new AndCondition($conditions);

        return DataManager::count_content_object_publications($condition);
    }

    public function getCategory()
    {
        $category = new ContentObjectPublicationCategory();
        $category->set_tool($this->get_parent()->get_tool_id());
        $category->set_course($this->get_parent()->get_course_id());
        $category->set_allow_change(1);

        return $category;
    }

    public function get_category_parameters()
    {
        return [];
    }

    public function get_next_category_display_order($parent_id)
    {
        return null;
    }

    /**
     * Returns the type
     *
     * @return int
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param int $type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::allowed_to_change_category_visibility()
     */

    private function have_subcategories_publications($category_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), new StaticConditionVariable($category_id)
        );
        $subcategries = DataManager::retrieves(
            ContentObjectPublicationCategory::class, new RetrievesParameters(condition: $condition)
        );

        foreach ($subcategries as $cat)
        {
            $count = $this->count_category_publications($cat->get_id());
            if ($count > 0 || $this->have_subcategories_publications($cat->get_id()))
            {
                return true;
            }
        }

        return false;
    }

    /*
     * (non-PHPdoc) @see \configuration\category\CategorySupport::get_category_parameters()
     */

    public function retrieve_categories($condition, $offset = null, $count = null, $order_property = null)
    {
        if ($condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_parent()->get_course_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_parent()->get_tool_id())
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieves(
            ContentObjectPublicationCategory::class,
            new RetrievesParameters(condition: $condition, count: $count, offset: $offset, orderBy: $order_property)
        );
    }
}
