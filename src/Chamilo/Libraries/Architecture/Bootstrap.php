<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package libraries
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Use the new \Chamilo\Libraries\Architecture\Factory\BootstrapFactory now
 */
class Bootstrap
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Bootstrap
     */
    protected static $instance = null;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->initialize();

        $this->request = \Symfony\Component\HttpFoundation\Request :: createFromGlobals();
        $this->configuration = \Chamilo\Configuration\Configuration :: get_instance();
    }

    /**
     * Return 'this' as singleton
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            self :: $instance = new static();
        }

        return static :: $instance;
    }

    /**
     * Include the composer autoloader which also handles the autoloading of the Chamilo codebase
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap
     */
    private function initialize()
    {
        $autoload_file = realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';
        if (is_readable($autoload_file))
        {
            require_once $autoload_file;
        }

        return $this;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Check if the system has been installed, if not display message accordingly
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap
     */
    private function checkInstallation()
    {
        if (! $this->getConfiguration()->is_available())
        {
            $this->request->query->set(Application :: PARAM_CONTEXT, 'Chamilo\Core\Install');
            Request :: set_get(Application :: PARAM_CONTEXT, 'Chamilo\Core\Install');
            return $this;
        }

        if (! $this->getConfiguration()->is_connectable())
        {
            throw new \Exception(Translation :: get('DatabaseConnectionNotAvailable'));
        }

        return $this;
    }

    private function startSession()
    {
        // Start session
        Session :: start(true);

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap
     */
    public static function setup()
    {
        return self :: getInstance()->checkInstallation()->startSession();
    }
}