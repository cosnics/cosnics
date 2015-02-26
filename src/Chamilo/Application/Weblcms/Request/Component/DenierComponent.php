<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Request\Form\RequestForm;
use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DenierComponent extends Manager
{

    function run()
    {
        if (! \Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->request_is_allowed())
        {
            throw new NotAllowedException();
        }

        $ids = \Chamilo\Libraries\Platform\Session\Request :: get(self :: PARAM_REQUEST_ID);

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $failures = $this->single_deny($ids);
                $ids = array($ids);
            }
            else
            {
                $failures = $this->multiple_denies($ids);
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDenied';
                    $parameter = array('OBJECT' => Translation :: get('Request'));
                }
                elseif (count($ids) > $failures)
                {
                    $message = 'SomeObjectsNotDenied';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
                else
                {
                    $message = 'ObjectsNotDenied';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDenied';
                    $parameter = array('OBJECT' => Translation :: get('Request'));
                }
                else
                {
                    $message = 'ObjectsDenied';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
            }

            $this->redirect(
                Translation :: get($message, $parameter, Utilities :: COMMON_LIBRARIES),
                ($failures ? true : false),
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('Request')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    function single_deny($id)
    {
        $request = DataManager :: retrieve_by_id(Request :: class_name(), (int) $id);

        if (! \Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->is_target_user(
            $this->get_user(),
            $request->get_user_id()) && ! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $failures = 0;

        $form = new RequestForm(
            $request,
            $this->get_url(
                array(self :: PARAM_ACTION => self :: ACTION_DENY, self :: PARAM_REQUEST_ID => $request->get_id())));

        if ($form->validate())
        {
            $values = $form->exportValues();

            $request->set_decision(Request :: DECISION_DENIED);
            $request->set_decision_date(time());
            $request->set_decision_motivation($values[Request :: PROPERTY_DECISION_MOTIVATION]);

            if (! $request->update())
            {
                $failures ++;
            }
            else
            {
                $this->send_mail($request);
            }

            return $failures;
        }
        else
        {
            $form->freeze(
                array(
                    Request :: PROPERTY_COURSE_TYPE_ID,
                    Request :: PROPERTY_NAME,
                    Request :: PROPERTY_SUBJECT,
                    Request :: PROPERTY_MOTIVATION));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    function multiple_denies($ids)
    {
        $failures = 0;

        foreach ($ids as $id)
        {
            $request = DataManager :: retrieve_by_id(Request :: class_name(), (int) $id);

            if (! \Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->is_target_user(
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
                    $request->set_decision(Request :: DECISION_DENIED);
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

        return $failures;
    }

    function send_mail(Request $request)
    {
        set_time_limit(3600);

        $recipient = $request->get_user();

        $title = Translation :: get(
            'RequestDeniedMailTitle',
            array('PLATFORM' => PlatformSetting :: get('site_name'), 'NAME' => $request->get_name()));

        if (strlen($request->get_decision_motivation()) > 0)
        {
            $variable = 'RequestDeniedMailBody';
        }
        else
        {
            $variable = 'RequestDeniedMailBodySimple';
        }

        $body = Translation :: get(
            $variable,
            array(
                'USER' => $recipient->get_fullname(),
                'PLATFORM' => PlatformSetting :: get('site_name'),
                'NAME' => $request->get_name(),
                'DENIER' => $this->get_user()->get_fullname(),
                'MOTIVATION' => $request->get_decision_motivation()));

        $mail = Mail :: factory(
            $title,
            $body,
            array($recipient->get_email()),
            array(
                Mail :: NAME => PlatformSetting :: get('administrator_firstname') . '_' .
                     PlatformSetting :: get('administrator_surname'),
                    Mail :: EMAIL => PlatformSetting :: get('administrator_email')));

        $mail->send();
    }
}
?>