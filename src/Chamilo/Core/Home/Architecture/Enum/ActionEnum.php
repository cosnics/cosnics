<?php
namespace Chamilo\Core\Home\Architecture\Enum;

use Chamilo\Core\Home\Component\ViewHomeComponent;

/**
 * @package Chamilo\Core\Home\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case VIEW_HOME = 'ViewHome';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            ViewHomeComponent::class => self::VIEW_HOME
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
