<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ViewerComponent extends Manager
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $group;

    private $groupIdentifier;

    private $rootGroup;

    private $root_group;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request::get(self::PARAM_GROUP_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $id);
        if ($id)
        {
            $this->group = $this->retrieve_group($id);
            $this->root_group = $this->retrieve_groups(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )->current();

            $group = $this->group;

            if (!$this->get_user()->is_platform_admin())
            {
                throw new NotAllowedException();
            }

            $html = [];

            $html[] = $this->render_header();
            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

            $html[] = $this->buttonToolbarRenderer->render() . '<br />';

            // Details
            $html[] = '<div class="panel panel-default">';

            $glyph = new FontAwesomeGlyph('info-circle', ['fa-lg'], null, 'fas');

            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . $glyph->render() . ' ' . Translation::get('Details') . '</h3>';
            $html[] = '</div>';

            $html[] = '<div class="panel-body">';
            $html[] = '<b>' . Translation::get('Code') . '</b>: ' . $group->get_code();
            $html[] = '<br /><b>' . Translation::get('Description', null, StringUtilities::LIBRARIES) . '</b>: ' .
                $group->get_description();
            $html[] = '</div>';

            $html[] = '</div>';

            // Users
            $html[] = '<div class="panel panel-default">';

            $glyph = new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas');

            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . $glyph->render() . ' ' .
                Translation::get('Users', null, \Chamilo\Core\User\Manager::CONTEXT) . '</h3>';
            $html[] = '</div>';

            $html[] = '<div class="panel-body">';

            $table = new GroupRelUserTable($this);
            $html[] = $this->renderTable();
            $html[] = '</div>';
            $html[] = '</div>';

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectSelected', null, StringUtilities::LIBRARIES))
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                Translation::get('BrowserComponent')
            )
        );
    }

    public function getButtonToolbarRenderer()
    {
        $group = $this->group;
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url([self::PARAM_GROUP_ID => $group->get_id()]));
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url([self::PARAM_GROUP_ID => $group->get_id()]), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_group_editing_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            if ($this->group != $this->root_group)
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_group_delete_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $toolActions->addButton(
                new Button(
                    Translation::get('AddUsers', null, \Chamilo\Core\User\Manager::CONTEXT),
                    new FontAwesomeGlyph('plus-circle'), $this->get_group_suscribe_user_browser_url($group),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $condition = new EqualityCondition(
                new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
                new StaticConditionVariable($group->get_id())
            );
            $users = $this->retrieve_group_rel_users($condition);
            $visible = ($users->count() > 0);

            if ($visible)
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('Truncate'), new FontAwesomeGlyph('trash-alt'),
                        $this->get_group_emptying_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
            else
            {
                $toolActions->addButton(
                    new Button(
                        Translation::get('TruncateNA'), new FontAwesomeGlyph('trash-alt', ['text-muted']), null,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $toolActions->addButton(
                new Button(
                    Translation::get('Metadata', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('info-circle'),
                    $this->get_group_metadata_url($group), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getGroupIdentifier(): int
    {
        if (!$this->groupIdentifier)
        {
            $this->groupIdentifier =
                $this->getRequest()->query->get(self::PARAM_GROUP_ID, $this->getRootGroup()->getId());
        }

        return $this->groupIdentifier;
    }

    protected function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function getRootGroup(): Group
    {
        if (!$this->rootGroup)
        {
            $this->rootGroup = $this->retrieve_groups(
                new EqualityCondition(
                    new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
                    new StaticConditionVariable(0)
                )
            )->current();
        }

        return $this->rootGroup;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    public function getSubscribedUsersCondition()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable(Request::get(self::PARAM_GROUP_ID))
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
            );
            $condition = new OrCondition($or_conditions);

            $users = DataManager::retrieves(
                User::class, new DataClassRetrievesParameters($condition)
            );

            foreach ($users as $user)
            {
                $userconditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable($user->get_id())
                );
            }

            if (count($userconditions))
            {
                $conditions[] = new OrCondition($userconditions);
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable(0)
                );
            }
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $this->getSubscribedUsersCondition()
        );
        $subscribedUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedUserTableRenderer->getParameterNames(),
            $subscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getGroupMembershipService()->findSubscribedUsersForGroupIdentifier(
            $this->getGroupIdentifier(), $this->getSubscribedUsersCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subscribedUserTableRenderer->render($tableParameterValues, $users);
    }

}
