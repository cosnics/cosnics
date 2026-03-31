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
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonToolBarSearchFormTrait;
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
    use ButtonToolBarSearchFormTrait;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly ButtonToolBarRenderer $buttonToolBarRenderer,
        protected readonly RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler,
        protected readonly UserTableRenderer $userTableRenderer
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
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
        $html[] = $this->buttonToolBarRenderer->render($this->getButtonToolBar($currentUser));
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
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

    public function getButtonToolBarSearchProperties(?string $type = null): array
    {
        $searchProperties = [];
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);
        $searchProperties[] = new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL);

        return $searchProperties;
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
        $searchCondition = $this->getButtonToolBarSearchCondition();

        $totalNumberOfItems = $this->userService->countUsers($searchCondition);

        $tableParameterValues = $this->requestTableParameterValuesCompiler->determineParameterValues(
            $this->userTableRenderer->getParameterNames(), $this->userTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = $this->userService->findUsers(
            $searchCondition, $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $this->userTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $this->userTableRenderer->render($tableParameterValues, $users);
    }
}
