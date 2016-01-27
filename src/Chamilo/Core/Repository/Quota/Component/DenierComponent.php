<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Core\Repository\Quota\Form\RequestForm;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Mail\Mail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DenierComponent extends Manager
{

    public function run()
    {
        if (! \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed())
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->get(self :: PARAM_REQUEST_ID);

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

    public function single_deny($id)
    {
        $request = DataManager :: retrieve_by_id(Request :: class_name(), (int) $id);

        if (! \Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->is_target_user(
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
            $form->freeze(array('quota_step', Request :: PROPERTY_QUOTA, Request :: PROPERTY_MOTIVATION));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->get_action_bar()->as_html();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function multiple_denies($ids)
    {
        $failures = 0;

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

    public function send_mail(Request $request)
    {
        set_time_limit(3600);

        $recipient = $request->get_user();

        $title = Translation :: get(
            'RequestDeniedMailTitle',
            array(
                'PLATFORM' => PlatformSetting :: get('site_name'),
                'QUOTA' => Filesystem :: format_file_size($request->get_quota())));

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
                'QUOTA' => Filesystem :: format_file_size($request->get_quota()),
                'MOTIVATION' => $request->get_decision_motivation()));

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

    public function get_action_bar()
    {
        $calculator = new Calculator($this->get_user());
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $allow_upgrade = PlatformSetting :: get('allow_upgrade', __NAMESPACE__);
        $maximum_user_disk_space = PlatformSetting :: get('maximum_user', __NAMESPACE__);

        if ($calculator->upgradeAllowed())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('UpgradeQuota'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Upgrade'),
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPGRADE))));
        }

        if ($calculator->requestAllowed())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('RequestUpgrade'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Request'),
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE))));
        }

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('BackToOverview'),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Browser'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE))));

        return $action_bar;
    }
}
