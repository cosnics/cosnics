<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LoginAsComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(): Response
    {
        if ($this->getAdminUserIdentifier())
        {
            $session = $this->getSession();

            $session->clear();
            $session->set(AuthenticationValidator::SESSION_USER_ID, $this->getAdminUserIdentifier());

            return new RedirectResponse($this->getUrlGenerator()->fromParameters());
        }
        elseif ($this->getUser()->isPlatformAdministrator() && $this->getUserIdentifier())
        {
            $session = $this->getSession();

            $session->clear();
            $session->set(AuthenticationValidator::SESSION_USER_ID, $this->getUserIdentifier());
            $session->set(AuthenticationValidator::PARAM_AS_ADMIN, $this->getUser()->getId());

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters([Application::PARAM_CONTEXT => 'Chamilo\Core\Home'])
            );
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    protected function getAdminUserIdentifier(): ?string
    {
        return $this->getSession()->get(AuthenticationValidator::PARAM_AS_ADMIN);
    }

    protected function getUserIdentifier(): ?string
    {
        return $this->getRequest()->query->get(self::PARAM_USER_ID);
    }

}
