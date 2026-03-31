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
    public function __construct(protected SessionInterface $session)
    {
    }

    public function clear(): void
    {
        $this->session->remove(static::class);
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert[]
     */
    public function retrieve(): array
    {
        $serializedSessionAlerts = $this->session->get(static::class);

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
        $this->session->set(static::class, serialize($alerts));
    }
}