<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 *
 * @package reporting.lib.reporting_manager
 * @author Michael Kyndt
 */

/**
 * A reporting manager provides some functionalities to the admin to manage the reporting
 */
abstract class Manager extends Application
{

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }
}
