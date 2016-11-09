<?php
namespace Chamilo\Application\Survey\Mail\Component;

use Chamilo\Application\Survey\Mail\Form\MailTestForm;
use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Session\Request;

class TestMailComponent extends Manager
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
        $this->publication_id = Request:: get(Manager :: PARAM_PUBLICATION_ID);

//         if (! Rights :: getInstance()->is_right_granted(Rights :: INVITE_RIGHT, $this->publication_id))
//         {
//             throw new NotAllowedException();
//         }

        $form = new MailTestForm(
            $this,
            $this->get_user(),
            $this->get_url(array(self :: PARAM_PUBLICATION_ID => $this->publication_id))
        );

        if ($form->validate())
        {
            $values = $form->exportValues();
            $this->parse_values($values);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
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

            $logs = array();
            $start = time();
            $logs['count'] = $index ++;
            $logs['start'] = date('D, d M Y H:i:s', $start);

            $mail = new Mail(
                $email_header, $email_content, $email_to_address, true, array(), array(), $email_from_address_name,
                $email_from_address, $email_reply_address_name, $email_reply_address
            );

            $mailerFactory = new MailerFactory(Configuration::getInstance());
            $mailer = $mailerFactory->getActiveMailer();

            try
            {
                $mailer->sendMail($mail);

                $logs['mail send'] = 'true';
            }
            catch (\Exception $ex)
            {
                $mail_send = false;
                $logs['mail send'] = 'false';
                $failed_count ++;
            }

            $end = time();
            $logs['end'] = date('D, d M Y H:i:s', $end);
            $logs['time'] = $end - $start . ' secs';
            var_dump($logs);
        }

        $end = time();
        $meta_logs['end'] = date('D, d M Y H:i:s', $end);
        $meta_logs['time'] = $end - $initial_start . ' secs';
        $meta_logs['mails asked'] = $email_asked_count;
        $meta_logs['failed'] = $failed_count;
        $meta_logs['succeed'] = $email_asked_count - $failed_count;

        var_dump($meta_logs);
    }

//     function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
//     {
//         $breadcrumbtrail->add(
//             new Breadcrumb(
//                 $this->get_url(
//                     array(
//                         \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE)), 
//                 Translation :: get('BrowserComponent')));
//         $breadcrumbtrail->add(
//             new Breadcrumb(
//                 $this->get_url(
//                     array(
//                         \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE_PARTICIPANTS, 
//                         \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID => Request :: get(
//                             \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID))), 
//                 Translation :: get('ParticipantBrowserComponent')));
//         $breadcrumbtrail->add(
//             new Breadcrumb(
//                 $this->get_url(
//                     array(
//                         self :: PARAM_ACTION => self :: ACTION_BROWSE, 
//                         self :: PARAM_PUBLICATION_ID => Request :: get(self :: PARAM_PUBLICATION_ID))), 
//                 Translation :: get('BrowserComponent')));
//     }
}

?>