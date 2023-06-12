<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagerComponent extends Manager
{

    public function run()
    {
        if ($this->getUser()->isPlatformAdmin())
        {
            $session = $this->getSession();

            if ($session->has(self::SESSION_GENERAL_MODE))
            {
                $this->getSession()->remove(self::SESSION_GENERAL_MODE);
            }
            else
            {
                $this->getSession()->set(self::SESSION_GENERAL_MODE, true);
            }
        }

        return new RedirectResponse(
            $this->getUrlGenerator()->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_VIEW_HOME]
            )
        );
    }
}
