<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Form\RequestForm;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

class CreatorComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $calculator;

    public function run()
    {
        $this->calculator = new Calculator($this->get_user());

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        if (!$this->calculator->requestAllowed())
        {
            throw new NotAllowedException();
        }

        $request = new Request();
        $request->set_user_id($this->get_user_id());

        $form = new RequestForm($request, $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)));

        if ($form->validate())
        {
            $values = $form->exportValues();

            $request->set_quota($values[Request::PROPERTY_QUOTA] * pow(1024, 2));
            $request->set_motivation($values[Request::PROPERTY_MOTIVATION]);
            $request->set_decision(Request::DECISION_PENDING);
            $request->set_creation_date(time());

            $success = $request->create();

            // If the request was successfully created, send an e-mail to the people who can actually grant or deny it.
            if ($success)
            {
                $authorized_users = $this->getRightsService()->getAuthorizedUsersForUser($this->getUser());

                set_time_limit(3600);

                $title = Translation::get(
                    'QuotaCreatedMailTitle', array(
                        'PLATFORM' => Configuration::getInstance()->get_setting(
                            array('Chamilo\Core\Admin', 'site_name')
                        )
                    )
                );

                $mailerFactory = new MailerFactory(Configuration::getInstance());
                $mailer = $mailerFactory->getActiveMailer();

                foreach ($authorized_users as $authorized_user)
                {
                    $mail = new Mail(
                        $title, Translation::get(
                        'QuotaCreatedMailBody', array(
                            'USER' => $authorized_user->get_fullname(),
                            'PLATFORM' => Configuration::getInstance()->get_setting(
                                array('Chamilo\Core\Admin', 'site_name')
                            )
                        )
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

            $this->redirect(
                Translation::get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated', array('OBJECT' => Translation::get('Request')),
                    Utilities::COMMON_LIBRARIES
                ), ($success ? false : true), $parameters
            );
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $allow_upgrade = Configuration::getInstance()->get_setting(
                array('Chamilo\Core\Repository', 'allow_upgrade')
            );
            $maximum_user_disk_space = Configuration::getInstance()->get_setting(
                array('Chamilo\Core\Repository', 'maximum_user')
            );

            if ($this->calculator->upgradeAllowed())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('UpgradeQuota'), new FontAwesomeGlyph('angle-double-up', [], null, 'fas'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_UPGRADE))
                    )
                );
            }

            $toolActions->addButton(
                new Button(
                    Translation::get('BackToOverview'), new FontAwesomeGlyph('folder', [], null, 'fas'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE))
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }
}
