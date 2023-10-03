<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\PictureForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PictureComponent extends ProfileComponent
{

    protected PictureForm $pictureForm;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageAccount');
        $translator = $this->getTranslator();
        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
        {
            $pictureForm = $this->getPictureForm();

            if ($pictureForm->validate())
            {
                try
                {
                    $removeExistingPicture = (bool) $pictureForm->exportValue('remove_picture');
                }
                catch (Exception)
                {
                    $removeExistingPicture = false;
                }

                $pictureInformation = $this->getRequest()->files->get(User::PROPERTY_PICTURE_URI);

                $success = $userPictureProvider->updateUserPictureFromParameters(
                    $this->getUser(), $this->getUser(), $pictureInformation, $removeExistingPicture
                );

                if (!$success)
                {
                    if ($pictureInformation instanceof UploadedFile && !$pictureInformation->isValid())
                    {
                        $errorMessage = $pictureInformation->getErrorMessage();
                    }
                    else
                    {
                        $errorMessage = 'UserProfileNotUpdated';
                    }
                }
                else
                {
                    $errorMessage = 'UserProfileNotUpdated';
                    $successMessage = 'UserProfileUpdated';
                }

                $this->redirectWithMessage(
                    $this->getTranslator()->trans($success ? $successMessage : $errorMessage), !$success,
                    [Application::PARAM_ACTION => self::ACTION_CHANGE_PICTURE]
                );
            }
            else
            {
                return $this->renderPage();
            }
        }
        else
        {
            return $this->display_error_page(
                $translator->trans('UserPictureProviderDoesNotSuportUpdates', [], Manager::CONTEXT)
            );
        }
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        return $this->getPictureForm()->render();
    }

    /**
     * @throws \QuickformException
     */
    public function getPictureForm(): PictureForm
    {
        if (!isset($this->pictureForm))
        {
            $this->pictureForm = new PictureForm($this->getUser(), $this->get_url());
        }

        return $this->pictureForm;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }
}
