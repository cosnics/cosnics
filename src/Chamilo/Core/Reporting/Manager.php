<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * $Id: reporting_manager.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 *
 * @package reporting.lib.reporting_manager
 * @author Michael Kyndt
 */

/**
 * A reporting manager provides some functionalities to the admin to manage the reporting
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'reporting';

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::__construct()
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null)
    {
        parent :: __construct($request, $user, $application);

        Page :: getInstance()->setSection('Chamilo\Core\Admin');
    }
}
