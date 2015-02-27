<?php
namespace Chamilo\Application\Survey\Mail\Component;

use Chamilo\Application\Survey\Cron\Storage\DataClass\MailJob;
use Chamilo\Application\Survey\Mail\Form\MailForm;
use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Application\Survey\Mail\Storage\DataClass\UserMail;
use Chamilo\Application\Survey\Mail\Storage\DataManager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class ManagerSendMailComponent extends Manager
{

    private $invitees;

    private $reporting_users;

    private $not_started;

    private $started;

    private $finished;

    private $mail_send = true;

    private $publication_id;

    private $survey_id;

    private $type;

    private $send_date;

    function run()
    {
        $this->publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        $this->type = Request :: get(Manager :: PARAM_TYPE);
        
        if (! Rights :: get_instance()->is_right_granted(Rights :: INVITE_RIGHT, $this->publication_id))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        switch ($this->type)
        {
            case Mail :: PARTICIPANT_TYPE :
                $target_entities = Rights :: get_instance()->get_publication_targets_entities(
                    Rights :: PARTICIPATE_RIGHT, 
                    $this->publication_id);
                
                if (is_array(($target_entities[UserEntity :: ENTITY_TYPE])))
                {
                    $user_ids = $target_entities[UserEntity :: ENTITY_TYPE];
                }
                else
                {
                    $user_ids = array();
                }
                
                if (is_array(($target_entities[PlatformGroupEntity :: ENTITY_TYPE])))
                {
                    $group_ids = $target_entities[PlatformGroupEntity :: ENTITY_TYPE];
                    $group_user_ids = array();
                    foreach ($group_ids as $group_id)
                    {
                        $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                            Group :: class_name(), 
                            $group_id);
                        $group_user_ids = array_merge($group_user_ids, $group->get_users(true, true));
                    }
                }
                else
                {
                    $group_user_ids = array();
                }
                $group_user_ids = array_unique($group_user_ids);
                $this->invitees = array_merge($user_ids, $group_user_ids);
                $this->not_started = array();
                $this->started = array();
                $this->finished = array();
                
                $condition = new EqualityCondition(Participant :: PROPERTY_SURVEY_PUBLICATION_ID, $this->publication_id);
                $parameters = new DataClassRetrievesParameters($condition);
                $participants = DataManager :: get_instance()->retrieves(Participant :: class_name(), $parameters);
                
                while ($participant = $participants->next_result())
                {
                    if ($participant->get_status() == Participant :: STATUS_FINISHED)
                    {
                        $this->finished[] = $participant->get_user_id();
                    }
                    else 
                        if ($participant->get_status() == Participant :: STATUS_STARTED)
                        {
                            $this->started[] = $participant->get_user_id();
                        }
                }
                
                $invitee_count = count(array_unique($this->invitees));
                $started_count = count(array_unique($this->started));
                $finished_count = count(array_unique($this->finished));
                $started_and_finished_users = array_merge($this->started, $this->finished);
                $this->not_started = array_diff($this->invitees, $started_and_finished_users);
                
                $not_started_count = $invitee_count - $started_count - $finished_count;
                
                $users = array();
                $users[Rights :: PARTICIPATE_RIGHT_NAME] = $invitee_count;
                $users[Participant :: STATUS_STARTED] = $started_count;
                $users[Participant :: STATUS_NOTSTARTED] = $not_started_count;
                $users[Participant :: STATUS_FINISHED] = $finished_count;
                break;
            case Mail :: EXPORT_TYPE :
                $target_entities = Rights :: get_instance()->get_publication_targets_entities(
                    Rights :: RIGHT_EXPORT_RESULT, 
                    $this->publication_id);
                $group_ids = $target_entities[PlatformGroupEntity :: ENTITY_TYPE];
                $group_user_ids = array();
                foreach ($group_ids as $group_id)
                {
                    $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $group_id);
                    $group_user_ids = array_merge($group_user_ids, $group->get_users(true, true));
                }
                $group_user_ids = array_unique($group_user_ids);
                $this->invitees = array_merge($target_entities[UserEntity :: ENTITY_TYPE], $group_user_ids);
                $invitee_count = count(array_unique($this->invitees));
                $users = array();
                $users[Rights :: EXPORT_RESULT_RIGHT_NAME] = $invitee_count;
                //
                break;
            case Mail :: REPORTING_TYPE :
                $target_entities = Rights :: get_instance()->get_publication_targets_entities(
                    Rights :: RIGHT_REPORTING, 
                    $this->publication_id);
                $group_ids = $target_entities[PlatformGroupEntity :: ENTITY_TYPE];
                $group_user_ids = array();
                foreach ($group_ids as $group_id)
                {
                    $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $group_id);
                    $group_user_ids = array_merge($group_user_ids, $group->get_users(true, true));
                }
                $group_user_ids = array_unique($group_user_ids);
                $this->invitees = array_merge($target_entities[UserEntity :: ENTITY_TYPE], $group_user_ids);
                $invitee_count = count(array_unique($this->invitees));
                $users = array();
                $users[Rights :: REPORTING_RIGHT_NAME] = $invitee_count;
                break;
        }
        
        $survey_publication = DataManager :: retrieve_by_id(Publication :: class_name(), $this->publication_id);
        $form = new MailForm(
            $this, 
            $this->get_user(), 
            $users, 
            $this->type, 
            $this->get_url(
                array(self :: PARAM_PUBLICATION_ID => $this->publication_id, Manager :: PARAM_TYPE => $this->type)));
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            $user_ids = $form->get_seleted_group_user_ids();
            $this->parse_values($values, $user_ids);
        }
        else
        {
            $this->display_header();
            echo $this->get_survey_html($survey_publication);
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function get_survey_html($survey_publication)
    {
        $html = array();
        $html[] = '<div class="content_object" style="background-image: url(' .
             Theme :: getInstance()->getImagesPath('application\survey') . 'survey_22.png);">';
        
        switch ($this->type)
        {
            case Mail :: PARTICIPANT_TYPE :
                $html[] = '<div class="title">' . Translation :: get('MailToParticipantsForSurvey') . '  ' . ' </div>';
                break;
            case Mail :: EXPORT_TYPE :
                $html[] = '<div class="title">' . Translation :: get('MailToExportersForSurvey') . '  ' . ' </div>';
                break;
            case Mail :: REPORTING_TYPE :
                $html[] = '<div class="title">' . Translation :: get('MailToReportersForSurvey') . '  ' . ' </div>';
                break;
        }
        $html[] = $survey_publication->get_title() . '<br/>';
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    function parse_values($values, $filter_user_ids)
    {
        $users = array();
        $mail_user_ids = array();
        
        switch ($this->type)
        {
            case Mail :: PARTICIPANT_TYPE :
                $user_ids = array();
                
                $not_started = $values[Participant :: STATUS_NOTSTARTED];
                if ($not_started == 1)
                {
                    $user_ids = array_merge($user_ids, $this->not_started);
                }
                
                $started = $values[Participant :: STATUS_STARTED];
                
                if ($started == 1)
                {
                    $user_ids = array_merge($user_ids, $this->started);
                }
                
                $finished = $values[Participant :: STATUS_FINISHED];
                
                if ($finished == 1)
                {
                    $user_ids = array_merge($user_ids, $this->finished);
                }
                
                $invitees = $values[Rights :: PARTICIPATE_RIGHT_NAME];
                
                if ($invitees == 1)
                {
                    
                    $user_ids = array_merge($user_ids, $this->invitees);
                }
                
                $user_ids = array_unique($user_ids);
                if (isset($filter_user_ids))
                {
                    $mail_user_ids = array_intersect($filter_user_ids, $user_ids);
                }
                else
                {
                    $mail_user_ids = $user_ids;
                }
                break;
            case Mail :: EXPORT_TYPE :
                if (isset($user_ids))
                {
                    $this->invitees = array_intersect($this->invitees, $user_ids);
                }
                
                $invitees = $values[Rights :: EXPORT_RESULT_RIGHT_NAME];
                
                if ($invitees == 1)
                {
                    
                    $mail_user_ids = array_merge($mail_user_ids, $this->invitees);
                }
                break;
            case Mail :: REPORTING_TYPE :
                
                if (isset($user_ids))
                {
                    $this->invitees = array_intersect($this->invitees, $user_ids);
                }
                
                $invitees = $values[Rights :: REPORTING_RIGHT_NAME];
                if ($invitees == 1)
                {
                    $mail_user_ids = array_merge($mail_user_ids, $this->invitees);
                }
                break;
        }
        
        $mail_user_ids = array_unique($mail_user_ids);
        
        if (count($mail_user_ids) == 0)
        {
            $this->redirect(
                Translation :: get('NoSurveyMailsSend'), 
                false, 
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $email_content = $values[MailForm :: EMAIL_CONTENT];
            
            $fullbody = array();
            $fullbody[] = $email_content;
            $fullbody[] = '<br/><br/>';
            $fullbody[] = '<p id="link">';
            
            $parameters = array();
            
            switch ($this->type)
            {
                case Mail :: PARTICIPANT_TYPE :
                    $parameters[Manager :: PARAM_ACTION] = \Chamilo\Application\Survey\Manager :: ACTION_TAKE;
                    $parameters[Manager :: PARAM_PUBLICATION_ID] = $this->publication_id;
                    $url = Path :: getInstance()->getBasePath(true) . $this->get_link($parameters);
                    $fullbody[] = '<a href=' . $url . '>' . Translation :: get('ClickToTakeSurvey') . '</a>';
                    $selected_tab = BrowserComponent :: TAB_MAILS_TO_PARTICIPANTS;
                    break;
                case Mail :: EXPORT_TYPE :
                    $parameters[Manager :: PARAM_ACTION] = \Chamilo\Application\Survey\Export\Manager :: ACTION_EXPORT;
                    $parameters[Manager :: PARAM_PUBLICATION_ID] = $this->publication_id;
                    $url = Path :: getInstance()->getBasePath(true) . $this->get_link($parameters);
                    $fullbody[] = '<a href=' . $url . '>' . Translation :: get('ClickToExportResults') . '</a>';
                    $selected_tab = BrowserComponent :: TAB_MAILS_TO_EXPORTERS;
                    break;
            }
            
            $fullbody[] = '<br/><br/>' . Translation :: get('OrCopyAndPasteThisText') . ':';
            $fullbody[] = '<br/><a href=' . $url . '>' . $url . '</a>';
            $fullbody[] = '</p>';
            
            $body = implode(PHP_EOL, $fullbody);
            
            $email_header = $values[MailForm :: EMAIL_HEADER];
            $email_from_address = $values[MailForm :: FROM_ADDRESS];
            $email_reply_address = $values[MailForm :: REPLY_ADDRESS];
            $email_from_address_name = $values[MailForm :: FROM_ADDRESS_NAME];
            $email_reply_address_name = $values[MailForm :: REPLY_ADDRESS_NAME];
            
            $email = new Mail();
            $email->set_mail_header($email_header);
            $email->set_mail_content($body);
            $email->set_sender_user_id($this->get_user_id());
            $email->set_from_address($email_from_address);
            $email->set_from_address_name($email_from_address_name);
            $email->set_reply_address($email_reply_address);
            $email->set_reply_address_name($email_reply_address_name);
            $email->set_publication_id($this->publication_id);
            $email->set_send_date(time());
            $email->set_type($this->type);
            $succes = $email->create();
            
            if ($succes)
            {
                foreach ($mail_user_ids as $key => $user_id)
                {
                    $user = $dm = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                        User :: class_name(), 
                        $user_id);
                    $to_email = $user->get_email();
                    $this->send_mail($user_id, $to_email, $email);
                }
                
                $cron_enabled = PlatformSetting :: get('enable_mail_cron_job', 'application\survey');
                if ($this->mail_send == false)
                {
                    if (! $cron_enabled)
                    {
                        $message = Translation :: get('NotAllMailsSend');
                    }
                    else
                    {
                        $message = Translation :: get('NotAllMailJobsCreated');
                    }
                }
                else
                {
                    if (! $cron_enabled)
                    {
                        $message = Translation :: get('AllMailsSend');
                    }
                    else
                    {
                        $message = Translation :: get('AllMailJobsCreated');
                    }
                }
                $this->redirect(
                    $message, 
                    ! $this->mail_send, 
                    array(
                        self :: PARAM_ACTION => self :: ACTION_BROWSE, 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => $selected_tab));
            }
            else
            {
                $this->redirect(
                    Translation :: get('NoMailsSend'), 
                    true, 
                    array(
                        self :: PARAM_ACTION => self :: ACTION_BROWSE, 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => $selected_tab));
            }
        }
    }

    function send_mail($user_id, $to_email, $email)
    {
        $arg = array();
        $user_mail = new UserMail();
        $user_mail->set_user_id($user_id);
        $user_mail->set_mail_id($email->get_id());
        $user_mail->set_publication_id($this->publication_id);
        
        $cron_enabled = PlatformSetting :: get('enable_mail_cron_job', 'application\survey');
        
        if (! $cron_enabled)
        {
            $from = array();
            $from[\Chamilo\Libraries\Mail\Mail :: NAME] = $email->get_from_address_name();
            $from[\Chamilo\Libraries\Mail\Mail :: EMAIL] = $email->get_from_address();
            
            $mail = \Chamilo\Libraries\Mail\Mail :: factory(
                $email->get_mail_header(), 
                $email->get_mail_content(), 
                $to_email, 
                $from);
            $reply = array();
            $reply[\Chamilo\Libraries\Mail\Mail :: NAME] = $email->get_reply_address_name();
            $reply[\Chamilo\Libraries\Mail\Mail :: EMAIL] = $email->get_reply_address();
            $mail->set_reply($reply);
            
            // Check whether it was sent successfully
            
            if ($mail->send() === FALSE)
            {
                $this->mail_send = false;
                $user_mail->set_status(UserMail :: STATUS_MAIL_NOT_SEND);
            }
            else
            {
                $user_mail->set_status(UserMail :: STATUS_MAIL_SEND);
            }
            $user_mail->create();
        }
        else
        {
            
            $user_mail->set_status(UserMail :: STATUS_MAIL_IN_QUEUE);
            $user_mail->create();
            
            $mail_job = new MailJob();
            $mail_job->set_status(MailJob :: STATUS_NEW);
            $mail_job->set_UUID(0);
            $mail_job->set_publication_mail_tracker_id($user_mail->get_id());
            if (! $mail_job->create())
            {
                $this->mail_send = false;
            }
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE)), 
                Translation :: get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE_PARTICIPANTS, 
                        \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID => Request :: get(
                            \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID))), 
                Translation :: get('ParticipantBrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_BROWSE, 
                        self :: PARAM_PUBLICATION_ID => Request :: get(self :: PARAM_PUBLICATION_ID))), 
                Translation :: get('BrowserComponent')));
    }

    function get_parameters()
    {
        return array(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
    }
}
?>