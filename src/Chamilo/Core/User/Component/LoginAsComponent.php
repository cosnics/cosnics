<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
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
    public function run(?User $currentUser = null): Response
    {
        if ($this->getAdminUserIdentifier()) {
            $session = $this->getRequest()->getSession();

            $session->clear();
            $session->set(AuthenticationValidator::SESSION_USER_ID, $this->getAdminUserIdentifier());

            return new RedirectResponse($this->getUrlGenerator()->fromParameters());
        }
        elseif ($currentUser instanceof User && $currentUser->isPlatformAdministrator() && $this->getUserIdentifier()) {
            $session = $this->getRequest()->getSession();

            $session->clear();
            $session->set(AuthenticationValidator::SESSION_USER_ID, $this->getUserIdentifier());
            $session->set(AuthenticationValidator::PARAM_AS_ADMIN, $currentUser->getIdentifier()->toString());

            return new RedirectResponse(
                $this->getUrlGenerator()->fromParameters(
                    [ApplicationInterface::PARAM_CONTEXT => \Chamilo\Core\Home\Manager::CONTEXT]
                )
            );
        }
        else {
            throw new NotAllowedException();
        }
    }

    protected function getAdminUserIdentifier(): ?string
    {
        return $this->getRequest()->getSession()->get(AuthenticationValidator::PARAM_AS_ADMIN);
    }

    protected function getUserIdentifier(): ?string
    {
        return $this->getRequest()->query->get(self::PARAM_USER_ID);
    }
}
