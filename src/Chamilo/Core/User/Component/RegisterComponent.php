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
use Chamilo\Core\User\UserInterface\Form\RegisterFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
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
 */
class RegisterComponent extends Manager implements NoAuthenticationSupportInterface
{
    protected FormFactoryInterface $formFactory;

    protected RegisterFormType $registerFormType;

    protected Environment $twigEnvironment;

    protected bool $userCanRegister;

    protected ?UserPictureProviderInterface $userPictureUpdateProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, ?UserPictureUpdateProviderInterface $userPictureUpdateProvider,
        FormFactoryInterface $formFactory, Environment $twigEnvironment, RegisterFormType $registerFormType,
        bool $userCanRegister
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->userPictureUpdateProvider = $userPictureUpdateProvider;
        $this->userCanRegister = $userCanRegister;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->registerFormType = $registerFormType;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        $translator = $this->getTranslator();

        if (!$this->canUserRegister($currentUser)) {
            throw new NotAllowedException();
        }

        $registerUri = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::REGISTER->value
            ]
        );

        $form = $this->getFormFactory()->create(
            RegisterFormType::class, [User::PROPERTY_ACTIVE => true, AbstractUserFormType::PROPERTY_SEND_MAIL => true],
            ['action' => $registerUri, 'executingUser' => $currentUser]
        );

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $submittedData = $form->getData();

                $registeredUser = $this->getUserService()->registerUserFromParameters(
                    $submittedData[User::PROPERTY_GIVEN_NAME], $submittedData[User::PROPERTY_SURNAME],
                    $submittedData[User::PROPERTY_USERNAME], $submittedData[User::PROPERTY_OFFICIAL_CODE],
                    $submittedData[User::PROPERTY_EMAIL],
                    (bool) $submittedData[AbstractUserFormType::PROPERTY_PASSWORD_GENERATE],
                    $submittedData[User::PROPERTY_PASSWORD], PlatformAuthentication::class,
                    (bool) $submittedData[AbstractUserFormType::PROPERTY_SEND_MAIL]
                );

                $userPictureProvider = $this->getUserPictureProvider();

                if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                    $pictureInformation = $submittedData[User::PROPERTY_PICTURE_URI];

                    if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                        if (!$userPictureProvider->updateUserPictureFromParameters(
                            $registeredUser, $pictureInformation
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

                return new RedirectResponse($this->getUrlGenerator()->fromParameters());
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

    public function canUserRegister(?User $currentUser = null): bool
    {
        if ($currentUser instanceof User && $currentUser->isPlatformAdministrator()) {
            return true;
        }

        return $this->userCanRegister;
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    public function getRegisterFormType(): RegisterFormType
    {
        return $this->registerFormType;
    }

    public function getTwigEnvironment(): Environment
    {
        return $this->twigEnvironment;
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
