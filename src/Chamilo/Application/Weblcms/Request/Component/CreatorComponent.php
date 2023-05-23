<?php
namespace Chamilo\Application\Weblcms\Request\Component;

use Chamilo\Application\Weblcms\Request\Form\RequestForm;
use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Rights\Rights;
use Chamilo\Application\Weblcms\Request\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

class CreatorComponent extends Manager
{

    public function run()
    {
        if (!$this->request_allowed())
        {
            throw new NotAllowedException();
        }

        $request = new Request();
        $request->set_user_id($this->get_user_id());

        $form = new RequestForm($request, $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE]));

        if ($form->validate())
        {
            $values = $form->exportValues();

            $request->set_name($values[Request::PROPERTY_NAME]);
            $request->set_course_type_id($values[Request::PROPERTY_COURSE_TYPE_ID]);
            $request->set_subject($values[Request::PROPERTY_SUBJECT]);
            $request->set_motivation($values[Request::PROPERTY_MOTIVATION]);
            $request->set_decision(Request::DECISION_PENDING);
            $request->set_creation_date(time());
            $request->set_category_id($values[Request::PROPERTY_CATEGORY_ID]);

            $success = $request->create();

            // If the request was successfully created, send an e-mail to the people who can actually grant or deny it.
            if ($success)
            {
                $authorized_users = Rights::getInstance()->get_authorized_users(
                    $this->get_user()
                );

                set_time_limit(3600);

                $siteName = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'site_name']);

                $title = Translation::get('RequestCreatedMailTitle', ['PLATFORM' => $siteName]);

                $mailer = $this->getActiveMailer();

                foreach ($authorized_users as $authorized_user)
                {
                    $mail = new Mail(
                        $title, Translation::get(
                        'RequestCreatedMailBody', ['USER' => $authorized_user->get_fullname(), 'PLATFORM' => $siteName]
                    ), $authorized_user->get_email()
                    );

                    try
                    {
                        $mailer->sendMail($mail);
                    }
                    catch (Exception $ex)
                    {
                    }
                }
            }

            $parameters = [];
            $parameters[self::PARAM_ACTION] = self::ACTION_BROWSE;

            $this->redirectWithMessage(
                Translation::get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated', ['OBJECT' => Translation::get('Request')],
                    StringUtilities::LIBRARIES
                ), !$success, $parameters
            );
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}