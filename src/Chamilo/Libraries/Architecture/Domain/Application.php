<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Libraries\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application implements ApplicationInterface
{
    use DependencyInjectionContainerTrait;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function checkAuthorization(string $context, ?User $user = null, ?string $action = null): void
    {
        if (!$this instanceof NoAuthenticationSupportInterface) {
            if (!$user instanceof User) {
                throw new NotAllowedException();
            }
        }
    }

    public function getBreadcrumbGenerator(): BreadcrumbGenerator
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    public function getCurrentAction(): string
    {
        return $this->getRequest()->query->get(self::PARAM_ACTION, $this->getDefaultApplicationAction());
    }

    /**
     * @param string[] $parameters
     */
    public function getRedirectResponseWithMessage(
        ?string $message = null, bool $errorMessage = false, array $parameters = []
    ): RedirectResponse
    {
        if ($message) {
            $messageType = (!$errorMessage) ? NotificationMessage::TYPE_INFO : NotificationMessage::TYPE_DANGER;
            $this->getNotificationMessageManager()->addMessage(new NotificationMessage($message, $messageType));
        }

        return new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
    }

    public function renderFooter(): string
    {
        return $this->getDefaultFooterRenderer()->render();
    }

    public function renderHeader(?User $user = null): string
    {
        return $this->getApplicationHeaderRenderer()->render($this, $user);
    }
}
