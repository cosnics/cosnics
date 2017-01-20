<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeUserOrder;
use Chamilo\Application\Weblcms\Form\CourseTypeUserCategoryRelCourseForm;
use Chamilo\Application\Weblcms\Form\CourseUserCategoryForm;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: sorter.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */

/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class SorterComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManagePersonalCourses');

        $this->set_parameter(
            CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE,
            Request::get(CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE)
        );

        \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::fix_course_tab_user_orders_for_user(
            $this->get_user_id()
        );
        DataManager::fix_course_type_user_category_rel_course_for_user($this->get_user_id());

        // $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $component_action = Request::get(self::PARAM_COMPONENT_ACTION);
        $this->set_parameter(self::PARAM_COMPONENT_ACTION, $component_action);

        switch ($component_action)
        {
            case 'add' :
                return $this->add_course_user_category();
                break;
            case 'move' :
                return $this->move_course_list();
                break;
            case 'movecat' :
                return $this->move_category_list();
                break;
            case 'assign' :
                return $this->assign_course_category();
                break;
            case 'edit' :
                return $this->edit_course_user_category();
                break;
            case 'delete' :
                $this->delete_course_type_user_category();
                break;
            case 'view' :
                return $this->show_course_list();
                break;
            case 'move_course_type_up' :
                $this->move_course_type(- 1);
                break;
            case 'move_course_type_down' :
                $this->move_course_type(1);
                break;
            default :
                return $this->show_course_list();
        }
    }

    /**
     * Moves a course type for a given direction
     *
     * @param $direction int
     */
    public function move_course_type($direction)
    {
        $selected_course_type_id = Request::get(CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE);

        $course_type_translation = Translation::get('CourseType');

        if (isset($selected_course_type_id) && $selected_course_type_id != 0)
        {
            $selected_course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_by_id(
                CourseType::class_name(),
                $selected_course_type_id
            );

            if (!$selected_course_type)
            {
                throw new ObjectNotExistException($course_type_translation, $selected_course_type_id);
            }

            $selected_course_type_user_order =
                \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_user_order_for_course_type(
                    $selected_course_type_id,
                    $this->get_user_id()
                );

            if (!$selected_course_type_user_order)
            {
                \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::create_course_type_user_orders_for_user(
                    $this->get_user_id()
                );

                DataClassCache::truncate(CourseTypeUserOrder::class_name());

                $selected_course_type_user_order =
                    \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_user_order_for_course_type(
                        $selected_course_type_id,
                        $this->get_user_id()
                    );
            }

            $selected_course_type_user_order->change_display_order_with_count($direction);
            $succes = $selected_course_type_user_order->update();

            $this->redirect(
                Translation::get($succes ? 'CourseTypeMoved' : 'CourseTypeNotMoved'),
                !$succes,
                array(CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE => $selected_course_type_id),
                array(self::PARAM_COMPONENT_ACTION)
            );
        }
        else
        {
            throw new NoObjectSelectedException($course_type_translation);
        }
    }

    public function move_course_list()
    {
        $direction = Request::get(self::PARAM_DIRECTION);
        $course = Request::get(self::PARAM_COURSE);
        $course_type_user_category_id = Request::get(self::PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        if (isset($direction) && isset($course))
        {
            $success = $this->move_course($course, $course_type_user_category_id, $direction);
            $this->redirect(
                Translation::get($success ? 'CourseUserMoved' : 'CourseUserNotMoved'),
                ($success ? false : true),
                array(self::PARAM_COMPONENT_ACTION => self::ACTION_MANAGER_SORT)
            );
        }
        else
        {
            return $this->show_course_list();
        }
    }

    public function move_category_list()
    {
        $direction = Request::get(self::PARAM_DIRECTION);
        $course_type_user_category_id = Request::get(self::PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        if (isset($direction) && isset($course_type_user_category_id))
        {
            $success = $this->move_category($course_type_user_category_id, $direction);
            $this->redirect(
                Translation::get($success ? 'CourseUserCategoryMoved' : 'CourseUserCategoryNotMoved'),
                ($success ? false : true),
                array(self::PARAM_COMPONENT_ACTION => self::ACTION_MANAGER_SORT)
            );
        }
        else
        {
            return $this->show_course_list();
        }
    }

    public function assign_course_category()
    {
        $course_id = Request::get(self::PARAM_COURSE);
        $course_type_user_category_id = Request::get(self::PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        if (!$course_type_user_category_id)
        {
            $course_type_user_category_rel_course = new CourseTypeUserCategoryRelCourse();
            $course_type_user_category_rel_course->set_course_id($course_id);
            $course_type_user_category_rel_course->set_user_id($this->get_user_id());
        }
        else
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse::class_name(),
                    CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_ID
                ),
                new StaticConditionVariable($course_id)
            );

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse::class_name(),
                    CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID
                ),
                new StaticConditionVariable($course_type_user_category_id)
            );

            $condition = new AndCondition($conditions);

            $course_type_user_category_rel_course = DataManager::retrieves(
                CourseTypeUserCategoryRelCourse::class_name(),
                new DataClassRetrievesParameters($condition)
            )->next_result();
        }

        if (!$course_type_user_category_rel_course)
        {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation('CourseUserCategory', null, Manager::context())
            );
        }

        $form = new CourseTypeUserCategoryRelCourseForm(
            $course_type_user_category_rel_course,
            $this->get_user(),
            $this->get_url(
                array(
                    self::PARAM_COURSE => $course_id,
                    self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category_id
                )
            )
        );

        if ($form->validate())
        {
            $success = $form->update_course_type_user_category_rel_course();
            $this->redirect(
                Translation::get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'),
                ($success ? false : true),
                array(self::PARAM_COMPONENT_ACTION => self::ACTION_MANAGER_SORT)
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function move_course($course_id, $course_type_user_category_id, $direction)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse::class_name(),
                CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable($course_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseTypeUserCategoryRelCourse::class_name(),
                CourseTypeUserCategoryRelCourse::PROPERTY_COURSE_TYPE_USER_CATEGORY_ID
            ),
            new StaticConditionVariable($course_type_user_category_id)
        );

        $condition = new AndCondition($conditions);

        $course_type_user_category_rel_course = DataManager::retrieve(
            CourseTypeUserCategoryRelCourse::class_name(),
            new DataClassRetrieveParameters($condition)
        );

        $sort_factor = $direction == 'up' ? - 1 : 1;
        $sort = $course_type_user_category_rel_course->get_sort();
        $new_sort = $sort + $sort_factor;

        $course_type_user_category_rel_course->set_sort($new_sort);

        return $course_type_user_category_rel_course->update();
    }

    public function move_category($course_type_user_category_id, $direction)
    {
        $move_category = DataManager::retrieve_by_id(
            CourseTypeUserCategory::class_name(),
            $course_type_user_category_id
        );

        $sort = $move_category->get_sort();

        $next_category = $this->retrieve_course_type_user_category_at_sort(
            $this->get_user_id(),
            $move_category->get_course_type_id(),
            $sort,
            $direction
        );

        if ($direction == 'up')
        {
            $move_category->set_sort($sort - 1);
        }
        elseif ($direction == 'down')
        {
            $move_category->set_sort($sort + 1);
        }

        if ($move_category->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function add_course_user_category()
    {
        $courseusercategory = new CourseUserCategory();

        $form = new CourseUserCategoryForm(
            CourseUserCategoryForm::TYPE_CREATE,
            $courseusercategory,
            $this->get_user(),
            $this->get_url(),
            $this
        );

        if ($form->validate())
        {
            $success = $form->create_course_user_category();
            $this->redirect(
                Translation::get($success ? 'CourseUserCategoryAdded' : 'CourseUserCategoryNotAdded'),
                ($success ? false : true),
                array(self::PARAM_COMPONENT_ACTION => 'view')
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function edit_course_user_category()
    {
        $course_type_user_category_id = Request::get(self::PARAM_COURSE_TYPE_USER_CATEGORY_ID);
        $course_type_user_category = DataManager::retrieve_by_id(
            CourseTypeUserCategory::class_name(),
            $course_type_user_category_id
        );

        $course_user_category = DataManager::retrieve_by_id(
            CourseUserCategory::class_name(),
            $course_type_user_category->get_course_user_category_id()
        );

        $form = new CourseUserCategoryForm(
            CourseUserCategoryForm::TYPE_EDIT,
            $course_user_category,
            $this->get_user(),
            $this->get_url(array(self::PARAM_COURSE_TYPE_USER_CATEGORY_ID => $course_type_user_category_id)),
            $this
        );

        if ($form->validate())
        {
            $success = $form->update_course_user_category();
            $this->redirect(
                Translation::get($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'),
                ($success ? false : true),
                array(self::PARAM_COMPONENT_ACTION => 'view')
            );
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function delete_course_type_user_category()
    {
        $course_type_user_category_id = Request::get(self::PARAM_COURSE_TYPE_USER_CATEGORY_ID);

        $course_type_user_category = DataManager::retrieve_by_id(
            CourseTypeUserCategory::class_name(),
            $course_type_user_category_id
        );

        $success = $course_type_user_category->delete();
        $this->redirect(
            Translation::get($success ? 'CourseUserCategoryDeleted' : 'CourseUserCategoryNotDeleted'),
            ($success ? false : true),
            array(self::PARAM_COMPONENT_ACTION => 'view')
        );
    }

    public function display_page_header()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = '<div class="clear"></div><br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Shows the tabs of the course types For each of the tabs show the course list
     */
    public function show_course_list()
    {
        $renderer = new CourseTypeCourseListRenderer($this);

        $html = array();

        $html[] = $this->display_page_header();
        $html[] = $this->getButtonToolbarRenderer($renderer)->render();
        $html[] = '<div class="clear"></div><br />';
        $html[] = $renderer->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the action bar for the given course list renderer
     *
     * @param $renderer CourseTypeCourseListRenderer
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer(CourseTypeCourseListRenderer $renderer = null)
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('CreateCourseUserCategory'),
                    Theme::getInstance()->getCommonImagePath('Action/Create'),
                    $this->get_course_user_category_add_url(),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            /**
             * Retrieve the user order and if not existing, fall back on the default order
             */
            $course_type_id = $renderer->get_selected_course_type_id();

            $course_type_user_order =
                \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::retrieve_user_order_for_course_type(
                    $course_type_id,
                    $this->get_user_id()
                );

            if ($course_type_user_order)
            {
                $display_order = $course_type_user_order->get_display_order();
                $max =
                    \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::get_max_display_order_for_course_type_user_orders(
                        $this->get_user_id()
                    );
            }
            else
            {
                $selected_course_type = $renderer->get_selected_course_type();

                if ($selected_course_type)
                {
                    $display_order = $selected_course_type->get_display_order();
                    $max =
                        \Chamilo\Application\Weblcms\CourseType\Storage\DataManager::get_max_display_order_for_course_types(
                        );
                }
                else
                {
                    $display_order = $max = 1;
                }
            }

            if ($display_order > 1)
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('MoveCourseType'),
                        Theme::getInstance()->getCommonImagePath('Action/Left'),
                        $this->get_url(
                            array(
                                self::PARAM_COMPONENT_ACTION => 'move_course_type_up',
                                CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE => $course_type_id
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
            else
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('MoveCourseTypeNA'),
                        Theme::getInstance()->getCommonImagePath('Action/LeftNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if ($display_order < $max)
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('MoveCourseType'),
                        Theme::getInstance()->getCommonImagePath('Action/Right'),
                        $this->get_url(
                            array(
                                self::PARAM_COMPONENT_ACTION => 'move_course_type_down',
                                CourseTypeCourseListRenderer::PARAM_SELECTED_COURSE_TYPE => $course_type_id
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
            else
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('MoveCourseTypeNA'),
                        Theme::getInstance()->getCommonImagePath('Action/RightNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_course_actions($course_type_user_category = array(), $course = null, $offset = 1, $count = 1)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($course_type_user_category)
        {
            if ($offset > 0 && $count > 1)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Up'),
                        $this->get_course_user_move_url($course_type_user_category, $course, 'up'),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveUpNA', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/UpNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if ($offset < ($count - 1) && $count > 1)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Down'),
                        $this->get_course_user_move_url($course_type_user_category, $course, 'down'),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveDownNA', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/DownNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Move'),
                $this->get_course_user_edit_url($course_type_user_category, $course),
                ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }

    public function get_course_type_user_category_actions($course_type_user_category, $offset = 1, $count = 1)
    {
        if (!$course_type_user_category)
        {
            return;
        }

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($offset > 0 && $count > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Up'),
                    $this->get_course_user_category_move_url($course_type_user_category, 'up'),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUpNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/UpNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($offset < ($count - 1) && $count > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Down'),
                    $this->get_course_user_category_move_url($course_type_user_category, 'down'),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDownNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/DownNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_course_user_category_edit_url($course_type_user_category),
                ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_course_user_category_delete_url($course_type_user_category),
                ToolbarItem::DISPLAY_ICON,
                true
            )
        );

        return '<div style="float:right;">' . $toolbar->as_html() . '</div>';
    }

    public function show_empty_courses()
    {
        return true;
    }
}
