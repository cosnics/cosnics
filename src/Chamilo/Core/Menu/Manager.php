<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
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
    public const string CONTEXT = __NAMESPACE__;
    public const string PARAM_DIRECTION = 'direction';
    public const string PARAM_DIRECTION_DOWN = 'down';
    public const string PARAM_DIRECTION_UP = 'up';
    public const string PARAM_ITEM = 'item';
    public const string PARAM_PARENT = 'parent';
    public const string PARAM_TYPE = 'type';

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
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
        return ActionEnum::BROWSE->value;
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
