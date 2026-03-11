<?php
namespace Chamilo\Core\Menu;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Translation\Translator;

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

    protected AlertsManager $alertsManager;

    protected CachedItemService $cachedItemService;

    protected ItemRendererRegistry $itemRendererRegistry;

    protected ItemService $itemService;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, CachedItemService $cachedItemService,
        ItemRendererRegistry $itemRendererRegistry, ItemService $itemService, AlertsManager $alertsManager
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator);

        $this->cachedItemService = $cachedItemService;
        $this->itemRendererRegistry = $itemRendererRegistry;
        $this->itemService = $itemService;
        $this->alertsManager = $alertsManager;
    }

    public function getAlertsManager(): AlertsManager
    {
        return $this->alertsManager;
    }

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
        return $this->cachedItemService;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::BROWSE->value;
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->itemRendererRegistry;
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }
}
