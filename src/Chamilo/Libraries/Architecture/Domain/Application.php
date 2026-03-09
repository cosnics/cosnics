<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;

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

    public function getCurrentAction(): string
    {
        return $this->getRequest()->query->get(self::PARAM_ACTION, $this->getDefaultApplicationAction());
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
