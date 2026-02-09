<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Core\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_EDIT = 'Editor';
    public const ACTION_ITEM_TREE_DATA = 'ItemTreeData';
    public const ACTION_MOVE = 'Mover';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;
    public const PARAM_DIRECTION = 'direction';
    public const PARAM_DIRECTION_DOWN = 'down';
    public const PARAM_DIRECTION_UP = 'up';
    public const PARAM_ITEM = 'item';
    public const PARAM_PARENT = 'parent';
    public const PARAM_TYPE = 'type';

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function __construct(?User $user = null)
    {
        parent::__construct($user);

        $this->checkAuthorization(Manager::CONTEXT);
    }

    public function getCachedItemService(): CachedItemService
    {
        return $this->getService(CachedItemService::class);
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
