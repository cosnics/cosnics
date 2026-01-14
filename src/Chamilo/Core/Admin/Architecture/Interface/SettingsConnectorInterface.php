<?php
namespace Chamilo\Core\Admin\Architecture\Interface;

/**
 * @package Chamilo\Core\Admin\Service
 */
interface SettingsConnectorInterface
{
    public function getContext(): string;
}