<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Table\UserTableRenderer;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Form\ButtonSearchForm;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseComponent extends Manager
{
    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler;

    protected UserTableRenderer $userTableRenderer;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        ButtonToolBarRenderer $buttonToolBarRenderer, UrlGenerator $urlGenerator,
        RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler, UserTableRenderer $userTableRenderer
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->buttonToolBarRenderer = $buttonToolBarRenderer;
        $this->requestTableParameterValuesCompiler = $requestTableParameterValuesCompiler;
        $this->userTableRenderer = $userTableRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar($currentUser));
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getAdminUserTableRenderer(): UserTableRenderer
    {
        return $this->userTableRenderer;
    }

    public function getButtonToolBar(User $user): ButtonToolBar
    {
        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::BROWSE->value]
            )
        );

        $commonActions = new ButtonGroup();
        $translator = $this->getTranslator();

        if ($user->isPlatformAdministrator()) {
            $commonActions->addButton(
                new Button(
                    $translator->trans('Add', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $this->getUrlGenerator()->fromParameters(
                        [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::CREATE->value]
                    ), DisplayTypeEnum::ICON_AND_LABEL
                )
            );
        }

        $buttonToolBar->addButton($commonActions);

        return $buttonToolBar;
    }

    public function getButtonToolBarRenderer(): ButtonToolBarRenderer
    {
        return $this->buttonToolBarRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->requestTableParameterValuesCompiler;
    }

    public function getUserTableCondition(): ?ConditionInterface
    {
        $searchProperties = [];
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        return $this->getButtonToolBarRenderer()->getConditions($searchProperties);
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function renderTable(): string
    {
        $this->getRequest()->query->set(
            ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, $this->getButtonToolBarRenderer()->getSearchForm()->getQuery()
        );

        $totalNumberOfItems = $this->getUserService()->countUsers($this->getUserTableCondition());
        $adminUserTableRenderer = $this->getAdminUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $adminUserTableRenderer->getParameterNames(), $adminUserTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getUserTableCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $adminUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $adminUserTableRenderer->render($tableParameterValues, $users);
    }
}
