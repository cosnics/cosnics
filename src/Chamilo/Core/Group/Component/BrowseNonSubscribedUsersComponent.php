<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Table\NonSubscribedUserTableRenderer;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseNonSubscribedUsersComponent extends Manager
{
    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    protected NonSubscribedUserTableRenderer $nonSubscribedUserTableRenderer;

    protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler;

    private ?Group $group;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        GroupMembershipService $groupMembershipService, GroupUrlGenerator $groupUrlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupService $groupService,
        UserService $userService, ButtonToolBarRenderer $buttonToolBarRenderer, UrlGenerator $urlGenerator,
        NonSubscribedUserTableRenderer $nonSubscribedUserTableRenderer,
        RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $groupMembershipService,
            $groupUrlGenerator, $alertsManager, $breadcrumbTrail, $groupService, $userService, $urlGenerator
        );

        $this->buttonToolBarRenderer = $buttonToolBarRenderer;
        $this->nonSubscribedUserTableRenderer = $nonSubscribedUserTableRenderer;
        $this->requestTableParameterValuesCompiler = $requestTableParameterValuesCompiler;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb($this->getTranslator()->trans('ViewerComponent', [], Manager::CONTEXT),
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::BROWSE->value,
                        DataClass::PROPERTY_ID => $this->getGroupIdentifier()
                    ]
                ))
        );

        $output = $this->renderNonSubscribedUserTable();

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar());
        $html[] = $output;
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getButtonToolBar(): ButtonToolBar
    {
        $group = $this->getGroup();

        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::BROWSE_NON_SUBSCRIBED_USERS->value,
                    DataClass::PROPERTY_ID => $group->getId()
                ]
            )
        );
        $commonActions = new ButtonGroup();

        $commonActions->addButton(
            new Button(
                $this->getTranslator()->trans('ShowAll', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('folder'), $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::BROWSE_NON_SUBSCRIBED_USERS->value,
                    DataClass::PROPERTY_ID => $group->getId()
                ]
            ), DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $buttonToolBar->addButton($commonActions);

        return $buttonToolBar;
    }

    public function getButtonToolBarRenderer(): ButtonToolBarRenderer
    {
        return $this->buttonToolBarRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getGroup(): Group
    {
        if (!isset($this->group)) {
            $this->group = $this->getGroupService()->findGroupByIdentifier($this->getGroupIdentifier());
        }

        return $this->group;
    }

    protected function getGroupIdentifier(): string
    {
        return $this->getRequest()->query->get(DataClass::PROPERTY_ID);
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getNonSubscribedUserCondition(): AndCondition
    {
        $conditions = [];

        $userIdentifiers = $this->getGroupMembershipService()->findSubscribedUserIdentifiersForGroupIdentifier(
            $this->getGroupIdentifier()
        );

        $conditions[] = new NotCondition(
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers)
        );

        $query = $this->getButtonToolBarRenderer()->getSearchForm()->getQuery();

        if (isset($query) && $query != '') {
            $orConditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME), $query
            );
            $orConditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME), $query
            );
            $orConditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
            );
            $conditions[] = new OrCondition($orConditions);
        }

        return new AndCondition($conditions);
    }

    public function getNonSubscribedUserTableRenderer(): NonSubscribedUserTableRenderer
    {
        return $this->nonSubscribedUserTableRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->requestTableParameterValuesCompiler;
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function renderNonSubscribedUserTable(): string
    {
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getNonSubscribedUserCondition());
        $nonSubscribedUserTableRenderer = $this->getNonSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $nonSubscribedUserTableRenderer->getParameterNames(),
            $nonSubscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getNonSubscribedUserCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $nonSubscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $nonSubscribedUserTableRenderer->render($tableParameterValues, $users);
    }
}