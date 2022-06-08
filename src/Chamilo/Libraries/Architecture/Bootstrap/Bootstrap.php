<?php
namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\User\Service\SessionHandler;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler;
use Chamilo\Libraries\Platform\ChamiloRequest;
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

    private ErrorHandler $errorHandler;

    private FileConfigurationLocator $fileConfigurationLocator;

    private ChamiloRequest $request;

    private ?SessionHandler $sessionHandler;

    private SessionUtilities $sessionUtilities;

    private bool $showErrors;

    public function __construct(
        ChamiloRequest $request, FileConfigurationLocator $fileConfigurationLocator, SessionUtilities $sessionUtilities,
        ErrorHandler $errorHandler, ?SessionHandler $sessionHandler = null, bool $showErrors = false
    )
    {
        $this->request = $request;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->sessionUtilities = $sessionUtilities;
        $this->sessionHandler = $sessionHandler;
        $this->errorHandler = $errorHandler;
        $this->showErrors = $showErrors;
    }

    /**
     * @throws \Exception
     */
    protected function checkInstallation(): Bootstrap
    {
        if (!$this->getFileConfigurationLocator()->isAvailable())
        {
            $this->getRequest()->query->set(Application::PARAM_CONTEXT, 'Chamilo\Core\Install');
            // TODO: This is old code to make sure those instances still accessing the parameter the old way keep on
            // working for now
            Request::set_get(Application::PARAM_CONTEXT, 'Chamilo\Core\Install');

            return $this;
        }

        return $this;
    }

    public function getErrorHandler(): ErrorHandler
    {
        return $this->errorHandler;
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    public function setFileConfigurationLocator(FileConfigurationLocator $fileConfigurationLocator): Bootstrap
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;

        return $this;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function setRequest(ChamiloRequest $request): Bootstrap
    {
        $this->request = $request;

        return $this;
    }

    public function getSessionHandler(): ?SessionHandler
    {
        return $this->sessionHandler;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    public function setSessionUtilities(SessionUtilities $sessionUtilities): Bootstrap
    {
        $this->sessionUtilities = $sessionUtilities;

        return $this;
    }

    public function getShowErrors(): bool
    {
        return $this->showErrors;
    }

    public function setShowErrors(bool $showErrors): Bootstrap
    {
        $this->showErrors = $showErrors;

        return $this;
    }

    protected function registerErrorHandlers(): Bootstrap
    {
        if (!$this->getShowErrors())
        {
            $this->getErrorHandler()->registerErrorHandlers();
        }

        return $this;
    }

    public function setExceptionLogger(ErrorHandler $errorHandler): Bootstrap
    {
        $this->errorHandler = $errorHandler;

        return $this;
    }

    /**
     *
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->registerErrorHandlers()->checkInstallation()->startSession();
    }

    protected function startSession(): Bootstrap
    {
        if ($this->getSessionHandler() instanceof SessionHandler)
        {
            $this->getSessionHandler()->setSaveHandler();
        }

        $this->getSessionUtilities()->start();

        return $this;
    }
}