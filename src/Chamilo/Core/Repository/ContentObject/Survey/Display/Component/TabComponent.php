<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Menu;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\ComplexPage;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\ComplexSurvey;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

abstract class TabComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var \core\repository\content_object\page\display\Menu
     */
    protected $menu;

    /**
     *
     * @var \libraries\format\DynamicVisualTabsRenderer
     */
    private $tabs_renderer;

    public function run()
    {
        $survey = $this->get_parent()->get_root_content_object();

        $trail = BreadcrumbTrail :: get_instance();

        if (! $survey)
        {
            return $this->display_error_page(Translation :: getInstance()->getTranslation('NoObjectSelected'));
        }

        $this->menu = new Menu($this);

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

        $this->tabs_renderer = new DynamicVisualTabsRenderer('survey');

        if ($this->get_current_node()->is_root())
        {
            $view_title = $this->get_current_content_object()->get_title();
            $view_image = Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Home');
        }
        else
        {
            $view_title = Translation :: getInstance()->getTranslation('ViewerComponent');
            $view_image = Theme :: getInstance()->getImagePath(
                Manager :: package(),
                'Tab/' . self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT);
        }

        $this->tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                $view_title,
                $view_image,
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                        self :: PARAM_STEP => $this->get_current_step())),
                $this->get_action() == self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()) && RightsService :: getInstance()->canEditContentObject(
            $this->get_user(),
            $this->get_current_content_object()))
        {
            if ($this->get_complex_content_object_item() instanceof ComplexSurvey || $this->get_current_node()->is_root())
            {
                $edit_title = Translation :: getInstance()->getTranslation('EditSurvey');
            }
            elseif ($this->get_complex_content_object_item() instanceof ComplexPage)
            {
                $edit_title = Translation :: getInstance()->getTranslation('EditPage');
            }
            else
            {
                $edit_title = Translation :: getInstance()->getTranslation('EditQuestion');
            }

            $edit_image = Theme :: getInstance()->getImagePath(
                Manager :: package(),
                'Tab/' . self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM);

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

        $this->tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_ACTIVITY,
                Translation :: getInstance()->getTranslation('ActivityComponent'),
                Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_ACTIVITY),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_ACTIVITY,
                        self :: PARAM_STEP => $this->get_current_step())),
                $this->get_action() == self :: ACTION_ACTIVITY,
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        $additional_tabs = $this->get_parent()->get_additional_tabs();

        foreach ($additional_tabs as $additional_tab)
        {
            $this->tabs_renderer->add_tab($additional_tab);
        }

        if (! $this->get_current_node()->is_root() &&
             $this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()->get_parent()))
        {
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                    Translation :: getInstance()->getTranslation('DeleteQuestion'),
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(),
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
            if (($this->get_current_content_object() instanceof Page ||
                 $this->get_current_content_object() instanceof Survey) &&
                 count($this->get_current_node()->get_children()) > 1)
            {

                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_MANAGER,
                        Translation :: getInstance()->getTranslation('ManagerComponent'),
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MANAGER),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MANAGER,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_MANAGER,
                        false,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
        }

        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            if ($this->get_current_node()->get_content_object() instanceof Page ||
                 $this->get_current_node()->get_content_object() instanceof Survey)
            {

                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_MERGE,
                        Translation :: getInstance()->getTranslation('MergerComponent'),
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MERGE),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_MERGE,
                                self :: PARAM_STEP => $this->get_current_step(),
                                \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager :: ACTION_BROWSER)),
                        false,
                        false,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

                $template = \Chamilo\Core\Repository\Configuration :: registration_default_by_type(
                    ClassnameUtilities :: getInstance()->getNamespaceParent(Page :: context(), 2));

                $selected_template_id = TypeSelector :: get_selection();

                $is_selected = ($this->get_action() == self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM &&
                     $selected_template_id != $template->get_id());

                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        Translation :: getInstance()->getTranslation('CreatorComponent'),
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(),
                            'Tab/' . self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                                self :: PARAM_STEP => $this->get_current_step())),
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
                        Translation :: getInstance()->getTranslation('MoveDown'),
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(),
                            'Tab/' . self :: ACTION_SORT . self :: SORT_DOWN),
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
                        Translation :: getInstance()->getTranslation('MoveDownNotAvailable'),
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(),
                            'Tab/' . self :: ACTION_SORT . self :: SORT_DOWN . 'Na'),
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
                        Translation :: getInstance()->getTranslation('MoveUp'),
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(),
                            'Tab/' . self :: ACTION_SORT . self :: SORT_UP),
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
                        Translation :: getInstance()->getTranslation('MoveUpNotAvailable'),
                        Theme :: getInstance()->getImagePath(
                            Manager :: package(),
                            'Tab/' . self :: ACTION_SORT . self :: SORT_UP . 'Na'),
                        null,
                        false,
                        false,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }

            if ((! ($this->get_current_complex_content_object_item() instanceof ComplexSurvey ||
                 $this->get_current_complex_content_object_item() instanceof ComplexPage)) &&
                 $this->get_current_complex_content_object_item()->is_visible())
            {
                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_QUESTION_MANAGER,
                        Translation :: getInstance()->getTranslation('QuestionManagerComponent'),
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MANAGER),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_QUESTION_MANAGER,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_QUESTION_MANAGER,
                        false,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

                $this->tabs_renderer->add_tab(
                    new DynamicVisualTab(
                        self :: ACTION_CREATE_CONFIGURATION,
                        Translation :: getInstance()->getTranslation('ConfigurationCreatorComponent'),
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Configurer'),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_CREATE_CONFIGURATION,
                                self :: PARAM_STEP => $this->get_current_step())),
                        $this->get_action() == self :: ACTION_CREATE_CONFIGURATION,
                        false,
                        DynamicVisualTab :: POSITION_RIGHT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
            $this->tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_CHANGE_QUESTION_VISIBILITY,
                    Translation :: getInstance()->getTranslation('ToggleVissibility'),
                    Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MOVE),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_CHANGE_QUESTION_VISIBILITY,
                            self :: PARAM_STEP => $this->get_current_step())),
                    $this->get_action() == self :: ACTION_CHANGE_QUESTION_VISIBILITY,
                    false,
                    DynamicVisualTab :: POSITION_RIGHT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }

        return $this->build();
    }

    abstract function build();

    /**
     *
     * @see \libraries\SubManager::render_header()
     */
    public function render_header()
    {
        $html[] = parent :: render_header();
        $html[] = '<div style="width: 17%; float: left;">';
        $html[] = '<div style="width: 100%; overflow: auto;">';
        $html[] = $this->menu->render_as_tree();
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="survey-progress">';
        $html[] = $this->getProgressBar();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="survey-navigation">';
        $html[] = $this->get_navigation_bar();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div style="width: 81%; float: right; padding-left: 10px; min-height: 500px;">';

        $html[] = $this->get_tabs_renderer()->renderHeader();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::render_footer()
     */
    public function render_footer()
    {
        $html = array();

        $html[] = $this->get_tabs_renderer()->renderFooter();
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Get the TabsRenderer
     *
     * @return \libraries\format\DynamicVisualTabsRenderer
     */
    private function get_tabs_renderer()
    {
        return $this->tabs_renderer;
    }

    /**
     * Renders the progress bar for the learning path
     *
     * @return array() HTML code of the progress bar
     */
    private function getProgressBar()
    {
        $progress = $this->get_complex_content_object_path()->getProgress(
            $this->getApplicationConfiguration()->getAnswerService());
        return $this->renderProgressBar($progress);
    }

    /**
     *
     * @param integer $percent
     * @param integer $step
     * @return string
     */
    private function renderProgressBar($percent, $step = 2)
    {
        $displayPercent = round($percent);

        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $displayPercent .
             '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $displayPercent . '%; min-width: 2em;">';
        $html[] = $displayPercent . '%';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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

        // if ($this->get_action() != self :: ACTION_REPORTING || $this->is_current_step_set())
        // {
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
                    Theme :: getInstance()->getCommonImagePath('Action/Prev'),
                    $previous_url,
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('PreviousNA'),
                    Theme :: getInstance()->getCommonImagePath('Action/PrevNa'),
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
                    Theme :: getInstance()->getCommonImagePath('Action/Next'),
                    $next_url,
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('PreviousNA'),
                    Theme :: getInstance()->getCommonImagePath('Action/NextNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
    // }
}