<?php
namespace Chamilo\Application\Weblcms\Menu;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Menu\TreeMenu\GenericTree;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package group.lib
 */

/**
 * This class provides a navigation menu to allow a user to browse through categories of courses.
 *
 * @author Pieterjan Broekaert
 */
class PublicationCategoriesTree extends GenericTree
{
    const TREE_NAME = __CLASS__;

    private $browser;

    /**
     * Creates a new category navigation menu.
     *
     * @param $owner int The ID of the owner of the categories to provide in this menu.
     * @param $current_category int The ID of the current category in the menu.
     * @param $url_format string The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param $extra_items array An array of extra tree items, added to the root.
     */
    public function __construct($browser)
    {
        $this->browser = $browser;
        parent::__construct();
    }

    public function get_current_node_id()
    {
        return intval(Request::get(Manager::PARAM_CATEGORY));
    }

    public function get_node($node_id)
    {
        return DataManager::retrieve_by_id(ContentObjectPublicationCategory::class, $node_id);
    }

    public function get_node_children($parent_node_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), new StaticConditionVariable($parent_node_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->browser->get_parent()->get_course_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->browser->get_parent()->get_tool_id())
        );
        if (!$this->is_invisible_allowed())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_VISIBLE
                ), new StaticConditionVariable(true)
            );
        }
        $condition = new AndCondition($conditions);

        $children = DataManager::retrieves(
            ContentObjectPublicationCategory::class, new DataClassRetrievesParameters(
                $condition, null, null, new OrderBy(
                    new PropertyConditionVariable(
                        ContentObjectPublicationCategory::class,
                        ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER
                    )
                )
            )
        );

        return $children;
    }

    public function get_node_class($node)
    {
        if ($this->get_node($node->get_id())->get_visibility())
        {
            if ($this->browser->tool_category_has_new_publications($node->get_id()))
            {
                $glyph = new FontAwesomeGlyph('folder', array('fas-ci-new'), null, 'fas');

                return $glyph->getClassNamesString();
            }
            else
            {
                $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

                return $glyph->getClassNamesString();
            }
        }
        else
        {
            $glyph = new FontAwesomeGlyph('folder', array('text-muted'), null, 'fas');

            return $glyph->getClassNamesString();
        }
    }

    public function get_node_id($node)
    {
        return $node->get_id();
    }

    public function get_node_parent($node)
    {
        return $node->get_parent();
    }

    public function get_node_safe_title($node)
    {
        return $this->get_node_title($node);
    }

    public function get_node_title($node)
    {
        return $node->get_name();
    }

    /**
     * Returns the url of a node
     *
     * @param $node_id int
     *
     * @return string
     */
    public function get_node_url($node_id)
    {
        $url_param[Manager::PARAM_CATEGORY] = $node_id;

        return $this->browser->get_url($url_param);
    }

    public function get_root_node_class()
    {
        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        return $glyph->getClassNamesString();
    }

    public function get_root_node_title()
    {
        $parent = $this->browser->get_parent();
        $course_title = $parent->get_course()->get_title();
        $context = ClassnameUtilities::getInstance()->getNamespaceFromObject($parent);
        $root_title = Translation::get('TypeName', null, $context) . ' ' . $course_title;

        return $root_title;
    }

    public function get_search_url()
    {
        $searchUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Ajax\Manager::package(),
                \Chamilo\Application\Weblcms\Ajax\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Ajax\Manager::ACTION_XML_GROUP_MENU_FEED
            )
        );

        return $searchUrl->getUrl();
    }

    public function get_url_format()
    {
        $course_id = $this->browser->get_parent()->get_course_id();
        $tool = Request::get(Manager::PARAM_TOOL);

        $url_format = '?application=weblcms&course=' . $course_id . '&go=course_viewer&tool=' . $tool;

        $tool_action = Request::get(Manager::PARAM_TOOL_ACTION);
        if (!is_null($tool_action))
        {
            $url_format .= '&tool_action=' . $tool_action;
        }
        $browser_type = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE);
        if (!is_null($browser_type))
        {
            $url_format .= '&browser=' . $browser_type;
        }

        $url_format .= '&publication_category=%d';

        return $url_format;
    }

    /**
     * Returns true if the current user is allowed to view invisible categories.
     */
    public function is_invisible_allowed()
    {
        return $this->browser->is_allowed('view_invisible_category_right');
    }

    public function node_has_children($node_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_PARENT
            ), new StaticConditionVariable($node_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->browser->get_parent()->get_course_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->browser->get_parent()->get_tool_id())
        );
        if (!$this->is_invisible_allowed())
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_VISIBLE
                ), new StaticConditionVariable(true)
            );
        }

        $condition = new AndCondition($conditions);

        return DataManager::count(
                ContentObjectPublicationCategory::class, new DataClassCountParameters($condition)
            ) > 0;
    }
}
