<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\PictureForm;
use Chamilo\Libraries\Architecture\Domain\Application;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateUserPictureComponent extends ProfileComponent
{
    protected PictureForm $pictureForm;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');
        $translator = $this->getTranslator();
        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
            $pictureForm = $this->getPictureForm();

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

                return $this->redirectWithMessage(
                    $this->getTranslator()->trans($success ? $successMessage : $errorMessage), !$success, [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => self::ACTION_UPDATE_USER_PICTURE
                    ]
                );
            }
            else {
                return new Response($this->renderPage($currentUser));
            }
        }
        else {
            return new Response(
                $this->displayErrorPage(
                    $translator->trans('UserPictureProviderDoesNotSuportUpdates', [], Manager::CONTEXT)
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
        $service = $this->getService('Chamilo\Core\User\Service\UserPictureProvider');

        return $service instanceof UserPictureUpdateProviderInterface ? $service : null;
    }
}
