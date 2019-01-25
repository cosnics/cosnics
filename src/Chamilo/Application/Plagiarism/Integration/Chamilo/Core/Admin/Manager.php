<?php

namespace Chamilo\Application\Plagiarism\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $links = array();

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Plagiarism\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Application\Plagiarism\Manager::ACTION_TURNITIN_MANAGE_WEBHOOK
            )
        );

        $links[] = new DynamicAction(
            Translation::get('ManageWebhook'),
            Translation::get('ManageWebhookDescription'),
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'),
            $redirect->getUrl()
        );

        return new Actions(\Chamilo\Application\Plagiarism\Manager::context(), $links);
    }
}
