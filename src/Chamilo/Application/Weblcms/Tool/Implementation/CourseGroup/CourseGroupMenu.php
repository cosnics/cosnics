<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: group_menu.class.php 224 2009-11-13 14:40:30Z kariboe $
 * 
 * @package group.lib
 */

/**
 * This class provides a navigation menu to allow a user to browse through categories of courses.
 * 
 * @author Sven Vanpoucke
 */
class CourseGroupMenu extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    /**
     * The array renderer used to determine the breadcrumbs.
     */
    private $array_renderer;

    /**
     * The selected group
     * 
     * @var CourseGroup
     */
    private $current_group;

    /**
     * The current course
     * 
     * @var Course
     */
    private $course;

    /**
     * CourseGroupMenu constructor.
     * @param Course $course
     * @param $current_group
     * @param string $url_format
     * @throws ObjectNotExistException
     */
    public function __construct(Course $course, $current_group,
        $url_format = '?application=Chamilo\Application\Weblcms&go=CourseViewer&tool=CourseGroup&tool_action=Details&course=%s&course_group=%s')
    {
        $this->course = $course;
        $this->urlFmt = $url_format;
        
        if ($current_group == '0' || is_null($current_group))
        {
            $this->current_group = DataManager::retrieve_course_group_root($course->getId());
            $url = $this->get_home_url();
        }
        else
        {
            $this->current_group = DataManager::retrieve_by_id(CourseGroup::class_name(), $current_group);
            if(empty($this->current_group)) {
                throw new ObjectNotExistException(
                    Translation::get('TypeNameSingle', array(), 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup'),
                    $current_group
                );
            }
            $url = $this->get_url($this->current_group->get_id());
        }
        
        $menu = $this->get_menu();
        parent::__construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();
        $this->forceCurrentUrl($url);
    }

    public function get_menu()
    {
        $course_group = DataManager::retrieve_course_group_root($this->course->getId());
        
        $menu = array();
        
        $menu_item = array();
        $menu_item['title'] = $course_group->get_name();
        $menu_item['url'] = $this->get_home_url();
        
        $sub_menu_items = $this->get_menu_items($course_group->get_id());
        if (count($sub_menu_items) > 0)
        {
            $menu_item['sub'] = $sub_menu_items;
        }
        
        $menu_item['class'] = 'home';
        $menu_item[OptionsMenuRenderer::KEY_ID] = $course_group->get_id();
        $menu[$course_group->get_id()] = $menu_item;
        return $menu;
    }

    /**
     * Returns the menu items.
     * 
     * @param $extra_items array An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private function get_menu_items($parent_id = 0)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_PARENT_ID), 
            new StaticConditionVariable($parent_id));
        $groups = DataManager::retrieves(CourseGroup::class_name(), new DataClassRetrievesParameters($condition));
        
        // $current_group = $this->current_group;
        
        while ($group = $groups->next_result())
        {
            $menu_item = array();
            $menu_item['title'] = $group->get_name();
            $menu_item['url'] = $this->get_url($group->get_id());
            
            if ($group->has_children())
            {
                $menu_item['sub'] = $this->get_menu_items($group->get_id());
            }
            
            $menu_item['class'] = 'category';
            $menu_item[OptionsMenuRenderer::KEY_ID] = $group->get_id();
            $menu[$group->get_id()] = $menu_item;
        }
        
        return $menu;
    }

    /**
     * Gets the URL of a given category
     * 
     * @param $category int The id of the category
     * @return string The requested URL
     */
    public function get_url($group)
    {
        return htmlentities(sprintf($this->urlFmt, $this->course->getId(), $group));
    }

    private function get_home_url()
    {
        return htmlentities(
            sprintf(
                str_replace(
                    'tool_action=Details', 
                    'tool_action=Browser', 
                    str_replace('&course_group=%s', '', $this->urlFmt)), 
                $this->course->getId()));
    }

    /**
     * Get the breadcrumbs which lead to the current category.
     * 
     * @return array The breadcrumbs.
     */
    public function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $crumb['name'] = $crumb['title'];
            unset($crumb['title']);
        }
        return $breadcrumbs;
    }

    /**
     * Renders the menu as a tree
     * 
     * @return string The HTML formatted tree
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::TREE_NAME, true);
    }
}
