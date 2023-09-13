<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
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
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class ViewerComponent extends Manager
{

    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    protected ?Group $currentGroup;

    protected ?string $currentGroupIdentifier;

    protected ?Group $rootGroup;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $group = $this->getCurrentGroup();

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();

        $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';

        // Details
        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('info-circle', ['fa-lg'], null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h3 class="panel-title">' . $glyph->render() . ' ' . $translator->trans('Details', [], Manager::CONTEXT) .
            '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = '<b>' . $translator->trans('Code', [], Manager::CONTEXT) . '</b>: ' . $group->get_code();
        $html[] = '<br /><b>' . $translator->trans('Description', [], StringUtilities::LIBRARIES) . '</b>: ' .
            $group->get_description();
        $html[] = '</div>';

        $html[] = '</div>';

        // Users
        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $glyph->render() . ' ' .
            $translator->trans('Users', [], \Chamilo\Core\User\Manager::CONTEXT) . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $html[] = $this->renderTable();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $this->getTranslator()->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        $currentGroup = $this->getCurrentGroup();
        $rootGroup = $this->getRootGroup();
        $translator = $this->getTranslator();

        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url([self::PARAM_GROUP_ID => $currentGroup->getId()]));
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url([self::PARAM_GROUP_ID => $currentGroup->getId()]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_group_editing_url($currentGroup), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            if ($currentGroup->getId() != $rootGroup->getId())
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_group_delete_url($currentGroup), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $toolActions->addButton(
                new Button(
                    $translator->trans('AddUsers', [], \Chamilo\Core\User\Manager::CONTEXT),
                    new FontAwesomeGlyph('plus-circle'), $this->get_group_suscribe_user_browser_url($currentGroup),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $userCount =
                $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier($currentGroup->getId());

            if ($userCount > 0)
            {
                $toolActions->addButton(
                    new Button(
                        $translator->trans('Truncate', [], Manager::CONTEXT), new FontAwesomeGlyph('trash-alt'),
                        $this->get_group_emptying_url($currentGroup), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
            else
            {
                $toolActions->addButton(
                    new Button(
                        $translator->trans('TruncateNA', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('trash-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $toolActions->addButton(
                new Button(
                    $translator->trans('Metadata', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('info-circle'),
                    $this->get_group_metadata_url($currentGroup), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group
     */
    public function getCurrentGroup(): Group
    {
        if (!$this->currentGroup)
        {
            $this->currentGroup = $this->getGroupService()->findGroupByIdentifier($this->getCurrentGroupIdentifier());
        }

        return $this->currentGroup;
    }

    public function getCurrentGroupIdentifier(): string
    {
        if (!$this->currentGroupIdentifier)
        {
            $this->currentGroupIdentifier =
                $this->getRequest()->query->get(self::PARAM_GROUP_ID, $this->getRootGroup()->getId());
        }

        return $this->currentGroupIdentifier;
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
            $this->rootGroup = $this->getGroupService()->findRootGroup();
        }

        return $this->rootGroup;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function getSubscribedUsersCondition(): AndCondition
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->getRequest()->query->get(self::PARAM_GROUP_ID))
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

            $users = $this->getUserService()->findUsers($condition);
            $userconditions = [];

            foreach ($users as $user)
            {
                $userconditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable($user->getId())
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

        return new AndCondition($conditions);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier(
            $this->getCurrentGroupIdentifier(), $this->getSubscribedUsersCondition()
        );
        $subscribedUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $subscribedUserTableRenderer->getParameterNames(),
            $subscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getGroupMembershipService()->findSubscribedUsersForGroupIdentifier(
            $this->getCurrentGroupIdentifier(), $this->getSubscribedUsersCondition(),
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $subscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $subscribedUserTableRenderer->render($tableParameterValues, $users);
    }

}
