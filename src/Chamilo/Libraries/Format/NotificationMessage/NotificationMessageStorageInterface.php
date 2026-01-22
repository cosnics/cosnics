<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface NotificationMessageStorageInterface
{

    public function clear(): void;

    /**
     * @return \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[]
     */
    public function retrieve(): array;

    /**
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[] $notificationMessages
     */
    public function store(array $notificationMessages = []): void;
}

