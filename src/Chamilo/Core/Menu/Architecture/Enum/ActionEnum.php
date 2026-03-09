<?php
namespace Chamilo\Core\Menu\Architecture\Enum;

use Chamilo\Core\Menu\Component\BrowseComponent;
use Chamilo\Core\Menu\Component\CreateComponent;
use Chamilo\Core\Menu\Component\DeleteComponent;
use Chamilo\Core\Menu\Component\ItemTreeDataComponent;
use Chamilo\Core\Menu\Component\MoveComponent;
use Chamilo\Core\Menu\Component\UpdateComponent;

/**
 * @package Chamilo\Core\Group\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum ActionEnum: string
{
    case BROWSE = 'Browse';
    case CREATE = 'Create';
    case DELETE = 'Delete';
    case ITEM_TREE_DATA = 'ItemTreeData';
    case MOVE = 'Move';
    case UPDATE = 'Update';

    public static function getAction(string $className): ActionEnum
    {
        return match ($className) {
            BrowseComponent::class => self::BROWSE,
            CreateComponent::class => self::CREATE,
            DeleteComponent::class => self::DELETE,
            ItemTreeDataComponent::class => self::ITEM_TREE_DATA,
            MoveComponent::class => self::MOVE,
            UpdateComponent::class => self::UPDATE
        };
    }

    public static function getActionValue(string $className): string
    {
        return self::getAction($className)->value;
    }
}
