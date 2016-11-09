<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: account_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.forms
 */
class PictureForm extends FormValidator
{

    private $parent;

    private $user;

    private $unencryptedpass;

    private $adm;

    /**
     * Creates a new AccountForm
     */
    public function __construct($user, $action)
    {
        parent::__construct('user_account', 'post', $action);

        $this->user = $user;
        $this->adm = \Chamilo\Core\Admin\Storage\DataManager::getInstance();

        $this->build_form();

        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    public function build_form()
    {
        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $this->user->get_id(),
                'cache-id' => time()));

        // Show user picture
        $this->addElement(
            'static',
            null,
            Translation::get('CurrentImage'),
            '<img class="my-account-photo" src="' . $profilePhotoUrl->getUrl() . '" alt="' . $this->user->get_fullname() .
                 '" />');

        // Picture
        if (PlatformSetting::get('allow_change_user_picture', Manager::context()) == 1)
        {
            $this->addElement(
                'file',
                User::PROPERTY_PICTURE_URI,
                ($this->user->has_picture() ? Translation::get('UpdateImage') : Translation::get('AddImage')));
            $this->addElement('static', null, null, Translation::get('AllowedProfileImageFormats'));

            $this->addElement('checkbox', 'remove_picture', Translation::get('DeleteImage'));

            $allowed_picture_types = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
            $this->addRule(
                User::PROPERTY_PICTURE_URI,
                Translation::get('OnlyImagesAllowed'),
                'filetype',
                $allowed_picture_types);

            $this->addElement('hidden', User::PROPERTY_ID);

            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation::get('Save', null, Utilities::COMMON_LIBRARIES));
            $buttons[] = $this->createElement(
                'style_reset_button',
                'reset',
                Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
    }

    /**
     * Builds an update form
     */
    public function update()
    {
        $user = $this->user;
        $values = $this->exportValues();

        if (PlatformSetting::get('allow_change_user_picture', Manager::context()))
        {

            if (isset($_FILES['picture_uri']) && strlen($_FILES['picture_uri']['name']) > 0)
            {
                if (! $_FILES['picture_uri']['error'])
                {
                    $user->set_picture_file($_FILES['picture_uri']);
                }
                else
                {
                    return false;
                }
            }

            if (isset($values['remove_picture']))
            {
                $user->delete_picture();
            }
        }

        $value = $user->update();

        if ($value)
        {
            Event::trigger(
                'Update',
                Manager::context(),
                array(
                    ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                    ChangesTracker::PROPERTY_USER_ID => $user->get_id()));
        }

        return $value;
    }
}
