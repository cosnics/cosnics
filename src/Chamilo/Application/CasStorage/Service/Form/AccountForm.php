<?php
namespace Chamilo\Application\CasStorage\Service\Form;

use Chamilo\Application\CasStorage\Account\Storage\DataClass\Account;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

class AccountForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const PASSWORD_OPTION = 'password_option';
    const PASSWORD_GROUP = 'password_group';

    private $parent;

    /**
     *
     * @var string
     */
    private $unencrypted_password;

    /**
     *
     * @var CasAccount
     */
    private $cas_account;

    /**
     *
     * @var User
     */
    private $user;

    public function __construct($form_type, $cas_account, $action, $user)
    {
        parent::__construct('cas_account', 'post', $action);
        
        $this->cas_account = $cas_account;
        $this->user = $user;
        $this->form_type = $form_type;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', Account::PROPERTY_FIRST_NAME, Translation::get('FirstName'), array("size" => "50"));
        $this->addRule(
            Account::PROPERTY_FIRST_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Account::PROPERTY_LAST_NAME, Translation::get('LastName'), array("size" => "50"));
        $this->addRule(
            Account::PROPERTY_LAST_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Account::PROPERTY_EMAIL, Translation::get('Email'), array("size" => "50"));
        $this->addRule(
            Account::PROPERTY_EMAIL, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        $this->addRule(Account::PROPERTY_EMAIL, Translation::get('WrongEmail'), 'email');
        
        $group = array();
        if ($this->form_type == self::TYPE_EDIT)
        {
            $group[] = & $this->createElement(
                'radio', 
                self::PASSWORD_OPTION, 
                null, 
                Translation::get('KeepPassword') . '<br />', 
                2);
        }
        $group[] = & $this->createElement(
            'radio', 
            self::PASSWORD_OPTION, 
            null, 
            Translation::get('AutoGeneratePassword') . '<br />', 
            1);
        $group[] = & $this->createElement('radio', self::PASSWORD_OPTION, null, null, 0);
        $group[] = & $this->createElement(
            'password', 
            Account::PROPERTY_PASSWORD, 
            null, 
            null, 
            array('autocomplete' => 'off'));
        $this->addGroup($group, self::PASSWORD_GROUP, Translation::get('Password'), '');
        
        $affiliation_options = array();
        $affiliation_options['student'] = Translation::get('Student');
        $affiliation_options['employee'] = Translation::get('Employee');
        $affiliation_options['teacher'] = Translation::get('Teacher');
        $affiliation_options['external'] = Translation::get('External');
        
        $this->addElement(
            'select', 
            Account::PROPERTY_AFFILIATION, 
            Translation::get('Affiliation'), 
            $affiliation_options);
        
        $this->addElement('text', Account::PROPERTY_GROUP, Translation::get('Group'), array("size" => "50"));
        $this->addRule(
            Account::PROPERTY_GROUP, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('checkbox', Account::PROPERTY_STATUS, Translation::get('Enabled'), '', 1);
    }

    public function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', Account::PROPERTY_ID);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_cas_account()
    {
        $cas_account = $this->cas_account;
        $values = $this->exportValues();
        
        $cas_account->set_first_name($values[Account::PROPERTY_FIRST_NAME]);
        $cas_account->set_last_name($values[Account::PROPERTY_LAST_NAME]);
        $cas_account->set_email($values[Account::PROPERTY_EMAIL]);
        $cas_account->set_affiliation($values[Account::PROPERTY_AFFILIATION]);
        $cas_account->set_group($values[Account::PROPERTY_GROUP]);
        $cas_account->set_status($values[Account::PROPERTY_STATUS]);
        
        if ($values[self::PASSWORD_GROUP][self::PASSWORD_OPTION] != 2)
        {
            $this->unencrypted_password = $values[self::PASSWORD_GROUP][self::PASSWORD_OPTION] == 1 ? $this->unencrypted_password : $values[self::PASSWORD_GROUP][Account::PROPERTY_PASSWORD];
            $password = md5($this->unencrypted_password);
            $cas_account->set_password($password);
        }
        
        return $cas_account->update();
    }

    public function create_cas_account()
    {
        $cas_account = $this->cas_account;
        $values = $this->exportValues();
        
        $cas_account->set_first_name($values[Account::PROPERTY_FIRST_NAME]);
        $cas_account->set_last_name($values[Account::PROPERTY_LAST_NAME]);
        $cas_account->set_email($values[Account::PROPERTY_EMAIL]);
        $cas_account->set_affiliation($values[Account::PROPERTY_AFFILIATION]);
        $cas_account->set_group($values[Account::PROPERTY_GROUP]);
        $cas_account->set_status($values[Account::PROPERTY_STATUS]);
        
        $this->unencrypted_password = $values[self::PASSWORD_GROUP][self::PASSWORD_OPTION] == 1 ? Text::generate_password() : $values[self::PASSWORD_GROUP][Account::PROPERTY_PASSWORD];
        $cas_account->set_password(md5($this->unencrypted_password));
        
        if (! $cas_account->create())
        {
            return false;
        }
        else
        {
            $cas_account->set_person_id('EXT' . $cas_account->get_id());
            return $cas_account->update();
        }
    }

    /**
     * Sets default values.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $cas_account = $this->cas_account;
        $defaults[Account::PROPERTY_ID] = $cas_account->get_id();
        $defaults[Account::PROPERTY_FIRST_NAME] = $cas_account->get_first_name();
        $defaults[Account::PROPERTY_LAST_NAME] = $cas_account->get_last_name();
        $defaults[Account::PROPERTY_EMAIL] = $cas_account->get_email();
        $defaults[Account::PROPERTY_AFFILIATION] = $cas_account->get_affiliation();
        $defaults[Account::PROPERTY_GROUP] = $cas_account->get_group();
        $defaults[Account::PROPERTY_STATUS] = $cas_account->get_status();
        
        if ($this->form_type == self::TYPE_EDIT)
        {
            $defaults[self::PASSWORD_GROUP][self::PASSWORD_OPTION] = 2;
        }
        else
        {
            $defaults[self::PASSWORD_GROUP][self::PASSWORD_OPTION] = 1;
        }
        
        parent::setDefaults($defaults);
    }

    public function get_cas_account()
    {
        return $this->cas_account;
    }
}
