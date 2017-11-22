<?php
namespace Chamilo\Application\Weblcms\Ajax\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

class XmlPublicationsTreeFeedComponent extends \Chamilo\Application\Weblcms\Ajax\Manager
{

    public function run()
    {
        $publications_tree = array();
        $course = null;
        $user = null;

        $category_id = Request::get('parent_id');

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_PARENT),
            new StaticConditionVariable($category_id));

        $categories_tree = DataManager::retrieves(
            ContentObjectPublicationCategory::class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(
                            ContentObjectPublicationCategory::class_name(),
                            ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER)))))->as_array();

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n", '<tree>' . "\n";
        echo $this->dump_tree($categories_tree);
        echo '</tree>';
    }

    function dump_tree($categories)
    {
        if ($this->contains_results($categories))
        {
            $this->dump_categories_tree($categories);
        }
    }

    function dump_categories_tree($categories)
    {
        foreach ($categories as $category)
        {
            $has_children = $this->has_sub_categories($category->get_id()) ? 1 : 0;
            $class = $this->get_category_class($category);

            echo '<leaf id="' . $category->get_id() . '" classes="' . $class . '" has_children="';
            echo $has_children . '" title="' . htmlspecialchars($category->get_name()) . '" description="';
            echo htmlspecialchars($category->get_name()) . '"/>' . "\n";
        }
    }

    function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }
        return false;
    }

    function has_sub_categories($category_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_PARENT),
            new StaticConditionVariable($category_id));
        return DataManager::count(ContentObjectPublicationCategory::class_name(), new DataClassCountParameters($condition)) > 0;
    }

    function get_category_class(ContentObjectPublicationCategory $category)
    {
        global $course, $user;

        if (! $course)
        {
            $course = CourseDataManager::retrieve_by_id(Course::class_name(), $category->get_course());
        }

        if (! $user)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::get_current_user();
        }

        if ($category->get_visibility())
        {
            if (DataManager::tool_category_has_new_publications(
                $category->get_tool(),
                $user,
                $course,
                $category->get_id()))
            {
                return 'new_category';
            }
            else
            {
                return 'category';
            }
        }
        else
        {
            return 'invisible_category';
        }
    }
}
