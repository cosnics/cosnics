<?php
namespace Chamilo\Core\Admin\Service;

/**
 * @package Chamilo\Core\Admin\Service
 */
interface SettingsConnectorInterface
{
    public function getContext(): string;
}