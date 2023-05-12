<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package user.lib.forms
 */
class PictureForm extends FormValidator
{
    use DependencyInjectionContainerTrait;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $action
     *
     * @throws \Exception
     */
    public function __construct($user, $action)
    {
        parent::__construct('user_account', self::FORM_METHOD_POST, $action);

        $this->user = $user;

        $this->initializeContainer();
        $this->build_form();
        $this->setDefaults();
    }

    /**
     * @throws \Exception
     */
    public function build_form()
    {
        $userPictureProvider = $this->getUserPictureProvider();

        // Show user picture
        $this->addElement(
            'static', null, Translation::get('CurrentImage'), '<img class="my-account-photo" src="' .
            $userPictureProvider->getUserPictureAsBase64String($this->user, $this->user) . '" alt="' .
            $this->user->get_fullname() . '" />'
        );

        // Picture
        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
        {
            $this->addElement(
                'file', User::PROPERTY_PICTURE_URI,
                ($userPictureProvider->doesUserHavePicture($this->user) ? Translation::get('UpdateImage') :
                    Translation::get('AddImage'))
            );
            $this->addElement('static', null, null, Translation::get('AllowedProfileImageFormats'));

            $this->addElement('checkbox', 'remove_picture', Translation::get('DeleteImage'));

            $allowed_picture_types = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
            $this->addRule(
                User::PROPERTY_PICTURE_URI, Translation::get('OnlyImagesAllowed'), 'filetype', $allowed_picture_types
            );

            $this->addElement('hidden', User::PROPERTY_ID);

            $buttons[] = $this->createElement(
                'style_submit_button', 'submit', Translation::get('Save', null, StringUtilities::LIBRARIES)
            );
            $buttons[] = $this->createElement(
                'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
            );

            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
    }

    /**
     * @return \Chamilo\Core\User\Picture\UserPictureProviderInterface
     */
    public function getUserPictureProvider()
    {
        return $this->getService('Chamilo\Core\User\Picture\UserPictureProvider');
    }

    /**
     * Builds an update form
     */
    public function update()
    {
        $user = $this->user;
        $values = $this->exportValues();

        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface)
        {
            if (isset($_FILES[User::PROPERTY_PICTURE_URI]) && strlen($_FILES[User::PROPERTY_PICTURE_URI]['name']) > 0)
            {
                if (!$_FILES[User::PROPERTY_PICTURE_URI]['error'])
                {
                    $userPictureProvider->setUserPicture($user, $user, $_FILES[User::PROPERTY_PICTURE_URI]);
                }
                else
                {
                    return false;
                }
            }

            if (isset($values['remove_picture']))
            {
                $userPictureProvider->deleteUserPicture($user, $user);
            }
        }

        $value = $user->update();

        if ($value)
        {
            Event::trigger(
                'Update', Manager::CONTEXT, array(
                    ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                    ChangesTracker::PROPERTY_USER_ID => $user->get_id()
                )
            );
        }

        return $value;
    }
}
