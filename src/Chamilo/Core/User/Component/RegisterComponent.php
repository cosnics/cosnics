<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\RegisterForm;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 */
class RegisterComponent extends Manager implements NoAuthenticationSupportInterface
{
    protected bool $userCanRegister;

    protected ?UserPictureUpdateProviderInterface $userPictureUpdateProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, ?UserPictureUpdateProviderInterface $userPictureUpdateProvider,
        bool $userCanRegister
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->userPictureUpdateProvider = $userPictureUpdateProvider;
        $this->userCanRegister = $userCanRegister;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     */
    public function run(?User $currentUser = null): Response
    {
        $translator = $this->getTranslator();

        if (!$this->canUserRegister()) {
            throw new NotAllowedException();
        }

        $form = new RegisterForm(
            $this->getUrlGenerator()->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::REGISTER->value
                ]
            )
        );

        if ($form->validate()) {
            try {
                $formValues = $form->exportValues();

                $registeredUser = $this->getUserService()->registerUserFromParameters(
                    $formValues[User::PROPERTY_GIVEN_NAME], $formValues[User::PROPERTY_SURNAME],
                    $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                    $formValues[User::PROPERTY_EMAIL], (bool) $formValues[UserForm::PROPERTY_GENERATE_PASSWORD],
                    $formValues[User::PROPERTY_PASSWORD], 'Chamilo\Libraries\Authentication\Platform',
                    (bool) $formValues[UserForm::PROPERTY_SEND_MAIL]
                );

                $userPictureProvider = $this->getUserPictureProvider();

                if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                    $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                    if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                        if (!$userPictureProvider->updateUserPictureFromParameters(
                            $registeredUser, $currentUser, $pictureInformation
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
        $html[] = $form->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function canUserRegister(): bool
    {
        return $this->userCanRegister;
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        return $this->userPictureUpdateProvider;
    }
}
