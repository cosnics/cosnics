<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    // Actions
    const ACTION_FEEDBACK = 'feedback';
    const ACTION_BOOKMARK = 'bookmarker';
    const ACTION_ACTIVITY = 'activity';
    const ACTION_RIGHTS = 'rights';
    const ACTION_MOVE = 'mover';
    const ACTION_SORT = 'sorter';
    const ACTION_MANAGE = 'manager';
    const ACTION_USER = 'user';
    const ACTION_BUILD_PREREQUISITES = 'prerequisites_builder';
    const ACTION_TYPE_SPECIFIC = 'type_specific';
    const ACTION_BUILD = 'builder';
    const ACTION_REPORTING = 'reporting';
    const ACTION_ATTEMPT = 'attempt';

    // Parameters
    const PARAM_STEP = 'step';
    const PARAM_SHOW_PROGRESS = 'show_progress';
    const PARAM_DETAILS = 'details';
    const PARAM_LEARNING_PATH_ITEM_ID = 'learning_path_item_id';
    const PARAM_SORT = 'sort';
    const PARAM_ITEM_ATTEMPT_ID = 'item_attempt_id';

    // Sorting
    const SORT_UP = 'up';
    const SORT_DOWN = 'down';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    private $learning_path_menu;

    public function get_prerequisites_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BUILD_PREREQUISITES,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    public function get_mastery_score_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SET_MASTERY_SCORE,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    public function get_configuration_url($selected_complex_content_object_item)
    {
        $complex_content_object_item_id = ($this->get_complex_content_object_item()) ? ($this->get_complex_content_object_item()->get_id()) : null;
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CONFIGURE_FEEDBACK,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id,
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item));
    }

    public function run()
    {
        $learning_path = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $learning_path)
        {
            return $this->display_error_page(Translation :: get('NoObjectSelected'));
        }

        $this->learning_path_menu = new Menu($this);

        $this->set_complex_content_object_item($this->get_current_complex_content_object_item());

        foreach ($this->get_root_content_object()->get_complex_content_object_path()->get_parents_by_id(
            $this->get_current_step(),
            true,
            true) as $node_parent)
        {
            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_STEP] = $node_parent->get_id();
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb($this->get_url($parameters), $node_parent->get_content_object()->get_title()));
        }

        $this->tabs_renderer = new DynamicVisualTabsRenderer('learning_path');

        if ($this->get_action() == self :: ACTION_REPORTING && ! $this->is_current_step_set())
        {
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_REPORTING,
                    Translation :: get('ReportingComponent'),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_REPORTING),
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING)),
                    $this->get_action() == self :: ACTION_REPORTING,
                    false,
                    DynamicVisualTab :: POSITION_LEFT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        else
        {

            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    Translation :: get('ViewerComponent'),
                    Theme :: getInstance()->getImagePath(
                        __NAMESPACE__,
                        'Tab/' . self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                            self :: PARAM_STEP => $this->get_current_step())),
                    $this->get_action() == self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    false,
                    DynamicVisualTab :: POSITION_LEFT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

            $edit_title = Translation :: get('UpdaterComponent');
            $edit_image = Theme :: getInstance()->getImagePath(
                __NAMESPACE__,
                'Tab/' . self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM);

            $current_content_object = $this->get_current_node()->get_content_object();

            if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()) &&
                 $current_content_object->has_right(RepositoryRights :: COLLABORATE_RIGHT))
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        $edit_title,
                        $edit_image,
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        false,
                        DynamicVisualTab :: POSITION_LEFT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }

            if (! $this->get_current_node()->get_content_object() instanceof LearningPath)
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_BUILD_PREREQUISITES,
                        Translation :: get('BuildPrerequisites'),
                        Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_BUILD_PREREQUISITES),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_BUILD_PREREQUISITES,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_BUILD_PREREQUISITES,
                        false,
                        DynamicVisualTab :: POSITION_LEFT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }

            foreach ($this->get_node_specific_tabs($this->get_current_node()) as $tab)
            {
                $this->tabs_renderer->add_tab($tab);
            }

            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_ACTIVITY,
                    Translation :: get('ActivityComponent'),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_ACTIVITY),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_ACTIVITY,
                            self :: PARAM_STEP => $this->get_current_step())),
                    $this->get_action() == self :: ACTION_ACTIVITY,
                    false,
                    DynamicVisualTab :: POSITION_LEFT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_REPORTING,
                    Translation :: get('ReportingComponent'),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_REPORTING),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_REPORTING,
                            self :: PARAM_STEP => $this->get_current_step())),
                    $this->get_action() == self :: ACTION_REPORTING,
                    false,
                    DynamicVisualTab :: POSITION_LEFT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

            if (! $this->get_current_node()->is_root() &&
                 $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
            {
                $variable = $this->get_current_content_object() instanceof LearningPath ? 'DeleteFolder' : 'DeleterComponent';

                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                        Translation :: get($variable),
                        Theme :: getInstance()->getImagePath(
                            __NAMESPACE__,
                            'Tab/' . self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                        true,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }

            if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
            {
                if ($this->get_current_content_object() instanceof LearningPath &&
                     count($this->get_current_node()->get_children()) > 1)
                {
                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_MANAGE,
                            Translation :: get('ManagerComponent'),
                            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_MANAGE),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_MANAGE,
                                    self :: PARAM_STEP => $this->get_current_step())),
                            $this->get_action() == self :: ACTION_MANAGE,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                }
            }

            if (! $this->get_current_node()->is_root() &&
                 $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
            {
                $variable = $this->get_current_content_object() instanceof LearningPath ? 'MoveFolder' : 'MoverComponent';

                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_MOVE,
                        Translation :: get($variable),
                        Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_MOVE),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MOVE,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_MOVE,
                        false,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }

            if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
            {
                if ($this->get_current_node()->get_content_object() instanceof LearningPath)
                {
                    $template = \Chamilo\Core\Repository\Configuration :: registration_default_by_type(
                        LearningPath :: context());

                    $selected_template_id = TypeSelector :: get_selection();

                    $is_selected = ($this->get_action() == self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM &&
                         $selected_template_id != $template->get_id());

                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Translation :: get('CreatorComponent'),
                            Theme :: getInstance()->getImagePath(
                                __NAMESPACE__,
                                'Tab/' . self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                                    self :: PARAM_STEP => $this->get_current_step())),
                            $is_selected,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

                    $is_selected = ($this->get_action() == self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM &&
                         $selected_template_id == $template->get_id());

                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Translation :: get('AddFolder'),
                            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/folder'),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                                    self :: PARAM_STEP => $this->get_current_step(),
                                    TypeSelector :: PARAM_SELECTION => $template->get_id())),
                            $is_selected,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                }
            }

            if (! $this->get_current_node()->is_root() &&
                 $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()) &&
                 $this->get_current_node()->has_siblings())
            {
                if (! $this->get_current_node()->is_last_child())
                {
                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_SORT,
                            Translation :: get('MoveDown'),
                            Theme :: getInstance()->getImagePath(
                                __NAMESPACE__,
                                'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_DOWN),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_SORT,
                                    self :: PARAM_SORT => self :: SORT_DOWN,
                                    self :: PARAM_STEP => $this->get_current_step())),
                            $this->get_action() == self :: ACTION_SORT,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                }
                else
                {
                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_SORT,
                            Translation :: get('MoveDownNotAvailable'),
                            Theme :: getInstance()->getImagePath(
                                __NAMESPACE__,
                                'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_DOWN . '_na'),
                            null,
                            false,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                }

                if (! $this->get_current_node()->is_first_child())
                {
                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_SORT,
                            Translation :: get('MoveUp'),
                            Theme :: getInstance()->getImagePath(
                                __NAMESPACE__,
                                'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_UP),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_SORT,
                                    self :: PARAM_SORT => self :: SORT_UP,
                                    self :: PARAM_STEP => $this->get_current_step())),
                            $this->get_action() == self :: ACTION_SORT,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                }
                else
                {
                    $this->tabs_renderer->add_tab(
                        new DynamicVisualTab(
                            self :: ACTION_SORT,
                            Translation :: get('MoveUpNotAvailable'),
                            Theme :: getInstance()->getImagePath(
                                __NAMESPACE__,
                                'Tab/' . self :: ACTION_SORT . '_' . self :: SORT_UP . '_na'),
                            null,
                            false,
                            false,
                            DynamicVisualTab :: POSITION_RIGHT,
                            DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
                }
            }
        }
    }

    /**
     *
     * @see \libraries\SubManager::render_header()
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = '<div style="width: 17%; float: left;">';
        $html[] = '<div style="width: 100%; overflow: auto;">';
        $html[] = $this->learning_path_menu->render_as_tree();
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="learning-path-progress">';
        $html[] = $this->get_progress_bar();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="learning-path-navigation">';
        $html[] = $this->get_navigation_bar();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div style="width: 81%; float: right; padding-left: 10px; min-height: 500px;">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::render_footer()
     */
    public function render_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Get the id of the currently requested step
     *
     * @return int
     */
    public function get_current_step()
    {
        if (! isset($this->current_step))
        {
            if ($this->is_current_step_set())
            {
                $this->current_step = $this->get_current_step_from_request();

                if (is_array($this->current_step))
                {
                    $this->current_step = $this->current_step[0];
                }
            }
            else
            {
                $this->current_step = $this->get_complex_content_object_path()->get_root()->get_id();
            }
        }

        return $this->current_step;
    }

    /**
     *
     * @return boolean
     */
    public function is_current_step_set()
    {
        return ! is_null(Request :: get(self :: PARAM_STEP));
    }

    /**
     *
     * @return int
     */
    private function get_current_step_from_request()
    {
        return Request :: get(self :: PARAM_STEP);
    }

    /**
     * Get the content object linked to the current step
     *
     * @return \core\repository\ContentObject
     */
    public function get_current_content_object()
    {
        return $this->get_current_node()->get_content_object();
    }

    /**
     * Get the complex content object item linked to the current step
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_current_complex_content_object_item()
    {
        return $this->get_current_node()->get_complex_content_object_item();
    }

    /**
     * Get the TabsRenderer
     *
     * @return \libraries\format\DynamicVisualTabsRenderer
     */
    public function get_tabs_renderer()
    {
        return $this->tabs_renderer;
    }

    /**
     * Get the node linked to the current step
     *
     * @return \core\repository\common\path\ComplexContentObjectPathNode
     */
    public function get_current_node()
    {
        return $this->get_complex_content_object_path()->get_node($this->get_current_step());
    }

    public function get_complex_content_object_path()
    {
        $learning_path_item_attempt_data = $this->get_parent()->retrieve_learning_path_tracker_items(
            $this->get_parent()->retrieve_learning_path_tracker());

        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path(
            $learning_path_item_attempt_data);
    }

    public function get_learning_path_menu()
    {
        return $this->learning_path_menu;
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP);
    }

    public function get_node_specific_tabs(ComplexContentObjectPathNode $node)
    {
        $object_namespace = $node->get_content_object()->context();
        $integration_class_name = $object_namespace . '\integration\\' . __NAMESPACE__ . '\Manager';

        if (class_exists($integration_class_name))
        {
            try
            {
                $manager = new $integration_class_name($this->get_user(), $this);
                return $manager->get_node_tabs($node);
            }
            catch (\Exception $exception)
            {
                return array();
            }
        }
        else
        {
            return array();
        }
    }

    /**
     * Retrieves the navigation menu for the learning path
     *
     * @param $total_steps int
     * @param $current_step int
     * @param $current_content_object ContentObject
     */
    private function get_navigation_bar()
    {
        $current_node = $this->get_current_node();

        if ($this->get_action() != self :: ACTION_REPORTING || $this->is_current_step_set())
        {
            $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

            $previous_node = $current_node->get_previous();

            if ($previous_node instanceof ComplexContentObjectPathNode)
            {
                $previous_url = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                        self :: PARAM_STEP => $previous_node->get_id()));

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Previous'),
                        Theme :: getInstance()->getCommonImagePath('action_prev'),
                        $previous_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('PreviousNA'),
                        Theme :: getInstance()->getCommonImagePath('action_prev_na'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }

            $next_node = $current_node->get_next();

            if ($next_node instanceof ComplexContentObjectPathNode)
            {
                $next_url = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                        self :: PARAM_STEP => $next_node->get_id()));

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Previous'),
                        Theme :: getInstance()->getCommonImagePath('action_next'),
                        $next_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('PreviousNA'),
                        Theme :: getInstance()->getCommonImagePath('action_next_na'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }

            return $toolbar->as_html();
        }
    }

    /**
     * Renders the progress bar for the learning path
     *
     * @return array() HTML code of the progress bar
     */
    private function get_progress_bar()
    {
        // $progress = $this->learning_path_menu->get_progress();
        $progress = $this->get_complex_content_object_path()->get_progress();

        return Display :: get_progress_bar($progress);
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_attempt_data()
    {
        return $this->get_application()->is_allowed_to_edit_learning_path_attempt_data();
    }
}
