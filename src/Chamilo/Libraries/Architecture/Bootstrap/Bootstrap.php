<?php
namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\FileConfigurationLocator;
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
     * @var \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    private $fileConfigurationLocator;

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
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     * @param \Chamilo\Libraries\Storage\DataManager\Doctrine\Factory\ConnectionFactory $connectionFactory
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request,
        FileConfigurationLocator $fileConfigurationLocator, ConnectionFactory $connectionFactory,
        SessionUtilities $sessionUtilities)
    {
        $this->request = $request;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
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
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->fileConfigurationLocator;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     */
    public function setFileConfigurationLocator(FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
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
        if (! $this->getFileConfigurationLocator()->isAvailable())
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