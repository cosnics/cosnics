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

        $html = array();
        $diagnoser = new Diagnoser($this);

        $html[] = $this->render_header();
        $html[] = $diagnoser->to_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('admin_diagnoser');
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }
}
