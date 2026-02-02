<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Core\Admin\Service\FileConfigurationLocator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Error\Service\ErrorHandler;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Service\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Bootstrap
{

    private ErrorHandler $errorHandler;

    private FileConfigurationLocator $fileConfigurationLocator;

    private ChamiloRequest $request;

    private SessionInterface $session;

    private bool $showErrors;

    public function __construct(
        ChamiloRequest $request, FileConfigurationLocator $fileConfigurationLocator, ErrorHandler $errorHandler,
        SessionInterface $session, bool $showErrors = false
    )
    {
        $this->request = $request;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->session = $session;
        $this->errorHandler = $errorHandler;
        $this->showErrors = $showErrors;
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

    public function getSession(): SessionInterface
    {
        return $this->session;
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
        $this->registerErrorHandlers()->startSession();
    }

    protected function startSession(): Bootstrap
    {
        ini_set('session.gc_probability', 1);
        $this->getSession()->start();
        $this->getRequest()->setSession($this->getSession());

        return $this;
    }
}