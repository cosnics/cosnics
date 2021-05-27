<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Request\Form\RequestForm;
use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

class DenierComponent extends Manager
{

    function run()
    {
        if (! Rights::getInstance()->request_is_allowed())
        {
            throw new NotAllowedException();
        }
        
        $ids = $this->getRequest()->get(self::PARAM_REQUEST_ID);
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                return $this->single_deny($ids);
            }
            else
            {
                return $this->multiple_denies($ids);
            }
        }
        
        return $this->display_error_page(
            htmlentities(
                Translation::get(
                    'NoObjectSelected', 
                    array('OBJECT' => Translation::get('Request')), 
                    Utilities::COMMON_LIBRARIES)));
    }

    function single_deny($id)
    {
        $request = DataManager::retrieve_by_id(Request::class, (int) $id);
        
        if (! Rights::getInstance()->is_target_user(
            $this->get_user(), 
            $request->get_user_id()) && ! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $failures = 0;
        
        $form = new RequestForm(
            $request, 
            $this->get_url(array(self::PARAM_ACTION => self::ACTION_DENY, self::PARAM_REQUEST_ID => $request->get_id())));
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            
            $request->set_decision(Request::DECISION_DENIED);
            $request->set_decision_date(time());
            $request->set_decision_motivation($values[Request::PROPERTY_DECISION_MOTIVATION]);
            
            if (! $request->update())
            {
                $failures ++;
            }
            else
            {
                $this->send_mail($request);
            }
            
            return $this->redirectAfterDenyAction($failures, array($id));
        }
        
        $form->freeze(
            array(
                Request::PROPERTY_COURSE_TYPE_ID, 
                Request::PROPERTY_NAME, 
                Request::PROPERTY_SUBJECT, 
                Request::PROPERTY_MOTIVATION));
        
        $html = [];
        
        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    function multiple_denies($ids)
    {
        $failures = 0;
        
        foreach ($ids as $id)
        {
            $request = DataManager::retrieve_by_id(Request::class, (int) $id);
            
            if (! Rights::getInstance()->is_target_user(
                $this->get_user(), 
                $request->get_user_id()) && ! $this->get_user()->is_platform_admin())
            {
                $failures ++;
            }
            else
            {
                if (! $request->is_pending())
                {
                    $failures ++;
                }
                else
                {
                    $request->set_decision(Request::DECISION_DENIED);
                    $request->set_decision_date(time());
                    
                    if (! $request->update())
                    {
                        $failures ++;
                    }
                    else
                    {
                        $this->send_mail($request);
                    }
                }
            }
        }
        
        $this->redirectAfterDenyAction($failures, $ids);
    }

    /**
     * Sends a mail of the deny action
     * 
     * @param Request $request
     *
     * @throws \Exception
     */
    protected function send_mail(Request $request)
    {
        set_time_limit(3600);
        
        $recipient = $request->get_user();
        
        $siteName = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));
        
        $title = Translation::get(
            'RequestDeniedMailTitle', 
            array('PLATFORM' => $siteName, 'NAME' => $request->get_name()));
        
        if (strlen($request->get_decision_motivation()) > 0)
        {
            $variable = 'RequestDeniedMailBody';
        }
        else
        {
            $variable = 'RequestDeniedMailBodySimple';
        }
        
        $body = Translation::get(
            $variable, 
            array(
                'USER' => $recipient->get_fullname(), 
                'PLATFORM' => $siteName, 
                'NAME' => $request->get_name(), 
                'DENIER' => $this->get_user()->get_fullname(), 
                'MOTIVATION' => $request->get_decision_motivation()));
        
        $mail = new Mail($title, $body, $recipient->get_email());
        
        $mailerFactory = new MailerFactory(Configuration::getInstance());
        $mailer = $mailerFactory->getActiveMailer();
        
        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception $ex)
        {
        }
    }

    /**
     * Redirects the user to the list of requests after the requests have been denied
     * 
     * @param int $failureCount
     * @param int[]   $ids
     */
    protected function redirectAfterDenyAction($failureCount, $ids)
    {
        if ($failureCount)
        {
            if (count($ids) == 1)
            {
                $message = 'DecisionNotDenied';
            }
            elseif (count($ids) > $failureCount)
            {
                $message = 'SomeDecisionsNotDenied';
            }
            else
            {
                $message = 'DecisionsNotDenied';
            }
        }
        else
        {
            if (count($ids) == 1)
            {
                $message = 'DecisionDenied';
            }
            else
            {
                $message = 'ObjectsDenied';
            }
        }
        
        $this->redirect(
            Translation::get($message, [], Manager::context()),
            ($failureCount ? true : false), 
            array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}