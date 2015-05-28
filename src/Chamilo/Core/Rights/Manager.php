<?php
namespace Chamilo\Core\Rights;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * $Id: rights_manager.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 *
 * @package rights.lib.rights_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'rights';
    const ACTION_BROWSE = 'Browser';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::__construct()
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent :: __construct($applicationConfiguration);

        Page :: getInstance()->setSection('Chamilo\Core\Admin');
    }
}
