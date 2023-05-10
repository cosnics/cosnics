<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroupUserTableRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseUsersTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

class SubscriptionsOverviewerComponent extends Manager
{
    public const PLATFORM_GROUP_ROOT_ID = 0;

    public const TAB_COURSE_GROUPS = 2;
    public const TAB_USERS = 1;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private string $current_tab;

    private string $table_course_group_id;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->current_tab = $this->getRequest()->query->get(self::PARAM_TAB, (string) self::TAB_COURSE_GROUPS);

        $html = [];

        $html[] = $this->renderHeader();

        $course_settings_controller = CourseSettingsController::getInstance();

        if ($course_settings_controller->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
        ))
        {
            $html[] = $this->display_introduction_text($this->get_introduction_text());
        }

        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->get_tabs();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_TAB;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_GROUP;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $parameters = [];

            $buttonToolbar = new ButtonToolBar($this->get_url($parameters));
            $commonActions = new ButtonGroup();

            $param_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                self::ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW;
            $param_export_subscriptions_overview[self::PARAM_TAB] = $this->current_tab;

            if ($this->is_allowed(WeblcmsRights::VIEW_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        $this->getTranslator()->trans('Export', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('download'), $this->get_url($param_export_subscriptions_overview),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getCourseGroupUserTableRenderer(): CourseGroupUserTableRenderer
    {
        return $this->getService(CourseGroupUserTableRenderer::class);
    }

    public function getCourseUsersTableRenderer(): CourseUsersTableRenderer
    {
        return $this->getService(CourseUsersTableRenderer::class);
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \QuickformException
     */
    public function get_condition(): ?AndCondition
    {
        $conditions = [];
        $search_condition = $this->get_search_condition();
        if ($search_condition)
        {
            $conditions[] = $search_condition;
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }

        return null;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    private function get_course_groups_tab(): string
    {
        $courseGroupRoot = DataManager::retrieve_course_group_root($this->get_course_id());
        $course_groups = $courseGroupRoot->get_children();

        return $this->handle_course_groups($course_groups);
    }

    public function get_group()
    {
        return $this->getRequest()->query->get(
            \Chamilo\Application\Weblcms\Manager::PARAM_GROUP, self::PLATFORM_GROUP_ROOT_ID
        );
    }

    /**
     * @throws \QuickformException
     */
    public function get_search_condition(): ?OrCondition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            if ($this->current_tab == self::TAB_USERS)
            {
                $conditions = [];

                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE), $query
                );
                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query
                );
                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query
                );
                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
                );
                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL), $query
                );

                return new OrCondition($conditions);
            }
        }

        return null;
    }

    public function get_table_course_group_id(): string
    {
        return $this->table_course_group_id;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    private function get_tabs(): string
    {
        $translator = $this->getTranslator();
        $tabs = new TabsCollection();

        // all tab
        $link = $this->get_url([self::PARAM_TAB => self::TAB_USERS]);
        $tab_name = $translator->trans('User', [], Manager::CONTEXT);
        $tabs->add(
            new LinkTab(
                (string) self::TAB_USERS, $tab_name, new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas'), $link,
                $this->current_tab == self::TAB_USERS
            )
        );

        // users tab
        $link = $this->get_url([self::PARAM_TAB => self::TAB_COURSE_GROUPS]);
        $tab_name = $translator->trans('CourseGroup', [], Manager::CONTEXT);
        $tabs->add(
            new LinkTab(
                (string) self::TAB_COURSE_GROUPS, $tab_name, new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'),
                $link, $this->current_tab == self::TAB_COURSE_GROUPS
            )
        );

        return $this->getLinkTabsRenderer()->render($tabs, $this->get_tabs_content());
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    private function get_tabs_content(): string
    {
        switch ($this->current_tab)
        {
            case self::TAB_USERS :
                return $this->get_users_tab();
            case self::TAB_COURSE_GROUPS :
                return $this->get_course_groups_tab();
            default:
                return '';
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Doctrine\DBAL\Exception
     */
    private function get_users_tab(): string
    {
        $totalNumberOfItems = \Chamilo\Application\Weblcms\Course\Storage\DataManager::count_all_course_users(
            $this->get_course_id(), $this->get_search_condition()
        );
        $courseUsersTableRenderer = $this->getCourseUsersTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $courseUsersTableRenderer->getParameterNames(), $courseUsersTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_course_users(
            $this->get_course_id(), $this->get_search_condition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $courseUsersTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $courseUsersTableRenderer->legacyRender($this, $tableParameterValues, $users);
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function handle_course_groups(ArrayCollection $course_groups): string
    {
        $html = [];

        foreach ($course_groups as $course_group)
        {
            $this->table_course_group_id = $course_group->get_id();

            $html[] = '<h4>' . $course_group->get_name() . '</h4>' . $this->renderCourseGroupUserTable();

            $children = $course_group->get_children();
            $html[] = $this->handle_course_groups($children);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderCourseGroupUserTable(): string
    {
        $totalNumberOfItems =
            DataManager::count_course_group_users($this->get_table_course_group_id(), $this->get_search_condition());
        $courseGroupUserTableRenderer = $this->getCourseGroupUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $courseGroupUserTableRenderer->getParameterNames(),
            $courseGroupUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = DataManager::retrieve_course_group_users_with_subscription_time(
            $this->get_table_course_group_id(), $this->get_search_condition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $courseGroupUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $courseGroupUserTableRenderer->render($tableParameterValues, $users);
    }
}
