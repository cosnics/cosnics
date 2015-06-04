<?php
namespace Chamilo\Core\Tracking\Component;

use Chamilo\Core\Tracking\Archive\ArchiveWizard;
use Chamilo\Core\Tracking\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: archiver.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Tracking Manager Archiver component which allows the administrator to archive the trackers
 *
 * @author Sven Vanpoucke
 */
class ArchiverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $wizard = new ArchiveWizard($this);
        $wizard->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('tracking_archiver');
    }
}
