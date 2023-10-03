<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserCreationForm;
use Chamilo\Core\User\Form\UserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreatorComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $currentUser = $this->getUser();
        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $form = new UserCreationForm($this->get_url());

        if ($form->validate())
        {
            try
            {
                $formValues = $form->exportValues();

                $user = $this->getUserService()->createUserFromParameters(
                    $formValues[User::PROPERTY_FIRSTNAME], $formValues[User::PROPERTY_LASTNAME],
                    $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                    $formValues[User::PROPERTY_EMAIL], (bool) $formValues[UserForm::PROPERTY_GENERATE_PASSWORD],
                    $formValues[User::PROPERTY_PASSWORD], 'Chamilo\Libraries\Authentication\Platform',
                    (bool) $formValues[User::PROPERTY_PLATFORMADMIN], $formValues[User::PROPERTY_STATUS],
                    (bool) $formValues[User::PROPERTY_ACTIVE], true,
                    (bool) $formValues[FormValidator::PROPERTY_TIME_PERIOD_FOREVER],
                    $formValues[User::PROPERTY_ACTIVATION_DATE], $formValues[User::PROPERTY_EXPIRATION_DATE],
                    $formValues[User::PROPERTY_DISK_QUOTA], (bool) $formValues[UserForm::PROPERTY_SEND_MAIL]
                );

                $userPictureProvider = $this->getUserPictureProvider();

                if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
                {
                    $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                    if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid())
                    {
                        if (!$userPictureProvider->updateUserPictureFromParameters(
                            $user, $this->getUser(), $pictureInformation
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

                $this->getNotificationMessageManager()->addMessage(
                    new NotificationMessage(
                        $translator->trans('UserCreated', [], Manager::CONTEXT), NotificationMessage::TYPE_SUCCESS
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters());
            }
            catch (Exception $exception)
            {
                $this->getNotificationMessageManager()->addMessage(
                    new NotificationMessage($exception->getMessage(), NotificationMessage::TYPE_DANGER)
                );
            }
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $form->render();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService(UserPictureProviderInterface::class);
    }
}
