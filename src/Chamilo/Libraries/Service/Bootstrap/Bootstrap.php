<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\ErrorHandling\Service\ErrorHandler;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Service\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class Bootstrap
{
    protected ErrorHandler $errorHandler;

    protected ChamiloRequest $request;

    protected SessionInterface $session;

    protected bool $showErrors;

    public function __construct(
        ChamiloRequest $request, ErrorHandler $errorHandler, SessionInterface $session, bool $showErrors = false
    )
    {
        $this->request = $request;
        $this->session = $session;
        $this->errorHandler = $errorHandler;
        $this->showErrors = $showErrors;
    }

    protected function getErrorHandler(): ErrorHandler
    {
        return $this->errorHandler;
    }

    protected function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    protected function getSession(): SessionInterface
    {
        return $this->session;
    }

    protected function getShowErrors(): bool
    {
        return $this->showErrors;
    }

    protected function registerErrorHandlers(): Bootstrap
    {
        if (!$this->getShowErrors()) {
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