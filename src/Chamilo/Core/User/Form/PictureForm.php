<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PictureForm extends FormValidator
{
    private User $user;

    /**
     * @throws \QuickformException
     */
    public function __construct(User $user, string $action)
    {
        parent::__construct('user_account', self::FORM_METHOD_POST, $action);

        $this->user = $user;
        $this->buildForm();
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $userPictureProvider = $this->getUserPictureProvider();
        $translator = $this->getTranslator();
        $user = $this->getUser();

        $this->addElement(
            'static', 'image', $translator->trans('CurrentImage', [], Manager::CONTEXT),
            '<img class="my-account-photo" src="' . $userPictureProvider->getUserPictureAsBase64String($user, $user) .
            '" alt="' . $user->get_fullname() . '" />'
        );

        $this->addElement(
            'file', User::PROPERTY_PICTURE_URI, ($userPictureProvider->doesUserHavePicture($user) ?
            $translator->trans('UpdateImage', [], Manager::CONTEXT) :
            $translator->trans('AddImage', [], Manager::CONTEXT))
        );
        $this->addElement(
            'static', 'formats', null, $translator->trans('AllowedProfileImageFormats', [], Manager::CONTEXT)
        );

        $this->addElement('checkbox', 'remove_picture', $translator->trans('DeleteImage', [], Manager::CONTEXT));

        $allowedPictureTypes = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        $this->addRule(
            User::PROPERTY_PICTURE_URI, $translator->trans('OnlyImagesAllowed', [], Manager::CONTEXT), 'filetype',
            $allowedPictureTypes
        );

        $this->addElement('hidden', DataClass::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Save', [], StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }
}
