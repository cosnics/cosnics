<?php
namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory;

/**
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Bootstrap
{

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    private $fileConfigurationLoader;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory
     */
    private $connectionFactory;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    private $sessionUtilities;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Configuration\Service\FileConfigurationLoader $fileConfigurationLoader
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory $connectionFactory
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request,
        FileConfigurationLoader $fileConfigurationLoader, ConnectionFactory $connectionFactory,
        SessionUtilities $sessionUtilities)
    {
        $this->request = $request;
        $this->fileConfigurationLoader = $fileConfigurationLoader;
        $this->connectionFactory = $connectionFactory;
        $this->sessionUtilities = $sessionUtilities;
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    public function getFileConfigurationLoader()
    {
        return $this->fileConfigurationLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLoader $fileConfigurationLoader
     */
    public function setFileConfigurationLoader(FileConfigurationLoader $fileConfigurationLoader)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory
     */
    public function getConnectionFactory()
    {
        return $this->connectionFactory;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory $connectionFactory
     */
    public function setConnectionFactory(ConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    public function getSessionUtilities()
    {
        return $this->sessionUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function setSessionUtilities(SessionUtilities $sessionUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * Check if the system has been installed, if not display message accordingly
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrapper
     */
    private function checkInstallation()
    {
        if (! $this->getFileConfigurationLoader()->isAvailable())
        {
            $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Install');
            // TODO: This is old code to make sure those instances still accessing the parameter the old way keep on
            // working for now
            Request::set_get(Application::PARAM_CONTEXT, 'Chamilo\Core\Install');
            return $this;
        }

        $this->getConnectionFactory()->getConnection();

        return $this;
    }

    private function startSession()
    {
        $this->getSessionUtilities()->start();

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrapper
     */
    public function setup()
    {
        return $this->checkInstallation()->startSession();
    }
}