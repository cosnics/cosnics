<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Home\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if ($this->getUser()->is_platform_admin())
        {
            $this->getSessionUtilities()->register('Chamilo\Core\Home\General', '1');
        }

        return new RedirectResponse($this->getUrlGenerator()->fromParameters());
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
