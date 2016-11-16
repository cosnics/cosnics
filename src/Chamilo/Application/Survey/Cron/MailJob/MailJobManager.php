<?php
namespace Chamilo\Application\Survey\Cron\MailJob;

use Chamilo\Application\Survey\Cron\Storage\DataClass\MailJob;
use Chamilo\Application\Survey\Cron\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");
class MailJobManager
{
    const TYPE_ALL_MAILS_SEND = 1;
    const TYPE_NOT_ALL_MAILS_SEND = 2;

    static function launch_job()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(MailJob::PROPERTY_UUID, '0');
        $conditions[] = new EqualityCondition(MailJob::PROPERTY_STATUS, MailJob::STATUS_NEW);
        $condition = new AndCondition($conditions);
        $mail_jobs = DataManager::retrieve_mail_jobs($condition);
        
        $UUID = uniqid($_SERVER['SERVER_ADDR'], true);
        
        echo '  UUID=' . $UUID . "\n";
        
        while ($mail_job = $mail_jobs->next_result())
        {
            $mail_job->set_UUID($UUID);
            $mail_job->update();
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(MailJob::PROPERTY_UUID, $UUID);
        $conditions[] = new EqualityCondition(MailJob::PROPERTY_STATUS, MailJob::STATUS_NEW);
        $condition = new AndCondition($conditions);
        $mail_jobs = DataManager::retrieve_mail_jobs($condition);
        
        $user_ids = array();
        $user_publication_ids = array();
        $publication_failures = array();
        $mail_count = 0;
        $mail_failure_count = 0;
        $job_count = 0;
        
        while ($mail_job = $mail_jobs->next_result())
        {
            $job_count ++;
            $mail_tracker = DataManager::retrieve_by_id(
                \Chamilo\Application\Survey\Mail\Storage\DataClass\Mail::class_name(), 
                $mail_job->get_publication_mail_tracker_id());
            
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user($mail_tracker->get_user_id());
            $to_email = $user->get_email();
            
            $publication_mail = DataManager::retrieve_survey_publication_mail(
                $mail_tracker->get_survey_publication_mail_id());
            $user_ids[] = $publication_mail->get_sender_user_id();
            
            $mail = new Mail(
                $publication_mail->get_mail_header(), 
                $publication_mail->get_mail_content(), 
                $to_email, 
                true, 
                array(), 
                array(), 
                $publication_mail->get_from_address_name(), 
                $publication_mail->get_from_address(), 
                $publication_mail->get_reply_address_name(), 
                $publication_mail->get_reply_address());
            
            $mailerFactory = new MailerFactory(Configuration::getInstance());
            $mailer = $mailerFactory->getActiveMailer();
            
            $user_publication_ids[$publication_mail->get_sender_user_id()][] = $publication_mail->get_publication_id();
            
            try
            {
                $mailer->sendMail($mail);
                
                $mail_count ++;
                $mail_tracker->set_status(\Chamilo\Application\Survey\Mail\Storage\DataClass\UserMail::STATUS_MAIL_SEND);
                echo '    Mail send to: ' . $user->get_fullname() . ' ' . $to_email . "\n";
            }
            catch (\Exception $ex)
            {
                $mail_failure_count ++;
                $publication_failures[$publication_mail->get_publication_id()] = $publication_mail->get_sender_user_id();
                $mail_tracker->set_status(
                    \Chamilo\Application\Survey\Mail\Storage\DataClass\UserMail::STATUS_MAIL_NOT_SEND);
                echo '    Mail not send to: ' . $user->get_fullname() . ' ' . $to_email . "\n";
            }
            
            $mail_tracker->update();
            $mail_job->set_status(MailJob::STATUS_DONE);
            $mail_job->update();
        }
        
        $user_ids = array_unique($user_ids);
        $user_failures = array_unique($user_failures);
        
        echo '  COUNT senders:  ' . count($user_ids) . "\n";
        echo '  	JOB COUNT :  ' . $job_count . "\n";
        echo '  		COUNT mails send:  ' . $mail_count . "\n";
        echo '  		COUNT mail send failures:  ' . $mail_failure_count . "\n";
        
        if (count($user_ids) > 0)
        {
            foreach ($user_ids as $user_id)
            {
                $ids = $user_publication_ids[$user_id];
                $publication_ids = array_unique($ids);
                
                foreach ($publication_ids as $publication_id)
                {
                    if (in_array($user_id, $publication_failures[$publication_id]))
                    {
                        MailJobManager::send_mail(
                            $user_id, 
                            MailJobManager::get_mail_message(self::TYPE_NOT_ALL_MAILS_SEND, $publication_id));
                    }
                    else
                    {
                        MailJobManager::send_mail(
                            $user_id, 
                            MailJobManager::get_mail_message(self::TYPE_ALL_MAILS_SEND, $publication_id));
                    }
                }
            }
        }
    }

    static function send_mail($user_id, $message)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user($user_id);
        $to_email = $user->get_email();
        
        $configuration = Configuration::getInstance();
        
        $name = $configuration->get_setting(array('Chamilo\Core\Admin', 'administrator_firstname')) . ' ' .
             $configuration->get_setting(array('Chamilo\Core\Admin', 'administrator_surname'));
        $email = $configuration->get_setting(array('Chamilo\Core\Admin', 'administrator_email'));
        
        $mail = new Mail(Translation::get('MailHeader'), $message, $to_email, true, array(), array(), $name, $email, $name, $email);
        
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();
        
        try
        {
            $mailer->sendMail($mail);
            
            echo '    			Notification Mail send to: ' . $user->get_fullname() . ' ' . $to_email . "\n";
        }
        catch (\Exception $ex)
        {
            echo '    			Notification Mail not send to: ' . $user->get_fullname() . ' ' . $to_email . "\n";
        }
    }

    static function get_mail_message($type, $publication_id)
    {
        $message = array();
        
        $publication = DataManager::retrieve_survey_publication($publication_id);
        
        $click_message = Translation::get('ClickToGoToMailManager');
        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = \Chamilo\Application\Survey\Manager::package();
        $parameters[\Chamilo\Application\Survey\Manager::PARAM_ACTION] = \Chamilo\Application\Survey\Manager::ACTION_MAIL_INVITEES;
        $parameters[\Chamilo\Application\Survey\Manager::PARAM_PUBLICATION_ID] = $publication_id;
        
        $redirect = new Redirect($parameters);
        $url = $redirect->getUrl();
        
        switch ($type)
        {
            
            case self::TYPE_ALL_MAILS_SEND :
                $message[] = Translation::get("AllMailsSend");
                break;
            case self::TYPE_NOT_ALL_MAILS_SEND :
                $message[] = Translation::get("NotAllMailsSend");
                break;
        }
        
        $message[] = '<br/><br/>';
        $message[] = Translation::get('Title') . ': ';
        $message[] = '<br/>';
        $message[] = $publication->get_title();
        $message[] = '<br/><br/>';
        $message[] = Translation::get('Description') . ': ';
        $message[] = '<br/>';
        $message[] = $publication->getContentObject()->get_description();
        $message[] = '<br/><br/>';
        $message[] = '<a href=' . $url . '>' . $click_message . '</a>';
        
        $message[] = '<br/><br/>' . Translation::get('OrCopyAndPasteThisText') . ':';
        $message[] = '<br/><a href=' . $url . '>' . $url . '</a>';
        $message[] = '</p>';
        
        return implode(PHP_EOL, $message);
    }
}

?>