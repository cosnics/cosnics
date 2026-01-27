<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\UserCreationForm;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreateComponent extends Manager
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        $currentUser = $this->getUser();
        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $form = new UserCreationForm($this->getUrlGenerator()->fromRequest());

        if ($form->validate())
        {
            try
            {
                $formValues = $form->exportValues();

                $user = $this->getUserService()->createUserFromParameters(
                    $formValues[User::PROPERTY_GIVEN_NAME], $formValues[User::PROPERTY_SURNAME],
                    $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                    $formValues[User::PROPERTY_EMAIL], (bool) $formValues[UserForm::PROPERTY_GENERATE_PASSWORD],
                    $formValues[User::PROPERTY_PASSWORD], 'Chamilo\Libraries\Authentication\Platform',
                    (bool) $formValues[User::PROPERTY_PLATFORM_ADMINISTRATOR], $formValues[User::PROPERTY_STATUS],
                    (bool) $formValues[User::PROPERTY_ACTIVE], (bool) $formValues[UserForm::PROPERTY_SEND_MAIL]
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

                return new RedirectResponse(
                    $this->getUrlGenerator()->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Application::PARAM_ACTION => Manager::ACTION_BROWSE
                        ]
                    )
                );
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

        return new Response(implode(PHP_EOL, $html));
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        $service = $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');

        return $service instanceof UserPictureUpdateProviderInterface ? $service : null;
    }
}
