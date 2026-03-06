<?php
namespace Chamilo\Core\Admin\Architecture\Enum;

use Chamilo\Core\Admin\Component\BrowseComponent;
use Chamilo\Core\Admin\Component\DiagnoseComponent;
use Chamilo\Core\Admin\Component\ViewLogsComponent;
use Chamilo\Core\Admin\Component\ViewOnlineComponent;

/**
 * @package Chamilo\Core\Admin\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case BROWSE = 'Browse';
    case DIAGNOSE = 'Diagnose';
    case VIEW_ONLINE = 'ViewOnline';
    case VIEW_LOGS = 'ViewLogs';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            BrowseComponent::class => self::BROWSE,
            DiagnoseComponent::class => self::DIAGNOSE,
            ViewOnlineComponent::class => self::VIEW_ONLINE,
            ViewLogsComponent::class => self::VIEW_LOGS,
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
