<?php
namespace Chamilo\Application\CasStorage\Form;

use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class AccountRequestForm extends FormValidator
{
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $parent;

    /**
     *
     * @var AccountRequest
     */
    private $account_request;

    /**
     *
     * @var User
     */
    private $user;

    public function __construct($form_type, $account_request, $action, $user)
    {
        parent::__construct('account_request', 'post', $action);
        
        $this->account_request = $account_request;
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
        $this->addElement(
            'text', 
            AccountRequest::PROPERTY_FIRST_NAME, 
            Translation::get('FirstName'), 
            array("size" => "50"));
        $this->addRule(
            AccountRequest::PROPERTY_FIRST_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'text', 
            AccountRequest::PROPERTY_LAST_NAME, 
            Translation::get('LastName'), 
            array("size" => "50"));
        $this->addRule(
            AccountRequest::PROPERTY_LAST_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', AccountRequest::PROPERTY_EMAIL, Translation::get('Email'), array("size" => "50"));
        $this->addRule(
            AccountRequest::PROPERTY_EMAIL, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        $this->addRule(AccountRequest::PROPERTY_EMAIL, Translation::get('WrongEmail'), 'email');
        
        $affiliation_options = array();
        $affiliation_options['student'] = Translation::get('Student');
        $affiliation_options['employee'] = Translation::get('Employee');
        $affiliation_options['teacher'] = Translation::get('Teacher');
        $affiliation_options['external'] = Translation::get('External');
        
        $this->addElement(
            'select', 
            AccountRequest::PROPERTY_AFFILIATION, 
            Translation::get('Affiliation'), 
            $affiliation_options);
        
        $this->add_forever_or_timewindow(Translation::get('AccountExpiration'));
        
        $this->add_html_editor(AccountRequest::PROPERTY_MOTIVATION, Translation::get('Motivation'), true);
    }

    public function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', AccountRequest::PROPERTY_ID);
        
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

    public function update_account_request()
    {
        $account_request = $this->account_request;
        $values = $this->exportValues();
        
        $account_request->set_first_name($values[AccountRequest::PROPERTY_FIRST_NAME]);
        $account_request->set_last_name($values[AccountRequest::PROPERTY_LAST_NAME]);
        $account_request->set_email($values[AccountRequest::PROPERTY_EMAIL]);
        $account_request->set_affiliation($values[AccountRequest::PROPERTY_AFFILIATION]);
        $account_request->set_motivation($values[AccountRequest::PROPERTY_MOTIVATION]);
        
        if ($values[self::PARAM_FOREVER] != 0)
        {
            $from = $until = 0;
        }
        else
        {
            $from = DatetimeUtilities::time_from_datepicker($values[self::PARAM_FROM_DATE]);
            $until = DatetimeUtilities::time_from_datepicker($values[self::PARAM_TO_DATE]);
        }
        
        $account_request->set_valid_from($from);
        $account_request->set_valid_until($until);
        
        return $account_request->update();
    }

    public function create_account_request()
    {
        $account_request = $this->account_request;
        $values = $this->exportValues();
        
        $account_request->set_first_name($values[AccountRequest::PROPERTY_FIRST_NAME]);
        $account_request->set_last_name($values[AccountRequest::PROPERTY_LAST_NAME]);
        $account_request->set_email($values[AccountRequest::PROPERTY_EMAIL]);
        $account_request->set_affiliation($values[AccountRequest::PROPERTY_AFFILIATION]);
        $account_request->set_motivation($values[AccountRequest::PROPERTY_MOTIVATION]);
        $account_request->set_requester_id($this->user->get_id());
        $account_request->set_request_date(time());
        $account_request->set_status(AccountRequest::STATUS_PENDING);
        
        if ($values[self::PARAM_FOREVER] != 0)
        {
            $from = $until = 0;
        }
        else
        {
            $from = DatetimeUtilities::time_from_datepicker($values[self::PARAM_FROM_DATE]);
            $until = DatetimeUtilities::time_from_datepicker($values[self::PARAM_TO_DATE]);
        }
        
        $account_request->set_valid_from($from);
        $account_request->set_valid_until($until);
        
        return $account_request->create();
    }

    /**
     * Sets default values.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $account_request = $this->account_request;
        $defaults[AccountRequest::PROPERTY_ID] = $account_request->get_id();
        $defaults[AccountRequest::PROPERTY_FIRST_NAME] = $account_request->get_first_name();
        $defaults[AccountRequest::PROPERTY_LAST_NAME] = $account_request->get_last_name();
        $defaults[AccountRequest::PROPERTY_EMAIL] = $account_request->get_email();
        $defaults[AccountRequest::PROPERTY_MOTIVATION] = $account_request->get_motivation();
        $defaults[AccountRequest::PROPERTY_AFFILIATION] = $account_request->get_affiliation();
        $defaults[AccountRequest::PROPERTY_AFFILIATION] = $account_request->get_affiliation();
        
        if ($this->account_request->get_valid_from() != 0)
        {
            $defaults[self::PARAM_FOREVER] = 0;
            $defaults[self::PARAM_FROM_DATE] = $account_request->get_valid_from();
            $defaults[self::PARAM_TO_DATE] = $account_request->get_valid_until();
        }
        else
        {
            $defaults[self::PARAM_FOREVER] = 1;
        }
        
        parent::setDefaults($defaults);
    }

    public function get_account_request()
    {
        return $this->account_request;
    }
}
