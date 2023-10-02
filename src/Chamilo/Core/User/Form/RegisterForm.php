<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Form
 */
class RegisterForm extends FormValidator
{
    /**
     * @throws \QuickformException
     */
    public function __construct($action)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Basic'));

        $this->add_textfield(
            User::PROPERTY_LASTNAME, $translator->trans('LastName', [], Manager::CONTEXT), true, ['size' => '50']
        );
        $this->add_textfield(
            User::PROPERTY_FIRSTNAME, $translator->trans('FirstName', [], Manager::CONTEXT), true, ['size' => '50']
        );

        $this->add_textfield(
            User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT), true, ['size' => '50']
        );
        $this->addRule(User::PROPERTY_EMAIL, $translator->trans('WrongEmail'), 'email');

        $this->add_textfield(
            User::PROPERTY_USERNAME, $translator->trans('Username', [], Manager::CONTEXT), true, ['size' => '50']
        );

        $group = [];
        $group[] = $this->createElement(
            'radio', 'pass', null, $translator->trans('AutoGeneratePassword', [], Manager::CONTEXT) . '<br />', 1
        );
        $group[] = $this->createElement('radio', 'pass', null, null, 0);
        $group[] = $this->createElement('password', User::PROPERTY_PASSWORD, null, null);
        $this->addGroup($group, 'pw', $translator->trans('Password', [], Manager::CONTEXT), '');

        $this->addElement('category', $translator->trans('Additional', [], Manager::CONTEXT));

        $this->add_textfield(
            User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
            (bool) $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'require_official_code']),
            ['size' => '50']
        );

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_change_user_picture']))
        {
            $this->addElement(
                'file', User::PROPERTY_PICTURE_URI, $translator->trans('AddPicture', [], Manager::CONTEXT)
            );
            $this->addRule(
                User::PROPERTY_PICTURE_URI, $translator->trans('OnlyImagesAllowed', [], Manager::CONTEXT), 'filetype',
                ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF']
            );
        }

        $this->addElement(
            'text', User::PROPERTY_PHONE, $translator->trans('PhoneNumber', [], Manager::CONTEXT), ['size' => '50']
        );

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_teacher_registration']))
        {
            $status = [];
            $status[5] = $translator->trans('Student');
            $status[1] = $translator->trans('CourseAdmin');
            $this->addElement(
                'select', User::PROPERTY_STATUS, $translator->trans('Status', [], Manager::CONTEXT), $status
            );
        }

        $group = [];
        $group[] = $this->createElement(
            'radio', 'send_mail', null, $translator->trans('ConfirmYes', [], StringUtilities::LIBRARIES), 1
        );
        $group[] = $this->createElement(
            'radio', 'send_mail', null, $translator->trans('ConfirmNo', [], StringUtilities::LIBRARIES), 0
        );
        $this->addGroup($group, 'mail', $translator->trans('SendMailToNewUser', [], Manager::CONTEXT), '&nbsp;');

        if ($configurationConsulter->getSetting([Manager::CONTEXT, 'enable_terms_and_conditions']))
        {
            $this->addElement('category', $translator->trans('Information', [], Manager::CONTEXT));
            $this->addElement(
                'textarea', 'conditions', $translator->trans('TermsAndConditions', [], Manager::CONTEXT),
                ['cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style' => 'background-color: white;']
            );
            $this->addElement('checkbox', 'conditions_accept', '', $translator->trans('IAccept', [], Manager::CONTEXT));
            $this->addRule(
                'conditions_accept', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                'required'
            );
        }

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', $translator->trans('Register'), null, null, new FontAwesomeGlyph('user')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults(array $defaultValues = [], $filter = null): void
    {
        $defaults[User::PROPERTY_DATABASE_QUOTA] = '300';
        $defaults[User::PROPERTY_DISK_QUOTA] = '209715200';
        $defaults['mail']['send_mail'] = 1;
        $defaults['conditions'] = file_get_contents($this->getSystemPathBuilder()->getRootPath() . 'LICENSE');

        parent::setDefaults($defaults);
    }
}
