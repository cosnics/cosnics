<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\AccountForm;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountComponent extends ProfileComponent
{
    protected ?UserPictureProviderInterface $userPictureProvider;

    private AccountForm $accountForm;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, ?UserPictureProviderInterface $userPictureProvider, TabsRenderer $tabsRenderer,
        bool $userCanChangePicture
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator, $tabsRenderer,
            $userCanChangePicture
        );

        $this->userPictureProvider = $userPictureProvider;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');
        $translator = $this->getTranslator();

        $accountForm = $this->getAccountForm($currentUser);

        if ($accountForm->validate()) {
            $formValues = $accountForm->exportValues();

            $success = $this->getUserService()->updateAccountFromParameters(
                $currentUser, $formValues[User::PROPERTY_GIVEN_NAME], $formValues[User::PROPERTY_SURNAME],
                $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                $formValues[User::PROPERTY_EMAIL], $formValues[UserForm::PROPERTY_CURRENT_PASSWORD],
                $formValues[User::PROPERTY_PASSWORD]
            );

            $userPictureProvider = $this->getUserPictureProvider();

            if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                    if (!$userPictureProvider->updateUserPictureFromParameters(
                        $currentUser, $currentUser, $pictureInformation
                    )) {
                        $this->getAlertsManager()->addAlert(
                            new Alert(
                                $translator->trans('UserPictureNotUpdated', [], Manager::CONTEXT), AlertEnum::WARNING
                            )
                        );
                    }
                }
            }

            $message = !$success ? 'UserProfileNotUpdated' : 'UserProfileUpdated';

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $translator->trans($message, [], Manager::CONTEXT),
                    $success ? AlertEnum::SUCCESS : AlertEnum::DANGER
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::ACCOUNT->value
            ]));
        }
        else {
            return new Response($this->renderPage($currentUser));
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getAccountForm(User $user): AccountForm
    {
        if (!isset($this->accountForm)) {
            $this->accountForm = new AccountForm(
                $user, $this->getUrlGenerator()->fromRequest(), $this->getAuthenticationValidator()
            );
        }

        return $this->accountForm;
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(User $user): string
    {
        return $this->getAccountForm($user)->render();
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        return $this->userPictureProvider;
    }
}
