<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\PictureForm;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Exception;
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
class UpdateUserPictureComponent extends ProfileComponent
{
    protected PictureForm $pictureForm;

    protected ?UserPictureUpdateProviderInterface $userPictureUpdateProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, ?UserPictureUpdateProviderInterface $userPictureUpdateProvider,
        TabsRenderer $tabsRenderer, bool $userCanChangePicture
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator, $tabsRenderer,
            $userCanChangePicture
        );

        $this->userPictureUpdateProvider = $userPictureUpdateProvider;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');
        $translator = $this->getTranslator();
        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
            $pictureForm = $this->getPictureForm($currentUser);

            if ($pictureForm->validate()) {
                try {
                    $removeExistingPicture = (bool) $pictureForm->exportValue('remove_picture');
                }
                catch (Exception) {
                    $removeExistingPicture = false;
                }

                $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                $success = $userPictureProvider->updateUserPictureFromParameters(
                    $currentUser, $currentUser, $pictureInformation, $removeExistingPicture
                );

                if (!$success) {
                    if ($pictureInformation instanceof UploadedFile && !$pictureInformation->isValid()) {
                        $errorMessage = $pictureInformation->getErrorMessage();
                    }
                    else {
                        $errorMessage = 'UserProfileNotUpdated';
                    }
                }
                else {
                    $errorMessage = 'UserProfileNotUpdated';
                    $successMessage = 'UserProfileUpdated';
                }

                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $this->getTranslator()->trans($success ? $successMessage : $errorMessage),
                        !$success ? AlertEnum::DANGER : AlertEnum::SUCCESS
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE_USER_PICTURE->value
                ]));
            }
            else {
                return new Response($this->renderPage($currentUser));
            }
        }
        else {
            throw new UserException(
                $translator->trans(
                    'UserPictureProviderDoesNotSuportUpdates', [], Manager::CONTEXT
                )
            );
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(User $user): string
    {
        return $this->getPictureForm($user)->render();
    }

    /**
     * @throws \QuickformException
     */
    public function getPictureForm(User $user): PictureForm
    {
        if (!isset($this->pictureForm)) {
            $this->pictureForm = new PictureForm($user, $this->getUrlGenerator()->fromRequest());
        }

        return $this->pictureForm;
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        return $this->userPictureUpdateProvider;
    }
}
