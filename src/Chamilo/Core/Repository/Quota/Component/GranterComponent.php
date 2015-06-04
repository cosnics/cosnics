<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GranterComponent extends Manager
{

    public function run()
    {
        if (! \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed())
        {
            throw new NotAllowedException();
        }

        $ids = \Chamilo\Libraries\Platform\Session\Request :: get(self :: PARAM_REQUEST_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $request = DataManager :: retrieve(Request :: class_name(), (int) $id);

                if (! \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->is_target_user(
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
                        $calculator = new Calculator($request->get_user());

                        if ($calculator->get_available_allocated_disk_space() > $request->get_quota())
                        {
                            $user = $request->get_user();
                            $user->set_disk_quota($user->get_disk_quota() + $request->get_quota());

                            if ($user->update())
                            {
                                $request->set_decision(Request :: DECISION_GRANTED);
                                $request->set_decision_date(time());

                                if (! $request->update())
                                {
                                    $failures ++;
                                }
                                else
                                {
                                    $this->send_mail($request, $calculator);
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

    public function send_mail(Request $request, Calculator $calculator)
    {
        set_time_limit(3600);

        $recipient = $request->get_user();

        $title = Translation :: get(
            'RequestGrantedMailTitle',
            array(
                'PLATFORM' => PlatformSetting :: get('site_name'),
                'ADDED_QUOTA' => Filesystem :: format_file_size($request->get_quota())));

        $body = Translation :: get(
            'RequestGrantedMailBody',
            array(
                'USER' => $recipient->get_fullname(),
                'PLATFORM' => PlatformSetting :: get('site_name'),
                'ADDED_QUOTA' => Filesystem :: format_file_size($request->get_quota()),
                'QUOTA' => Filesystem :: format_file_size($calculator->get_maximum_user_disk_quota())));

        $mail = Mail :: factory(
            $title,
            $body,
            array(Mail :: NAME => $recipient->get_fullname(), Mail :: EMAIL => $recipient->get_email()),
            array(
                Mail :: NAME => PlatformSetting :: get('administrator_firstname') . '_' .
                     PlatformSetting :: get('administrator_surname'),
                    Mail :: EMAIL => PlatformSetting :: get('administrator_email')));

        $mail->send();
    }
}
