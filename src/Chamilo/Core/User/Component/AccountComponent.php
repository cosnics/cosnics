<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\AccountForm;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountComponent extends ProfileComponent
{
    private AccountForm $accountForm;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
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
                        $this->getNotificationMessageManager()->addMessage(
                            new NotificationMessage(
                                $translator->trans('UserPictureNotUpdated', [], Manager::CONTEXT),
                                NotificationMessage::TYPE_WARNING
                            )
                        );
                    }
                }
            }

            $message = !$success ? 'UserProfileNotUpdated' : 'UserProfileUpdated';

            return $this->redirectWithMessage(
                $translator->trans($message, [], Manager::CONTEXT), !$success, [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => ActionEnum::ACCOUNT->value
                ]
            );
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
        $service = $this->getService('Chamilo\Core\User\Service\UserPictureProvider');

        return $service instanceof UserPictureUpdateProviderInterface ? $service : null;
    }
}
