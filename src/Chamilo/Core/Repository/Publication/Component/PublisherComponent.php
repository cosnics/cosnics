<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Wizard\PublisherWizard;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * $Id: publisher.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class PublisherComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $wizard = new PublisherWizard($this);
        return $wizard->run();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_publisher');
    }
}
