<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Component\BrowseComponent;
use Chamilo\Core\Menu\Component\CreateComponent;
use Chamilo\Core\Menu\Component\DeleteComponent;
use Chamilo\Core\Menu\Component\ItemTreeDataComponent;
use Chamilo\Core\Menu\Component\MoveComponent;
use Chamilo\Core\Menu\Component\UpdateComponent;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_CREATE = 'Create';
    public const ACTION_DELETE = 'Delete';
    public const ACTION_ITEM_TREE_DATA = 'ItemTreeData';
    public const ACTION_MOVE = 'Move';
    public const ACTION_UPDATE = 'Update';
    public const CONTEXT = __NAMESPACE__;
    public const PARAM_DIRECTION = 'direction';
    public const PARAM_DIRECTION_DOWN = 'down';
    public const PARAM_DIRECTION_UP = 'up';
    public const PARAM_ITEM = 'item';
    public const PARAM_PARENT = 'parent';
    public const PARAM_TYPE = 'type';

    public function getApplicationAction(): string
    {
        return match (static::class) {
            BrowseComponent::class => self::ACTION_BROWSE,
            CreateComponent::class => self::ACTION_CREATE,
            DeleteComponent::class => self::ACTION_DELETE,
            ItemTreeDataComponent::class => self::ACTION_ITEM_TREE_DATA,
            MoveComponent::class => self::ACTION_MOVE,
            UpdateComponent::class => self::ACTION_UPDATE
        };
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getCachedItemService(): CachedItemService
    {
        return $this->getService(CachedItemService::class);
    }

    public function getDefaultApplicationAction(): string
    {
        return self::ACTION_BROWSE;
    }

    public function getHomeUrl(): string
    {
        return $this->getUrlGenerator()->fromParameters([Application::PARAM_ACTION => Manager::ACTION_BROWSE]);
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->getService(ItemRendererRegistry::class);
    }

    public function getItemService(): ItemService
    {
        return $this->getService(ItemService::class);
    }
}
