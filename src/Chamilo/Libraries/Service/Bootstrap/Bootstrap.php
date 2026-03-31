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
    public function __construct(
        protected ChamiloRequest $request, protected ErrorHandler $errorHandler, protected SessionInterface $session,
        protected bool $showErrors = false
    )
    {
    }

    protected function registerErrorHandlers(): Bootstrap
    {
        if (!$this->showErrors) {
            $this->errorHandler->registerErrorHandlers();
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
        $this->session->start();
        $this->request->setSession($this->session);

        return $this;
    }
}