<?php
namespace Chamilo\Libraries\Architecture\Factory;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Libraries\Architecture\Bootstrap\Bootstrap;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\DataSourceNameFactory;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class BootstrapFactory
{

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Include the composer autoloader which also handles the autoloading of the Chamilo codebase
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrapper
     */
    protected function initialize()
    {
        $autoload_file = realpath(__DIR__ . '/../../../../../') . '/vendor/autoload.php';

        if (is_readable($autoload_file))
        {
            require_once $autoload_file;
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Bootstrap
     */
    public function getBootstrap()
    {
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        $fileConfigurationLoader = new FileConfigurationLoader(
            new PathBuilder(new ClassnameUtilities(new StringUtilities())));
        $configurationConsulter = new ConfigurationConsulter($fileConfigurationLoader);

        $dataSourceNameFactory = new DataSourceNameFactory($configurationConsulter);
        $connectionFactory = new ConnectionFactory($dataSourceNameFactory->getDataSourceName());

        $sessionUtilities = new SessionUtilities($fileConfigurationLoader, $connectionFactory, $configurationConsulter);

        return new Bootstrap($request, $fileConfigurationLoader, $connectionFactory, $sessionUtilities);
    }
}