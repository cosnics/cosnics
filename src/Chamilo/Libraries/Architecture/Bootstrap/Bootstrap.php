<?php
namespace Chamilo\Libraries\Architecture\Bootstrap;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\Install\Manager as InstallationManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ErrorHandler\ErrorHandler;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @package Chamilo\Libraries\Architecture\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Bootstrap
{

    private ErrorHandler $errorHandler;

    private FileConfigurationLocator $fileConfigurationLocator;

    private ChamiloRequest $request;

    private Session $session;

    private SessionUtilities $sessionUtilities;

    private bool $showErrors;

    public function __construct(
        ChamiloRequest $request, FileConfigurationLocator $fileConfigurationLocator, SessionUtilities $sessionUtilities,
        ErrorHandler $errorHandler, Session $session, bool $showErrors = false
    )
    {
        $this->request = $request;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->sessionUtilities = $sessionUtilities;
        $this->session = $session;
        $this->errorHandler = $errorHandler;
        $this->showErrors = $showErrors;
    }

    protected function checkInstallation(): Bootstrap
    {
        $context = $this->getRequest()->query->get(Application::PARAM_CONTEXT);

        if (!$this->getFileConfigurationLocator()->isAvailable() && $context != InstallationManager::CONTEXT)
        {
            $this->getRequest()->initialize([Application::PARAM_CONTEXT => InstallationManager::CONTEXT]);
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

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    public function getShowErrors(): bool
    {
        return $this->showErrors;
    }

    protected function registerErrorHandlers(): Bootstrap
    {
        if (!$this->getShowErrors())
        {
            $this->getErrorHandler()->registerErrorHandlers();
        }

        return $this;
    }

    public function setup(): void
    {
        $this->registerErrorHandlers()->checkInstallation()->startSession();
    }

    protected function startSession(): Bootstrap
    {
        $this->getSessionUtilities()->start();
        $this->getRequest()->setSession($this->getSession());

        return $this;
    }
}