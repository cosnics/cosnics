<?php
namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\SessionUtilities;

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
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    private $fileConfigurationLocator;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    private $sessionUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler
     */
    private $errorHandler;

    /**
     *
     * @var boolean
     */
    private $showErrors;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler $errorHandler
     * @param boolean $showErrors
     */
    public function __construct(\Chamilo\Libraries\Platform\ChamiloRequest $request,
        FileConfigurationLocator $fileConfigurationLocator, SessionUtilities $sessionUtilities,
        ErrorHandler $errorHandler, $showErrors = false)
    {
        $this->request = $request;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->sessionUtilities = $sessionUtilities;
        $this->errorHandler = $errorHandler;
        $this->showErrors = $showErrors;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
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
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler $errorHandler
     */
    public function setExceptionLogger(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     *
     * @return boolean
     */
    public function getShowErrors()
    {
        return $this->showErrors;
    }

    /**
     *
     * @param boolean $showErrors
     */
    public function setShowErrors($showErrors)
    {
        $this->showErrors = $showErrors;
    }

    /**
     * Check if the system has been installed, if not display message accordingly
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Bootstrap
     */
    protected function checkInstallation()
    {
        if (! $this->getFileConfigurationLocator()->isAvailable())
        {
            $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Install');
            // TODO: This is old code to make sure those instances still accessing the parameter the old way keep on
            // working for now
            Request::set_get(Application::PARAM_CONTEXT, 'Chamilo\Core\Install');
            return $this;
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Bootstrap
     */
    protected function startSession()
    {
        $this->getSessionUtilities()->start();

        return $this;
    }

    /**
     * Registers the error handler by using the error handler manager
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Bootstrap
     */
    protected function registerErrorHandlers()
    {
        if (! $this->getShowErrors())
        {
            $this->getErrorHandler()->registerErrorHandlers();
        }

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Bootstrap
     */
    public function setUp(): void    {
        $this->registerErrorHandlers()->checkInstallation()->startSession();
    }
}