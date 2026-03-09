<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\UserForm;
use Chamilo\Core\User\UserInterface\Form\UserUpdateForm;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdateComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
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
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::UPDATE->value,
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
                                Application::PARAM_ACTION => ActionEnum::BROWSE->value
                            ]
                        )
                    );
                }
                catch (Exception $exception) {
                    $this->getNotificationMessageManager()->addMessage(
                        new NotificationMessage($exception->getMessage(), NotificationMessage::TYPE_DANGER)
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
            return new Response(
                $this->displayErrorPage(
                    htmlentities(
                        $translator->trans(
                            'NoObjectSelected', ['%Object%' => $translator->trans('User', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                )
            );
        }
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        $service = $this->getService(UserPictureProviderInterface::class);

        return $service instanceof UserPictureUpdateProviderInterface ? $service : null;
    }
}
