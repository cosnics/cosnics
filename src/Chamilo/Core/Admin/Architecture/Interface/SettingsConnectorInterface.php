<?php
namespace Chamilo\Core\Admin\Architecture\Interface;

/**
 * @package Chamilo\Core\Admin\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface SettingsConnectorInterface
{
    public function getContext(): string;
}