<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\CourseGroupMenu;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Abstract class to render the tabs of the different
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TabComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $buttonToolbarRenderer;

    /**
     * The introduction text
     *
     * @var string
     */
    protected $introduction_text;

    /**
     * The root course group
     *
     * @var CourseGroup
     */
    protected $rootCourseGroup;

    /**
     * The current course group
     *
     * @var CourseGroup
     */
    protected $currentCourseGroup;

    /**
     * Runs this component
     *
     * @return string
     */
    public function run()
    {
        $this->rootCourseGroup = DataManager::retrieve_course_group_root($this->get_course()->get_id());
        $this->introduction_text = $this->get_introduction_text();
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = array();

        $html[] = $this->render_header();

        $intro_text_allowed = CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
        );

        if ($intro_text_allowed)
        {
            $html[] = $this->display_introduction_text($this->introduction_text);
        }

        $html[] = $this->buttonToolbarRenderer->render();

        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-2">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';

        $html[] = '<div class="col-md-10">';
        $html[] = $this->renderTabs();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    /**
     * Builds the ButtonToolbar and returns the renderer
     *
     * @return ButtonToolBarRenderer
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $param_add_course_group[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                self::ACTION_ADD_COURSE_GROUP;
            $param_add_course_group[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $this->get_group_id();

            $param_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                self::ACTION_SUBSCRIPTIONS_OVERVIEW;
            $param_subscriptions_overview[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] =
                $this->get_group_id();

            if ($this->is_allowed(WeblcmsRights::ADD_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('Create'), new FontAwesomeGlyph('plus'),
                        $this->get_url($param_add_course_group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if (!$this->introduction_text && $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $label = Translation::get('PublishIntroductionText', null, Utilities::COMMON_LIBRARIES);
                $glyph = new FontAwesomeGlyph('info-circle');
                $allowedContentObjectTypes = array(Introduction::class);

                $parameters = $this->get_parameters();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH_INTRODUCTION;

                $actionSelector = new ActionSelector(
                    $this, $this->getUser()->getId(), $allowedContentObjectTypes, $parameters
                );

                $commonActions->addButton($actionSelector->getActionButton($label, $glyph));
            }

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('ViewSubscriptions'), new FontAwesomeGlyph('th-list'), $this->get_url(
                        $param_subscriptions_overview, array(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP)
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the current course group
     *
     * @return CourseGroup
     */
    public function getCurrentCourseGroup()
    {
        if (!$this->currentCourseGroup)
        {
            $id = $this->get_group_id();
            if (!$id || $id == $this->rootCourseGroup->getId())
            {
                $this->currentCourseGroup = $this->rootCourseGroup;
            }
            else
            {
                $this->currentCourseGroup = DataManager::retrieve_by_id(CourseGroup::class, $id);
            }
        }

        return $this->currentCourseGroup;
    }

    protected function getCurrentGroupName()
    {
        $currentCourseGroup = $this->getCurrentCourseGroup();

        if ($currentCourseGroup->get_max_number_of_members() > 0)
        {
            $maxMembersString =
                ' (' . $currentCourseGroup->count_members() . '/' . $currentCourseGroup->get_max_number_of_members() .
                ')';
        }
        else
        {
            $maxMembersString = ' (' . $currentCourseGroup->count_members() . ')';
        }

        return $currentCourseGroup->get_name() . $maxMembersString;
    }

    /**
     * Returns the group id from the request
     *
     * @return int
     */
    protected function get_group_id()
    {
        return $this->getRequest()->get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP);
    }

    /**
     * Checks wheter or not the current group is the root course group
     */
    protected function isCurrentGroupRoot()
    {
        $currentCourseGroup = $this->getCurrentCourseGroup();

        return $currentCourseGroup->getId() == $this->rootCourseGroup->getId();
    }

    /**
     * Returns the HTML for the menu
     *
     * @return string
     */
    protected function renderMenu()
    {
        $group_menu = new CourseGroupMenu($this->get_course(), $this->get_group_id());

        return $group_menu->render_as_tree();
    }

    /**
     * Renders the content for the tab
     *
     * @return string
     */
    abstract protected function renderTabContent();

    /**
     * Renders the tabs as HTML
     *
     * @return string
     */
    protected function renderTabs()
    {
        $translator = Translation::getInstance();

        $tabs = new DynamicVisualTabsRenderer('course_groups', $this->renderTabContent());

        if (!$this->isCurrentGroupRoot())
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    'view_details', $this->getCurrentGroupName(), new FontAwesomeGlyph('info-circle'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_GROUP_DETAILS)),
                    get_class($this) == DetailsComponent::class
                )
            );
        }

        $tabs->add_tab(
            new DynamicVisualTab(
                'view_details', $translator->getTranslation('BrowseChildren', null, Manager::context()),
                new FontAwesomeGlyph('folder'), $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)),
                get_class($this) == BrowserComponent::class
            )
        );

        if (!$this->isCurrentGroupRoot() && $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    'view_details', $translator->getTranslation('EditGroup', null, Manager::context()),
                    new FontAwesomeGlyph('pencil-alt'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_EDIT_COURSE_GROUP)),
                    get_class($this) == EditorComponent::class
                )
            );

            $tabs->add_tab(
                new DynamicVisualTab(
                    'view_details', $translator->getTranslation('ManageSubscriptions', null, Manager::context()),
                    new FontAwesomeGlyph('plus-circle'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_MANAGE_SUBSCRIPTIONS)),
                    get_class($this) == ManageSubscriptionsComponent::class
                )
            );
        }

        return $tabs->render();
    }
}
