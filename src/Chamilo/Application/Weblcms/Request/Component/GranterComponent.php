<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GranterComponent extends Manager
{

    function run()
    {
        if (! \Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->request_is_allowed())
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->get(self :: PARAM_REQUEST_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

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

                    if ($request->was_granted())
                    {
                        $failures ++;
                    }
                    else
                    {
                        $course = new Course();

                        $course->set_title($request->get_name());
                        $course->set_course_type_id($request->get_course_type_id());
                        $course->set_visual_code(strtoupper(uniqid()));
                        $course->set_titular_id($request->get_user_id());
                        $course->set_category_id($request->get_category_id());

                        if ($course->create())
                        {
                            $course_settings = array();
                            $course_settings[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: TITULAR] = $course->get_titular_id();

                            if ($course->create_course_settings_from_values($course_settings, true))
                            {
                                if (CourseManagementRights :: get_instance()->create_rights_from_values(
                                    $course,
                                    array()))
                                {
                                    if (\Chamilo\Application\Weblcms\Course\Storage\DataManager :: subscribe_user_to_course(
                                        $course->get_id(),
                                        '1',
                                        $request->get_user_id()))
                                    {
                                        $request->set_decision(Request :: DECISION_GRANTED);
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
                                    else
                                    {
                                        $failures ++;
                                    }
                                }
                                else
                                {
                                    $failures ++;
                                }
                            }
                            else
                            {
                                $failures ++;
                            }
                        }
                        else
                        {
                            $failures ++;
                        }
                    }
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotGranted';
                    $parameter = array('OBJECT' => Translation :: get('Request'));
                }
                elseif (count($ids) > $failures)
                {
                    $message = 'SomeObjectsNotGranted';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
                else
                {
                    $message = 'ObjectsNotGranted';
                    $parameter = array('OBJECTS' => Translation :: get('Requests'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectGranted';
                    $parameter = array('OBJECT' => Translation :: get('Request'));
                }
                else
                {
                    $message = 'ObjectsGranted';
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

    function send_mail(Request $request)
    {
        set_time_limit(3600);

        $recipient = $request->get_user();

        $title = Translation :: get(
            'RequestGrantedMailTitle',
            array('PLATFORM' => PlatformSetting :: get('site_name'), 'NAME' => $request->get_name()));

        $body = Translation :: get(
            'RequestGrantedMailBody',
            array(
                'USER' => $recipient->get_fullname(),
                'PLATFORM' => PlatformSetting :: get('site_name'),
                'NAME' => $request->get_name()));

        $mail = new Mail($title, $body, $recipient->get_email());

        $mailerFactory = new MailerFactory(Configuration::get_instance());
        $mailer = $mailerFactory->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (\Exception $ex)
        {
        }
    }
}