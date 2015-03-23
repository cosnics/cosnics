<?php
namespace Chamilo\Application\Survey\Mail\Component;

use Chamilo\Application\Survey\Mail\Form\MailTestForm;
use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ManagerTestMailComponent extends Manager
{

    private $invitees;

    private $reporting_users;

    private $not_started;

    private $started;

    private $finished;

    private $publication_id;

    private $survey_id;

    function run()
    {
        $this->publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        
        if (! Rights :: get_instance()->is_right_granted(Rights :: INVITE_RIGHT, $this->publication_id))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = new MailTestForm(
            $this, 
            $this->get_user(), 
            $this->get_url(array(self :: PARAM_PUBLICATION_ID => $this->publication_id)));
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            $this->parse_values($values);
        }
        else
        {
            $this->display_header();
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function parse_values($values)
    {
        $email_header = $values[MailTestForm :: EMAIL_HEADER];
        $email_content = $values[MailTestForm :: EMAIL_CONTENT];
        $email_from_address = $values[MailTestForm :: FROM_ADDRESS];
        $email_reply_address = $values[MailTestForm :: REPLY_ADDRESS];
        $email_to_address = $values[MailTestForm :: TO_ADDRESS];
        $email_from_address_name = $values[MailTestForm :: FROM_ADDRESS_NAME];
        $email_reply_address_name = $values[MailTestForm :: REPLY_ADDRESS_NAME];
        $email_to_address_name = $values[MailTestForm :: TO_ADDRESS_NAME];
        $email_count = $values[MailTestForm :: EMAILCOUNT];
        $email_asked_count = $email_count;
        $index = 1;
        
        $meta_logs = array();
        $initial_start = time();
        $meta_logs['start'] = date('D, d M Y H:i:s', $initial_start);
        $failed_count = 0;
        while ($email_count > 0)
        {
            $email_count --;
            
            $from = array();
            $from[Mail :: NAME] = $email_from_address_name;
            $from[Mail :: EMAIL] = $email_from_address;
            
            $mail = Mail :: factory($email_header, $email_content, $email_to_address, $from);
            
            $reply = array();
            $reply[Mail :: NAME] = $email_reply_address_name;
            $reply[Mail :: EMAIL] = $email_reply_address;
            $mail->set_reply($reply);
            
            $logs = array();
            $start = time();
            $logs['count'] = $index ++;
            $logs['start'] = date('D, d M Y H:i:s', $start);
            
            // Check whether it was sent successfully
            if ($mail->send() === FALSE)
            {
                $mail_send = false;
                $logs['mail send'] = 'false';
                $failed_count ++;
            }
            else
            {
                
                $logs['mail send'] = 'false';
            }
            $end = time();
            $logs['end'] = date('D, d M Y H:i:s', $end);
            $logs['time'] = $end - $start . ' secs';
        }
        
        $end = time();
        $meta_logs['end'] = date('D, d M Y H:i:s', $end);
        $meta_logs['time'] = $end - $initial_start . ' secs';
        $meta_logs['mails asked'] = $email_asked_count;
        $meta_logs['failed'] = $failed_count;
        $meta_logs['succeed'] = $email_asked_count - $failed_count;
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