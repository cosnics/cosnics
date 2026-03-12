<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Core\User\UserInterface\Form\UserUpdateForm;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
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
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdateComponent extends Manager
{
    protected ?UserPictureUpdateProviderInterface $userPictureUpdateProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, ?UserPictureUpdateProviderInterface $userPictureUpdateProvider
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->userPictureUpdateProvider = $userPictureUpdateProvider;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);

        if ($userIdentifier) {
            $userToUpdate = $this->getUserService()->findUserByIdentifier($userIdentifier);
            $isLockoutRisk =
                $currentUser->getId() == $userToUpdate->getId() && $userToUpdate->isPlatformAdministrator();

            $updateUrl = $urlGenerator->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE->value,
                self::PARAM_USER_ID => $userIdentifier
            ]);

            $form = new UserUpdateForm($userToUpdate, $isLockoutRisk, $updateUrl);

            if ($form->validate()) {
                try {
                    $formValues = $form->exportValues();

                    $this->getUserService()->updateUserFromParameters(
                        $userToUpdate, $formValues[User::PROPERTY_GIVEN_NAME], $formValues[User::PROPERTY_SURNAME],
                        $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                        $formValues[User::PROPERTY_EMAIL], (bool) $formValues[UserForm::PROPERTY_GENERATE_PASSWORD],
                        $formValues[User::PROPERTY_PASSWORD], (bool) $formValues[User::PROPERTY_PLATFORM_ADMINISTRATOR],
                        (bool) $formValues[User::PROPERTY_ACTIVE], (bool) $formValues[UserForm::PROPERTY_SEND_MAIL]
                    );

                    $userPictureProvider = $this->getUserPictureProvider();

                    if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                        $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                        if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                            if (!$userPictureProvider->updateUserPictureFromParameters(
                                $userToUpdate, $currentUser, $pictureInformation
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
                            $translator->trans('UserUpdated', [], Manager::CONTEXT), AlertEnum::SUCCESS
                        )
                    );

                    return new RedirectResponse(
                        $urlGenerator->fromParameters(
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
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else {
            throw new NoSuchParameterException(self::PARAM_USER_ID);
        }
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        return $this->userPictureUpdateProvider;
    }
}
