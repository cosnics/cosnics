<?php
namespace Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Interface;

/**
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface NotificationMessageStorageInterface
{

    public function clear(): void;

    /**
     * @return \Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage[]
     */
    public function retrieve(): array;

    /**
     * @param \Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage[] $notificationMessages
     */
    public function store(array $notificationMessages = []): void;
}

