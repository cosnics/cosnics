<?php
namespace Chamilo\Core\User\Email\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package application.common.category_manager
 */
class EmailForm extends FormValidator
{

    private $user;

    private $target_users;

    /**
     * Creates a new LanguageForm
     */
    public function __construct($action, $user, $target_users)
    {
        parent::__construct('email_form', self::FORM_METHOD_POST, $action);

        $this->target_users = $target_users;
        $this->user = $user;

        $this->build_form();
    }

    public function build_form()
    {
        $this->addElement('category', Translation::get('Email'));

        $this->addElement('text', 'title', Translation::get('EmailTitle'), array('size' => '50'));
        $this->addRule('title', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required');

        $this->add_html_editor(
            'message', Translation::get('EmailMessage'), true, array('height' => 500, 'width' => 750)
        );

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Email'), null, null, new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function email()
    {
        $values = $this->exportValues();

        $title = $values['title'];
        $message = $values['message'];
        $targets = $this->get_target_email_addresses();

        $mail = new Mail($title, $message, $targets, false, array($this->user->get_email()));

        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception $ex)
        {
        }

        return true;
    }

    public function get_target_email_addresses()
    {
        $email_addresses = [];

        foreach ($this->target_users as $target_user)
        {
            if (is_object($target_user) && $target_user instanceof User)
            {
                $email_addresses[] = $target_user->get_email();
            }
            else
            {
                $email_addresses[] = $target_user;
            }
        }

        return $email_addresses;
    }
}
