<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\SubscribedPlatformGroupMenuRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\AllSubscribedUserTableRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\DirectSubscribedPlatformGroupTableRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\PlatformGroupRelUserTableRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\SubscribedUserTableRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\SubSubscribedPlatformGroupTableRenderer;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Component
 * @author  Stijn Van Hoecke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscribeBrowserComponent extends Manager
{
    public const PLATFORM_GROUP_ROOT_ID = 0;

    public const TAB_ALL = 1;
    public const TAB_PLATFORM_GROUPS_SUBGROUPS = 4;
    public const TAB_PLATFORM_GROUPS_USERS = 3;
    public const TAB_USERS = 2;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private string $current_tab;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run()
    {
        // default all tab, unless specified
        $this->current_tab = $this->getRequest()->query->get(self::PARAM_TAB, self::TAB_ALL);

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $this->set_parameter(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->buttonToolbarRenderer->getSearchForm()->getQuery()
        );

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->get_tabs();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_TAB;

        $current_tab = $this->getRequest()->query->get(self::PARAM_TAB, self::TAB_ALL);

        if ($current_tab != self::TAB_ALL && $current_tab != self::TAB_USERS)
        {
            $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_GROUP;
        }

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getAllSubscribedUserTableRenderer(): AllSubscribedUserTableRenderer
    {
        return $this->getService(AllSubscribedUserTableRenderer::class);
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

            $translator = $this->getTranslator();

            $group_id = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
            if (isset($group_id))
            {
                $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_GROUP] = $group_id;
            }

            $buttonToolbar = new ButtonToolBar($this->get_url($parameters));
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('SubscribeUsers', [], Manager::CONTEXT), new FontAwesomeGlyph('user'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER]),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        $translator->trans('SubscribeGroups', [], Manager::CONTEXT), new FontAwesomeGlyph('users'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS]),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $param_export_subscriptions_overview = [];
                $param_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    self::ACTION_EXPORT;
                $param_export_subscriptions_overview[self::PARAM_TAB] = $this->current_tab;

                $toolActions->addButton(
                    new Button(
                        $translator->trans('ExportUserList', [], Manager::CONTEXT), new FontAwesomeGlyph('download'),
                        $this->get_url($param_export_subscriptions_overview), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getDirectSubscribedPlatformGroupTableRenderer(): DirectSubscribedPlatformGroupTableRenderer
    {
        return $this->getService(DirectSubscribedPlatformGroupTableRenderer::class);
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function getPlatformGroupRelUserTableRenderer(): PlatformGroupRelUserTableRenderer
    {
        return $this->getService(PlatformGroupRelUserTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    protected function getSubSubscribedPlatformGroupTableRenderer(): SubSubscribedPlatformGroupTableRenderer
    {
        return $this->getService(SubSubscribedPlatformGroupTableRenderer::class);
    }

    protected function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     * @throws \TableException
     */
    private function get_all_users_tab(): string
    {
        $totalNumberOfItems = DataManager::count_all_course_users(
            $this->get_course_id(), $this->get_condition()
        );
        $allSubscribedUserTableRenderer = $this->getAllSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $allSubscribedUserTableRenderer->getParameterNames(),
            $allSubscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = DataManager::retrieve_all_course_users(
            $this->get_course_id(), $this->get_condition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $allSubscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $allSubscribedUserTableRenderer->legacyRender($this, $tableParameterValues, $users);
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

        if ($this->current_tab == self::TAB_USERS)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($this->get_course_id())
            );
        }
        elseif ($this->current_tab == self::TAB_PLATFORM_GROUPS_SUBGROUPS)
        {
            if ($this->get_group() == self::PLATFORM_GROUP_ROOT_ID)
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID
                    ), new StaticConditionVariable($this->get_course_id())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_TYPE
                    ), new StaticConditionVariable(CourseEntityRelation::ENTITY_TYPE_GROUP)
                );
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(
                        $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP)
                    )
                );
            }
        }
        elseif ($this->current_tab == self::TAB_PLATFORM_GROUPS_USERS)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                new StaticConditionVariable(
                    $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP)
                )
            );
        }
        if ($conditions)
        {
            return new AndCondition($conditions);
        }

        return null;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Exception
     */
    private function get_direct_users_tab(): string
    {
        $totalNumberOfItems = DataManager::count_users_directly_subscribed_to_course(
            $this->get_condition()
        );
        $subscribedUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedUserTableRenderer->getParameterNames(),
            $subscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = DataManager::retrieve_users_directly_subscribed_to_course(
            $this->get_condition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subscribedUserTableRenderer->legacyRender($this, $tableParameterValues, $users);
    }

    public function get_group()
    {
        return $this->getRequest()->query->get(
            \Chamilo\Application\Weblcms\Manager::PARAM_GROUP, self::PLATFORM_GROUP_ROOT_ID
        );
    }

    public function get_menu_tree(): ?SubscribedPlatformGroupMenuRenderer
    {
        $root_ids = $this->get_subscribed_platformgroup_ids($this->get_course_id());

        if (count($root_ids) > 0)
        {
            return new SubscribedPlatformGroupMenuRenderer($this, $root_ids, true);
        }

        return null;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    private function get_platformgroup_tabs(): string
    {
        $html = [];

        $tabs = new TabsCollection();
        $translator = $this->getTranslator();

        // no users tab if the root is selected
        if ($this->get_group() != self::PLATFORM_GROUP_ROOT_ID)
        {
            // users tab
            $link = $this->get_url([self::PARAM_TAB => self::TAB_PLATFORM_GROUPS_USERS]);
            $tab_name = $translator->trans('Users', [], StringUtilities::LIBRARIES);

            $tabs->add(
                new LinkTab(
                    (string) self::TAB_PLATFORM_GROUPS_USERS, $tab_name,
                    new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $link,
                    $this->current_tab == self::TAB_PLATFORM_GROUPS_USERS
                )
            );
        }
        elseif ($this->current_tab == self::TAB_PLATFORM_GROUPS_USERS)
        {
            $this->current_tab = (string) self::TAB_PLATFORM_GROUPS_SUBGROUPS;
        }

        // subgroups tab
        $link = $this->get_url([self::PARAM_TAB => self::TAB_PLATFORM_GROUPS_SUBGROUPS]);
        if ($this->get_group() != self::PLATFORM_GROUP_ROOT_ID)
        {
            $tab_name = $translator->trans('Subgroups', [], Manager::CONTEXT);
            $tab_selected = $this->current_tab == self::TAB_PLATFORM_GROUPS_SUBGROUPS;
        }
        else
        {
            $tab_name = $translator->trans('SubscribedGroups', [], Manager::CONTEXT);
            $tab_selected = true;
        }
        $tabs->add(
            new LinkTab(
                (string) self::TAB_PLATFORM_GROUPS_SUBGROUPS, $tab_name,
                new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas'), $link, $tab_selected
            )
        );

        $html[] = $this->getLinkTabsRenderer()->render($tabs, $this->get_platformgroup_tabs_content());

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \TableException
     */
    private function get_platformgroup_tabs_content(): string
    {
        switch ($this->current_tab)
        {
            case self::TAB_PLATFORM_GROUPS_SUBGROUPS :
                return $this->get_platformgroups_subgroups_tab();
            case self::TAB_PLATFORM_GROUPS_USERS :
                return $this->get_platformgroups_users_tab();
            default:
                return '';
        }
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    private function get_platformgroups_subgroups_tab(): string
    {
        if ($this->get_group() != self::PLATFORM_GROUP_ROOT_ID)
        {
            return $this->renderSubSubscribedPlatformGroupTable();
        }
        else
        {
            return $this->renderDirectSubscribedPlatformGroupTable();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    private function get_platformgroups_tab(): string
    {
        $menu_tree = $this->get_menu_tree();
        $html = [];
        if ($menu_tree)
        {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
            $html[] = $menu_tree->render_as_tree();
            $html[] = '</div>';
            $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';
            $html[] = $this->get_platformgroup_tabs();
            $html[] = '</div>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = $this->getTranslator()->trans('NoGroupsSubscribed', [], Manager::CONTEXT);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Exception
     */
    private function get_platformgroups_users_tab(): string
    {
        $totalNumberOfItems = \Chamilo\Libraries\Storage\DataManager\DataManager::count(
            GroupRelUser::class, new DataClassCountParameters($this->get_condition())
        );
        $platformGroupRelUserTableRenderer = $this->getPlatformGroupRelUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $platformGroupRelUserTableRenderer->getParameterNames(),
            $platformGroupRelUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $groupUserRelations = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieves(
            GroupRelUser::class, new DataClassRetrievesParameters(
                $this->get_condition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(),
                $platformGroupRelUserTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $platformGroupRelUserTableRenderer->legacyRender($this, $tableParameterValues, $groupUserRelations);
    }

    /**
     * @throws \QuickformException
     * @throws \Exception
     */
    public function get_search_condition(): ?OrCondition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions = [];
            switch ($this->current_tab)
            {
                case self::TAB_ALL :
                case self::TAB_USERS :
                    $conditions[] = $this->buttonToolbarRenderer->getConditions(
                        [
                            new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
                            new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
                            new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
                            new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME),
                            new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL)
                        ]
                    );
                    break;
                case self::TAB_PLATFORM_GROUPS_SUBGROUPS :
                    $conditions[] = new ContainsCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $query
                    );
                    $conditions[] = new ContainsCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_CODE), $query
                    );
                    $conditions[] = new ContainsCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION), $query
                    );
                    break;
                case self::TAB_PLATFORM_GROUPS_USERS :
                    return null;
            }

            return new OrCondition($conditions);
        }

        return null;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     */
    private function get_tabs(): string
    {
        $html = [];

        $html[] = $this->get_tabs_header();
        $html[] = $this->get_tabs_content();
        $html[] = $this->get_tabs_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Doctrine\DBAL\Exception
     * @throws \QuickformException
     */
    private function get_tabs_content(): string
    {
        switch ($this->current_tab)
        {
            case self::TAB_ALL :
                return $this->get_all_users_tab();
            case self::TAB_USERS :
                return $this->get_direct_users_tab();
            case self::TAB_PLATFORM_GROUPS_SUBGROUPS :
            case self::TAB_PLATFORM_GROUPS_USERS :
                return $this->get_platformgroups_tab();
            default:
                return '';
        }
    }

    private function get_tabs_footer(): string
    {
        return $this->getLinkTabsRenderer()->renderFooter();
    }

    private function get_tabs_header(): string
    {
        $html = [];

        $tabs = new TabsCollection();
        $translator = $this->getTranslator();

        // all tab
        $link = $this->get_url(
            [self::PARAM_TAB => self::TAB_ALL], [\Chamilo\Application\Weblcms\Manager::PARAM_GROUP]
        );
        $tab_name = $translator->trans('AllSubscriptions', [], Manager::CONTEXT);

        $tabs->add(
            new LinkTab(
                (string) self::TAB_ALL, $tab_name, new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $link,
                $this->current_tab == self::TAB_ALL
            )
        );

        // users tab
        $link = $this->get_url(
            [self::PARAM_TAB => self::TAB_USERS], [\Chamilo\Application\Weblcms\Manager::PARAM_GROUP]
        );
        $tab_name = $translator->trans('DirectSubscriptions', [], Manager::CONTEXT);

        $tabs->add(
            new LinkTab(
                (string) self::TAB_USERS, $tab_name, new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $link,
                $this->current_tab == self::TAB_USERS
            )
        );

        // groups tab
        $link = $this->get_url([self::PARAM_TAB => self::TAB_PLATFORM_GROUPS_SUBGROUPS]);
        $tab_name = $translator->trans('GroupSubscriptions', [], Manager::CONTEXT);
        $selected = $this->current_tab == self::TAB_PLATFORM_GROUPS_SUBGROUPS ||
            $this->current_tab == self::TAB_PLATFORM_GROUPS_USERS;

        $tabs->add(
            new LinkTab(
                (string) self::TAB_PLATFORM_GROUPS_SUBGROUPS, $tab_name,
                new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas'), $link, $selected
            )
        );

        $html[] = $this->getLinkTabsRenderer()->renderHeader($tabs);

        return implode(PHP_EOL, $html);
    }

    public function is_course_admin($user): bool
    {
        return $this->get_course()->is_course_admin($user);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    protected function renderDirectSubscribedPlatformGroupTable(): string
    {
        $totalNumberOfItems = DataManager::count_groups_directly_subscribed_to_course($this->get_condition());
        $directSubscribedPlatformGroupTableRenderer = $this->getDirectSubscribedPlatformGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $directSubscribedPlatformGroupTableRenderer->getParameterNames(),
            $directSubscribedPlatformGroupTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $groups = DataManager::retrieve_groups_directly_subscribed_to_course(
            $this->get_condition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $directSubscribedPlatformGroupTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $directSubscribedPlatformGroupTableRenderer->legacyRender($this, $tableParameterValues, $groups);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function renderSubSubscribedPlatformGroupTable(): string
    {
        $totalNumberOfItems = $this->getGroupService()->countGroups($this->get_condition());
        $subSubscribedPlatformGroupTableRenderer = $this->getSubSubscribedPlatformGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subSubscribedPlatformGroupTableRenderer->getParameterNames(),
            $subSubscribedPlatformGroupTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $groups = $this->getGroupService()->findGroups(
            $this->get_condition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subSubscribedPlatformGroupTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subSubscribedPlatformGroupTableRenderer->render($tableParameterValues, $groups);
    }
}
