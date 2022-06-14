<?php
namespace Chamilo\Core\Admin\Component;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Support\Diagnoser;

/**
 *
 * @package admin.lib.admin_manager.component
 */

/**
 * Weblcms component displays diagnostics about the system
 */
class DiagnoserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageChamilo');

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getDiagnoser()->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\Support\Diagnoser
     */
    protected function getDiagnoser()
    {
        return $this->getService(Diagnoser::class);
    }

    public function get_breadcrumb_generator(): BreadcrumbGenerator
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
