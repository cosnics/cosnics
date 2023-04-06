<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;

class TermsConditionsForm extends FormValidator
{
    const TYPE_VIEW = 'view';
    const TYPE_EDIT = 'edit';

    private $parent;

    private $user;

    /**
     * Creates a new TermsConditionsForm Used for a guest to agree with (new) terms and conditions
     */
    public function __construct($user, $action, $type)
    {
        parent::__construct('user_settings', 'post', $action);
        
        $this->user = $user;
        
        if ($type == self::TYPE_VIEW)
        {
            $this->build_view_form();
        }
        else
        {
            $this->build_edit_form();
        }
        $this->setDefaults();
    }

    /**
     * Creates a creation form
     */
    public function build_view_form()
    {
        $this->add_last_modified_date();
        $this->addElement('html', '</br>');
        $this->add_accepted_date();
        
        $this->addElement('category', Translation::get('NewTermsConditions'));
        $this->addElement(
            'textarea', 
            'conditions', 
            null, 
            array('cols' => 80, 'rows' => 20, 'readonly' => '', 'style' => 'background-color: white;'));
        $this->addElement('category');
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('TermsConditionsAccept'), 
            null, 
            null, 
            'user');
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function add_last_modified_date()
    {
        // show date last updated
        $date_format = '%e-%m-%Y %H:%M';
        $date = \Strftime($date_format, Manager::get_date_terms_and_conditions_last_modified());
        $this->addElement('html', Translation::get('TermsConditionsDate') . $date);
    }

    public function add_accepted_date()
    {
        // show date when user has accepted tems & conditions
        if ($this->user->get_terms_date() != null && $this->user->get_terms_date() > 0)
        {
            $date_format = '%e-%m-%Y %H:%M';
            $date = \Strftime($date_format, $this->user->get_terms_date());
            $this->addElement('html', Translation::get('TermsConditionsAcceptedDate') . $date);
        }
    }

    public function build_edit_form()
    {
        $this->add_last_modified_date();
        
        $this->addElement('category', Translation::get('TermsConditions'));
        $this->addElement(
            'textarea', 
            'conditions', 
            null, 
            array('cols' => 80, 'rows' => 20, 'style' => 'background-color: white;'));
        $this->addElement('category');
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('TermsConditionsEdit'), 
            null, 
            null, 
            'user');
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Registers the date on wich the terms and conditions where last seen
     */
    public function register_terms_user()
    {
        $this->user->set_term_date(time());
        return $this->user->update();
    }

    public function edit_terms_conditions()
    {
        $values = $this->exportValues();
        $text = $values['conditions'];
        Manager::set_terms_and_conditions($text);
    }

    /**
     * Sets default values.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array (), $filter = null)
    {
        $defaults['conditions'] = Manager::get_terms_and_conditions();
        parent::setDefaults($defaults);
    }
}
