<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserForm;
use Chamilo\Core\User\Form\UserUpdateForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);

        if ($userIdentifier)
        {
            $user = $this->getUserService()->findUserByIdentifier($userIdentifier);
            $isLockoutRisk = $this->getUser()->getId() == $user->getId() && $user->isPlatformAdmin();

            $updateUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_UPDATE_USER,
                self::PARAM_USER_USER_ID => $userIdentifier
            ]);

            $form = new UserUpdateForm($user, $isLockoutRisk, $updateUrl);

            if ($form->validate())
            {
                try
                {
                    $formValues = $form->exportValues();

                    $this->getUserService()->updateUserFromParameters(
                        $user, $formValues[User::PROPERTY_FIRSTNAME], $formValues[User::PROPERTY_LASTNAME],
                        $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                        $formValues[User::PROPERTY_EMAIL], (bool) $formValues[UserForm::PROPERTY_GENERATE_PASSWORD],
                        $formValues[User::PROPERTY_PASSWORD], (bool) $formValues[User::PROPERTY_PLATFORMADMIN],
                        $formValues[User::PROPERTY_STATUS], (bool) $formValues[User::PROPERTY_ACTIVE], null,
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
                            $translator->trans('UserUpdated', [], Manager::CONTEXT), NotificationMessage::TYPE_SUCCESS
                        )
                    );

                    return new RedirectResponse(
                        $urlGenerator->fromParameters(
                            [
                                Application::PARAM_CONTEXT => Manager::CONTEXT,
                                Application::PARAM_ACTION => Manager::ACTION_BROWSE_USERS
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

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_USERS]),
                $this->getTranslator()->trans('AdminUserBrowserComponent', [], Manager::CONTEXT)
            )
        );
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService(UserPictureProviderInterface::class);
    }
}
