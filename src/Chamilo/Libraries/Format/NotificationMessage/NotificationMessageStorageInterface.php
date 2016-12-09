<?php

namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * Interface for a storage for notification messages
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface NotificationMessageStorageInterface
{
    /**
     * Stores the notification messages
     *
     * @param NotificationMessage[] $notificationMessages
     */
    public function store($notificationMessages = array());

    /**
     * Retrieves the notification messages
     *
     * @return NotificationMessage[]
     */
    public function retrieve();

    /**
     * Clears the notification messages
     */
    public function clear();
}

