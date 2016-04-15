<?php
namespace Chamilo\Application\Survey\Mail\Form;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class MailTestForm extends FormValidator
{
    const ALL_PARTICIPANTS = 'all_participants';
    const FROM_ADDRESS = 'from_address';
    const FROM_ADDRESS_NAME = 'from_address_name';
    const TO_ADDRESS = 'to_address';
    const TO_ADDRESS_NAME = 'to_address_name';
    const REPLY_ADDRESS = 'reply_address';
    const REPLY_ADDRESS_NAME = 'reply_address_name';
    const EMAIL_HEADER = 'email_header';
    const EMAIL_CONTENT = 'email_content';
    const EMAILCOUNT = 'email_count';

    function __construct($parent, $user, $actions)
    {
        parent :: __construct('mail_tester', 'post', $actions);

        $this->addElement(
            'text',
            self :: FROM_ADDRESS_NAME,
            Translation :: get('SurveyFromEmailAddressName'),
            array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: FROM_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text',
            self :: FROM_ADDRESS,
            Translation :: get('SurveyFromEmailAddress'),
            array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: FROM_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text',
            self :: REPLY_ADDRESS_NAME,
            Translation :: get('SurveyReplyEmailAddressName'),
            array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: REPLY_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text',
            self :: REPLY_ADDRESS,
            Translation :: get('SurveyReplyEmailAddress'),
            array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: REPLY_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self :: EMAIL_HEADER, Translation :: get('SurveyEmailTitle'), array('size' => 80));
        $this->addRule(self :: EMAIL_HEADER, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(self :: EMAIL_CONTENT, Translation :: get('SurveyEmailContent'), true);

        $this->addElement(
            'text',
            self :: TO_ADDRESS_NAME,
            Translation :: get('SurveyToEmailAddressName'),
            array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self :: TO_ADDRESS_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text',
            self :: TO_ADDRESS,
            Translation :: get('SurveyToEmailAddress'),
            array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self :: TO_ADDRESS, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', self :: EMAILCOUNT, Translation :: get('MailCount'), array('size' => 5));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('SendMail', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        // InvitationManager :: get_elements($this, false);

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>