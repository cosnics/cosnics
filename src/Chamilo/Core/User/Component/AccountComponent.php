<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\AccountForm;
use Chamilo\Core\User\Form\UserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageAccount');
        $translator = $this->getTranslator();

        $accountForm = $this->getAccountForm();

        if ($accountForm->validate())
        {
            $formValues = $accountForm->exportValues();

            $success = $this->getUserService()->updateAccountFromParameters(
                $this->getUser(), $formValues[User::PROPERTY_FIRSTNAME], $formValues[User::PROPERTY_LASTNAME],
                $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                $formValues[User::PROPERTY_EMAIL], $formValues[UserForm::PROPERTY_CURRENT_PASSWORD],
                $formValues[User::PROPERTY_PASSWORD]
            );

            $userPictureProvider = $this->getUserPictureProvider();

            if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
            {
                $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid())
                {
                    if (!$userPictureProvider->updateUserPictureFromParameters(
                        $this->getUser(), $this->getUser(), $pictureInformation
                    ))
                    {
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

            $this->redirectWithMessage(
                $translator->trans($message, [], Manager::CONTEXT), !$success,
                [Application::PARAM_ACTION => self::ACTION_VIEW_ACCOUNT]
            );
        }
        else
        {
            return $this->renderPage();
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getAccountForm(): AccountForm
    {
        if (!isset($this->accountForm))
        {
            $this->accountForm = new AccountForm(
                $this->getUser(), $this->get_url(), $this->getAuthenticationValidator()
            );
        }

        return $this->accountForm;
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        return $this->getAccountForm()->render();
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService(UserPictureProviderInterface::class);
    }
}
