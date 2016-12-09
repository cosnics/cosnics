<?php
namespace Chamilo\Application\Survey\Mail\Form;

use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class MailForm extends FormValidator
{
    const APPLICATION_NAME = 'survey';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_RIGHTS = 'rights';
    const ALL_PARTICIPANTS = 'all_participants';
    const FROM_ADDRESS = 'from_address';
    const FROM_ADDRESS_NAME = 'from_address_name';
    const REPLY_ADDRESS = 'reply_address';
    const REPLY_ADDRESS_NAME = 'reply_address_name';
    const EMAIL_HEADER = 'email_header';
    const EMAIL_CONTENT = 'email_content';
    const USERS_NOT_SELECTED_COUNT = 'users_not_selected_count';
    const FORM_NAME = 'survey_publication_mailer';

    private $type;

    function __construct($parent, $user, $users, $type, $actions)
    {
        parent::__construct(self::FORM_NAME, 'post', $actions);
        
        $this->type = $type;
        
        $attributes = array();
        $attributes['search_url'] = Path::getInstance()->getBasePath(true) . 'group/php/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation::get('ShareWith');
        $locale['Searching'] = Translation::get('Searching');
        $locale['NoResults'] = Translation::get('NoResults');
        $locale['Error'] = Translation::get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(
            self::APPLICATION_NAME . '_opt_' . self::PARAM_TARGET, 
            Translation::get('AddMailRecipientsFilter'), 
            $attributes);
        
        $defaults[self::APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self::APPLICATION_NAME . '_opt_' . self::PARAM_TARGET_OPTION] = 0;
        
        $this->addElement(
            'text', 
            self::FROM_ADDRESS_NAME, 
            Translation::get('SurveyFromEmailAddressName'), 
            array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self::FROM_ADDRESS_NAME, Translation::get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text', 
            self::FROM_ADDRESS, 
            Translation::get('SurveyFromEmailAddress'), 
            array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self::FROM_ADDRESS, Translation::get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text', 
            self::REPLY_ADDRESS_NAME, 
            Translation::get('SurveyReplyEmailAddressName'), 
            array('size' => 80, 'value' => $user->get_firstname() . ' ' . $user->get_lastname()));
        $this->addRule(self::REPLY_ADDRESS_NAME, Translation::get('ThisFieldIsRequired'), 'required');
        $this->addElement(
            'text', 
            self::REPLY_ADDRESS, 
            Translation::get('SurveyReplyEmailAddress'), 
            array('size' => 80, 'value' => $user->get_email()));
        $this->addRule(self::REPLY_ADDRESS, Translation::get('ThisFieldIsRequired'), 'required');
        $this->addElement('text', self::EMAIL_HEADER, Translation::get('SurveyEmailTitle'), array('size' => 80));
        $this->addRule(self::EMAIL_HEADER, Translation::get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(self::EMAIL_CONTENT, Translation::get('SurveyEmailContent'), true);
        
        $this->add_warning_message(
            'attention', 
            Translation::get('SurveyMailAttention'), 
            Translation::get('SurveyAttentionSendMailInfo'), 
            false);
        
        switch ($this->type)
        {
            case Mail::PARTICIPANT_TYPE :
                $invitees = (count($users[RightsService::RIGHT_TAKE]) > 1) ? Translation::get('Invitees') : Translation::get(
                    'Invitee');
                $this->addElement(
                    'checkbox', 
                    RightsService::RIGHT_TAKE, 
                    Translation::get('AllInvitees'), 
                    ' ' . $users[RightsService::RIGHT_TAKE] . ' ' . $invitees);
                $not_started = (count($users[Participant::STATUS_NOTSTARTED]) > 1) ? Translation::get('Invitees') : Translation::get(
                    'Invitee');
                $this->addElement(
                    'checkbox', 
                    Participant::STATUS_NOTSTARTED, 
                    Translation::get('SurveyNotStarted'), 
                    ' ' . $users[Participant::STATUS_NOTSTARTED] . ' ' . $not_started);
                $started = (count($users[Participant::STATUS_STARTED]) > 1) ? Translation::get('Participants') : Translation::get(
                    'Participant');
                $this->addElement(
                    'checkbox', 
                    Participant::STATUS_STARTED, 
                    Translation::get('SurveyStarted'), 
                    ' ' . $users[Participant::STATUS_STARTED] . ' ' . $started);
                $finished = (count($users[Participant::STATUS_FINISHED]) > 1) ? Translation::get('Participants') : Translation::get(
                    'Participant');
                $this->addElement(
                    'checkbox', 
                    Participant::STATUS_FINISHED, 
                    Translation::get('SurveyFinished'), 
                    ' ' . $users[Participant::STATUS_FINISHED] . ' ' . $finished);
                break;
            case Mail::EXPORT_TYPE :
                $exporters = (count($users[RightsService::RIGHT_REPORT]) > 1) ? Translation::get('Exporters') : Translation::get(
                    'Exporter');
                $this->addElement(
                    'checkbox', 
                    RightsService::RIGHT_REPORT, 
                    Translation::get('SurveyExporters'), 
                    ' ' . $users[RightsService::RIGHT_REPORT] . ' ' . $exporters);
                break;
            
            case Mail::REPORTING_TYPE :
                $rapporteurs = (count($users[RightsService::RIGHT_REPORT]) > 1) ? Translation::get('Rapporteurs') : Translation::get(
                    'Rapporteur');
                $this->addElement(
                    'checkbox', 
                    RightsService::RIGHT_REPORT, 
                    Translation::get('SurveyRapporters'), 
                    ' ' . $users[RightsService::RIGHT_REPORT] . ' ' . $rapporteurs);
                break;
        }
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('SendMail', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaults($defaults);
    }

    function get_seleted_group_user_ids()
    {
        $values = $this->exportValues();
        
        $user_ids = array();
        
        if ($values[self::APPLICATION_NAME . '_opt_' . self::PARAM_TARGET_OPTION] == 0)
        {
            // there is no user filter needed
            $user_ids = null;
        }
        else
        {
            $group_ids = $values[self::APPLICATION_NAME . '_opt_' . self::PARAM_TARGET . '_elements']['group'];
            
            if (count($group_ids))
            {
                foreach ($group_ids as $group_id)
                {
                    $group_user_ids = array();
                    foreach ($group_ids as $group_id)
                    {
                        
                        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $group_id);
                        $ids = $group->get_users(true, true);
                        $group_user_ids = array_merge($group_user_ids, $ids);
                    }
                    $user_ids = array_unique($group_user_ids);
                }
            }
        }
        return $user_ids;
    }
}
?>