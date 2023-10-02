<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\RegisterForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Exception;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\User\Component
 */
class RegisterComponent extends Manager implements NoAuthenticationSupportInterface
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $allowRegistration = $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_registration']);

        if (!$allowRegistration == 0)
        {
            throw new NotAllowedException();
        }

        $form = new RegisterForm(
            $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_REGISTER_USER
                ]
            )
        );

        if ($form->validate())
        {
            try
            {
                $formValues = $form->exportValues();

                $user = $this->getUserService()->registerUserFromParameters(
                    $formValues[User::PROPERTY_FIRSTNAME], $formValues[User::PROPERTY_LASTNAME],
                    $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                    $formValues[User::PROPERTY_EMAIL], $formValues['pw']['pass'] == '1',
                    $formValues['pw'][User::PROPERTY_PASSWORD], 'Platform', $formValues[User::PROPERTY_STATUS],
                    (bool) $formValues['send_mail']
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

                if ($allowRegistration == 2)
                {
                    $this->getNotificationMessageManager()->addMessage(
                        new NotificationMessage(
                            $translator->trans('UserAwaitingApproval', [], Manager::CONTEXT),
                            NotificationMessage::TYPE_SUCCESS
                        )
                    );
                }

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

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->getService(PasswordGeneratorInterface::class);
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService(UserPictureProviderInterface::class);
    }
}
