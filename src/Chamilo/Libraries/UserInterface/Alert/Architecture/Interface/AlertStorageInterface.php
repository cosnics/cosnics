<?php
namespace Chamilo\Libraries\UserInterface\Alert\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Interface
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AlertStorageInterface
{
    public function clear(): void;

    /**
     * @return \Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert[]
     */
    public function retrieve(): array;

    /**
     * @param \Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert[] $alerts
     */
    public function store(array $alerts = []): void;
}

