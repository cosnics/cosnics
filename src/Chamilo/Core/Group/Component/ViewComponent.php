<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\UserInterface\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewComponent extends Manager
{
    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    protected ?Group $currentGroup;

    protected ?string $currentGroupIdentifier;

    protected ?Group $rootGroup;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function run(): Response
    {
        $translator = $this->getTranslator();
        $group = $this->getCurrentGroup();

        if (!$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();

        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar()) . '<br />';

        // Details
        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('info-circle', ['fa-lg'], null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h3 class="panel-title">' . $glyph->render() . ' ' . $translator->trans('Details', [], Manager::CONTEXT) .
            '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = '<b>' . $translator->trans('Code', [], Manager::CONTEXT) . '</b>: ' . $group->getCode();
        $html[] = '<br /><b>' . $translator->trans('Description', [], StringUtilities::LIBRARIES) . '</b>: ' .
            $group->getDescription();
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

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getButtonToolBar(): ButtonToolBar
    {
        $currentGroup = $this->getCurrentGroup();
        $rootGroup = $this->getRootGroup();
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => self::ACTION_VIEW,
                    self::PARAM_GROUP_ID => $currentGroup->getId()
                ]
            )
        );
        $commonActions = new ButtonGroup();
        $toolActions = new ButtonGroup();

        $commonActions->addButton(
            new Button(
                $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => self::ACTION_VIEW,
                        self::PARAM_GROUP_ID => $currentGroup->getId()
                    ]
                ), ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
            )
        );

        $commonActions->addButton(
            new Button(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getGroupUrlGenerator()->getUpdateUrl($currentGroup),
                ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
            )
        );

        if ($currentGroup->getId() != $rootGroup->getId()) {
            $commonActions->addButton(
                new Button(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->getGroupUrlGenerator()->getDeleteUrl($currentGroup),
                    ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $toolActions->addButton(
            new Button(
                $translator->trans('AddUsers', [], \Chamilo\Core\User\Manager::CONTEXT),
                new FontAwesomeGlyph('plus-circle'), $this->getGroupUrlGenerator()->getSubscribeUrl($currentGroup),
                ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
            )
        );

        $userCount = $this->getGroupMembershipService()->countSubscribedUsersForGroupIdentifier($currentGroup->getId());

        if ($userCount > 0) {
            $toolActions->addButton(
                new Button(
                    $translator->trans('Truncate', [], Manager::CONTEXT), new FontAwesomeGlyph('trash-alt'),
                    $this->getGroupUrlGenerator()->getTruncateUrl($currentGroup),
                    ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        else {
            $toolActions->addButton(
                new Button(
                    $translator->trans('TruncateNA', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('trash-alt', ['text-muted']), null,
                    ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $buttonToolBar->addButton($commonActions);
        $buttonToolBar->addButton($toolActions);

        return $buttonToolBar;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getCurrentGroup(): Group
    {
        if (!$this->currentGroup) {
            $this->currentGroup = $this->getGroupService()->findGroupByIdentifier($this->getCurrentGroupIdentifier());
        }

        return $this->currentGroup;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getCurrentGroupIdentifier(): string
    {
        if (!$this->currentGroupIdentifier) {
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getRootGroup(): Group
    {
        if (!$this->rootGroup) {
            $this->rootGroup = $this->getGroupService()->findRootGroup();
        }

        return $this->rootGroup;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getSubscribedUsersCondition(): AndCondition
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->getRequest()->query->get(self::PARAM_GROUP_ID))
        );

        $query = $this->buttonToolBarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '') {
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
            );
            $condition = new OrCondition($or_conditions);

            $users = $this->getUserService()->findUsers($condition);
            $userconditions = [];

            foreach ($users as $user) {
                $userconditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable($user->getId())
                );
            }

            if (count($userconditions)) {
                $conditions[] = new OrCondition($userconditions);
            }
            else {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
                    new StaticConditionVariable(0)
                );
            }
        }

        return new AndCondition($conditions);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
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
