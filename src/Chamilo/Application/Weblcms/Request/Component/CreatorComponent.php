<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Request\Form\RequestForm;
use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CreatorComponent extends Manager
{

    function run()
    {
        if (! $this->request_allowed())
        {
            throw new NotAllowedException();
        }

        $request = new Request();
        $request->set_user_id($this->get_user_id());

        $form = new RequestForm($request, $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE)));

        if ($form->validate())
        {
            $values = $form->exportValues();

            $request->set_name($values[Request :: PROPERTY_NAME]);
            $request->set_course_type_id($values[Request :: PROPERTY_COURSE_TYPE_ID]);
            $request->set_subject($values[Request :: PROPERTY_SUBJECT]);
            $request->set_motivation($values[Request :: PROPERTY_MOTIVATION]);
            $request->set_decision(Request :: DECISION_PENDING);
            $request->set_creation_date(time());

            $success = $request->create();

            // If the request was successfully created, send an e-mail to the people who can actually grant or deny it.
            if ($success)
            {
                $authorized_users = \Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->get_authorized_users(
                    $this->get_user());

                set_time_limit(3600);

                $title = Translation :: get(
                    'RequestCreatedMailTitle',
                    array('PLATFORM' => PlatformSetting :: get('site_name')));

                $mail = Mail :: factory(
                    $title,
                    '',
                    '',
                    array(
                        Mail :: NAME => PlatformSetting :: get('administrator_firstname') . '_' . PlatformSetting :: get(
                            'administrator_surname'),
                        Mail :: EMAIL => PlatformSetting :: get('administrator_email')));

                foreach ($authorized_users as $authorized_user)
                {

                    $mail->set_message(
                        Translation :: get(
                            'RequestCreatedMailBody',
                            array(
                                'USER' => $authorized_user->get_fullname(),
                                'PLATFORM' => PlatformSetting :: get('site_name'))));

                    $mail->set_to($authorized_user->get_email());
                    $mail->send();
                }
            }

            $parameters = array();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE;

            $this->redirect(
                Translation :: get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated',
                    array('OBJECT' => Translation :: get('Request')),
                    Utilities :: COMMON_LIBRARIES),
                ($success ? false : true),
                $parameters);
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
}
?>