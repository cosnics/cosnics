<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\AbstractUserFormType;
use Chamilo\Core\User\UserInterface\Form\UserFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Authentication\Service\PlatformAuthentication;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreateComponent extends Manager
{
    protected FormFactoryInterface $formFactory;

    protected Environment $twigEnvironment;

    protected UserFormType $userFormType;

    protected ?UserPictureProviderInterface $userPictureUpdateProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, ?UserPictureProviderInterface $userPictureUpdateProvider,
        FormFactoryInterface $formFactory, Environment $twigEnvironment, UserFormType $userFormType
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->userPictureUpdateProvider = $userPictureUpdateProvider;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->userFormType = $userFormType;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $form = $this->getFormFactory()->create(
            UserFormType::class,
            [User::PROPERTY_ACTIVE => true, AbstractUserFormType::PROPERTY_PASSWORD_GENERATE => true],
            ['action' => $this->getUrlGenerator()->fromRequest(), 'executingUser' => $currentUser]
        );

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $submittedData = $form->getData();

                $createdUser = $this->getUserService()->createUserFromParameters(
                    $submittedData[User::PROPERTY_GIVEN_NAME], $submittedData[User::PROPERTY_SURNAME],
                    $submittedData[User::PROPERTY_USERNAME], $submittedData[User::PROPERTY_OFFICIAL_CODE],
                    $submittedData[User::PROPERTY_EMAIL],
                    (bool) $submittedData[AbstractUserFormType::PROPERTY_PASSWORD_GENERATE],
                    $submittedData[User::PROPERTY_PASSWORD], PlatformAuthentication::class,
                    (bool) $submittedData[User::PROPERTY_PLATFORM_ADMINISTRATOR],
                    (bool) $submittedData[User::PROPERTY_ACTIVE],
                    (bool) $submittedData[AbstractUserFormType::PROPERTY_SEND_MAIL]
                );

                $userPictureProvider = $this->getUserPictureProvider();

                if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                    $pictureInformation = $submittedData[User::PROPERTY_PICTURE_URI];

                    if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                        if (!$userPictureProvider->updateUserPictureFromParameters(
                            $createdUser, $pictureInformation
                        )) {
                            $this->getAlertsManager()->addAlert(
                                new Alert(
                                    $translator->trans('UserPictureNotUpdated', [], Manager::CONTEXT),
                                    AlertEnum::WARNING
                                )
                            );
                        }
                    }
                }

                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans('UserCreated', [], Manager::CONTEXT), AlertEnum::SUCCESS
                    )
                );

                return new RedirectResponse(
                    $this->getUrlGenerator()->fromParameters(
                        [
                            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
                        ]
                    )
                );
            }
            catch (Exception $exception) {
                $this->getAlertsManager()->addAlert(
                    new Alert($exception->getMessage(), AlertEnum::DANGER)
                );
            }
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getTwigEnvironment()->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    public function getTwigEnvironment(): Environment
    {
        return $this->twigEnvironment;
    }

    public function getUserFormType(): UserFormType
    {
        return $this->userFormType;
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        return $this->userPictureUpdateProvider;
    }

    public function getUserPictureUpdateProvider(): ?UserPictureProviderInterface
    {
        return $this->userPictureUpdateProvider;
    }
}
