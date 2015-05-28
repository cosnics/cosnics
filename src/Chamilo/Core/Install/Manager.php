<?php
namespace Chamilo\Core\Install;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * $Id: install_manager.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager
 */
/**
 * An install manager provides some functionalities to the end user to install his Chamilo platform
 *
 * @author Hans De Bisschop
 */
abstract class Manager extends Application implements NoContextComponent
{
    const APPLICATION_NAME = 'install';
    const DEFAULT_ACTION = self :: ACTION_INSTALL_PLATFORM;

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_INSTALL_PLATFORM = 'installer';

    /**
     * Property of this repository manager.
     */
    private $breadcrumbs;

    /**
     * Constructor
     *
     * @param $user_id int The user id of current user
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "7200");
        parent :: __construct($applicationConfiguration);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header()
    {
        $page = Page :: getInstance();
        $page->setApplication($this);

        return $page->getHeader()->toHtml();
    }
}
