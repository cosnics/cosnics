<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Trait;

use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DefaultRedirectAfterLoginTrait
{
    protected ChamiloRequest $request;

    protected UrlGenerator $urlGenerator;

    public function redirectAfterLogin(): void
    {
        $context = $this->request->query->get(ApplicationInterface::PARAM_CONTEXT);

        if ($this->request->query->count() > 0 && $context != Manager::CONTEXT) {
            $parameters = $this->request->query->all();
        }
        else {
            $parameters = [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT
            ];
        }

        $redirect = new RedirectResponse(
            $this->urlGenerator->fromParameters($parameters)
        );

        $redirect->send();
        exit;
    }
}