<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Form\RequestForm;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Core\Repository\Quota\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DenierComponent extends Manager
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        if (!$this->getRightsService()->canUserViewQuotaRequests($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_REQUEST_ID);

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $failures = $this->single_deny($ids);
                $ids = [$ids];
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
                    $parameter = ['OBJECT' => Translation::get('Request')];
                }
                elseif (count($ids) > $failures)
                {
                    $message = 'SomeObjectsNotDenied';
                    $parameter = ['OBJECTS' => Translation::get('Requests')];
                }
                else
                {
                    $message = 'ObjectsNotDenied';
                    $parameter = ['OBJECTS' => Translation::get('Requests')];
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDenied';
                    $parameter = ['OBJECT' => Translation::get('Request')];
                }
                else
                {
                    $message = 'ObjectsDenied';
                    $parameter = ['OBJECTS' => Translation::get('Requests')];
                }
            }

            $this->redirectWithMessage(
                Translation::get($message, $parameter, StringUtilities::LIBRARIES), (bool) $failures,
                [self::PARAM_ACTION => self::ACTION_BROWSE]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', ['OBJECT' => Translation::get('Request')], StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function getButtonToolbarRenderer()
    {
        $calculator = new Calculator($this->get_user());
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $allow_upgrade = Configuration::getInstance()->get_setting(
                ['Chamilo\Core\Repository', 'allow_upgrade']
            );
            $maximum_user_disk_space = Configuration::getInstance()->get_setting(
                ['Chamilo\Core\Repository', 'maximum_user']
            );

            if ($calculator->upgradeAllowed())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('UpgradeQuota'), new FontAwesomeGlyph('angle-double-up', [], null, 'fas'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_UPGRADE])
                    )
                );
            }

            if ($calculator->requestAllowed())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('RequestUpgrade'), new FontAwesomeGlyph('question-circle', [], null, 'fas'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE])
                    )
                );
            }

            $toolActions->addButton(
                new Button(
                    Translation::get('BackToOverview'), new FontAwesomeGlyph('folder', [], null, 'fas'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE])
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function multiple_denies($ids)
    {
        $failures = 0;

        foreach ($ids as $id)
        {
            $request = DataManager::retrieve(Request::class, (int) $id);

            if (!$this->getRightsService()->isUserIdentifierTargetForUser($request->get_user_id(), $this->getUser()))
            {
                $failures ++;
            }
            else
            {
                if (!$request->is_pending())
                {
                    $failures ++;
                }
                else
                {
                    $request->set_decision(Request::DECISION_DENIED);
                    $request->set_decision_date(time());

                    if (!$request->update())
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

        $title = Translation::get(
            'RequestDeniedMailTitle', [
                'PLATFORM' => Configuration::getInstance()->get_setting(['Chamilo\Core\Admin', 'site_name']),
                'QUOTA' => Filesystem::format_file_size($request->get_quota())
            ]
        );

        if (strlen($request->get_decision_motivation()) > 0)
        {
            $variable = 'RequestDeniedMailBody';
        }
        else
        {
            $variable = 'RequestDeniedMailBodySimple';
        }

        $body = Translation::get(
            $variable, [
                'USER' => $recipient->get_fullname(),
                'PLATFORM' => $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'site_name']),
                'QUOTA' => Filesystem::format_file_size($request->get_quota()),
                'MOTIVATION' => $request->get_decision_motivation()
            ]
        );

        $mail = new Mail($title, $body, $recipient->get_email());

        $mailer = $this->getActiveMailer();

        try
        {
            $mailer->sendMail($mail);
        }
        catch (Exception $ex)
        {
        }
    }

    public function single_deny($id)
    {
        $request = DataManager::retrieve_by_id(Request::class, (int) $id);

        if (!$this->getRightsService()->isUserIdentifierTargetForUser($request->get_user_id(), $this->getUser()))
        {
            throw new NotAllowedException();
        }

        $failures = 0;

        $form = new RequestForm(
            $request, $this->get_url(
            [self::PARAM_ACTION => self::ACTION_DENY, self::PARAM_REQUEST_ID => $request->get_id()]
        )
        );

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        if ($form->validate())
        {
            $values = $form->exportValues();

            $request->set_decision(Request::DECISION_DENIED);
            $request->set_decision_date(time());
            $request->set_decision_motivation($values[Request::PROPERTY_DECISION_MOTIVATION]);

            if (!$request->update())
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
            $form->freeze(['quota_step', Request::PROPERTY_QUOTA, Request::PROPERTY_MOTIVATION]);

            $html = [];

            $html[] = $this->render_header();
            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
