<?php
namespace Chamilo\Core\User\Email\Form;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: email_form.class.php 191 2009-11-13 11:50:28Z chellee $
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
        parent :: __construct('email_form', 'post', $action);
        
        $this->target_users = $target_users;
        $this->user = $user;
        
        $this->build_form();
    }

    public function build_form()
    {
        $this->addElement('category', Translation :: get('Email'));
        
        $this->addElement('text', 'title', Translation :: get('EmailTitle'), array('size' => '50'));
        $this->addRule(
            'title', 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->add_html_editor(
            'message', 
            Translation :: get('EmailMessage'), 
            true, 
            array('height' => 500, 'width' => 750));
        
        $this->addElement('category');
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Email'), 
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function email()
    {
        $values = $this->exportValues();
        
        $title = $values['title'];
        $message = $values['message'];
        $targets = $this->get_target_email_addresses();
        
        $mail = Mail :: factory($title, $message, $targets, array($this->user->get_email()));
        $mail->send();
        
        return true;
    }

    public function get_target_email_addresses()
    {
        $email_addresses = array();
        
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
