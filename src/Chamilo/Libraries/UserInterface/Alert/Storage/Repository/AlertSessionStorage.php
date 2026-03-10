<?php
namespace Chamilo\Libraries\UserInterface\Alert\Storage\Repository;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Interface\AlertStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AlertSessionStorage implements AlertStorageInterface
{
    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function clear(): void
    {
        $this->getSession()->remove(static::class);
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert[]
     */
    public function retrieve(): array
    {
        $serializedSessionAlerts = $this->getSession()->get(static::class);

        if (!$serializedSessionAlerts) {
            return [];
        }

        return unserialize($serializedSessionAlerts);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert[] $alerts
     */
    public function store(array $alerts = []): void
    {
        $this->getSession()->set(static::class, serialize($alerts));
    }
}