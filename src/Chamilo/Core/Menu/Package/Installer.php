<?php
namespace Chamilo\Core\Menu\Package;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\Renderer\ApplicationItemRenderer;
use Chamilo\Core\Menu\Service\Renderer\LanguageItemRenderer;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Service\Menu\RepositoryApplicationItemRenderer;
use Chamilo\Core\Repository\Service\Menu\WorkspaceCategoryItemRenderer;
use Chamilo\Core\User\Service\Menu\WidgetItemRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Menu\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    protected function createDefaultItems(): bool
    {
        // TODO: Fix this;
        $items = [];

        $items[] = $this->initializeApplicationItem(\Chamilo\Core\Home\Manager::CONTEXT, 1);
        $items[] = $this->initializeItem(LanguageItemRenderer::class, 2);
        $items[] = $this->initializeItem(RepositoryApplicationItemRenderer::class, 3);
        $items[] = $this->initializeItem(WorkspaceCategoryItemRenderer::class, 4);
        $items[] = $this->initializeApplicationItem(\Chamilo\Core\Admin\Manager::CONTEXT, 5);
        $items[] = $this->initializeItem(WidgetItemRenderer::class, 6);

        foreach ($items as $item)
        {
            if (!$this->getItemService()->createItem($item))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function extra(): bool
    {
        if (!$this->getRightsService()->createRoot())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $this->getTranslator()->trans(
                'ObjectCreated', ['OBJECT' => $this->getTranslator()->trans('RightsTree', [], 'Chamilo\Core\Menu')],
                StringUtilities::LIBRARIES
            )
            );
        }

        return $this->createDefaultItems();
    }

    protected function getItemService(): ItemService
    {
        return $this->getService(ItemService::class);
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    public function initializeApplicationItem(string $applicationContext, int $sort): Item
    {
        return $this->initializeItem(ApplicationItemRenderer::class, $sort, [
            ApplicationItemRenderer::CONFIGURATION_APPLICATION => $applicationContext,
            ApplicationItemRenderer::CONFIGURATION_USE_TRANSLATION => '1'
        ]);
    }

    public function initializeItem(string $type, int $sort, array $settings = []): Item
    {
        $item = new Item();

        $item->setType($type);
        $item->setSort($sort);
        $item->setParentId('0');
        $item->setDisplay(Item::DISPLAY_BOTH);
        $item->setConfiguration($settings);

        return $item;
    }
}
