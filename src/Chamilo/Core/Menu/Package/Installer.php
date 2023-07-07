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
        $items = [];

        $items[] = $this->initializeApplicationItem(\Chamilo\Core\Home\Manager::CONTEXT);
        $items[] = $this->initializeItem(LanguageItemRenderer::class);
        $items[] = $this->initializeItem(RepositoryApplicationItemRenderer::class);
        $items[] = $this->initializeItem(WorkspaceCategoryItemRenderer::class);
        $items[] = $this->initializeApplicationItem(\Chamilo\Core\Admin\Manager::CONTEXT);
        $items[] = $this->initializeItem(WidgetItemRenderer::class);

        foreach ($items as $item)
        {
            if (!$this->getItemService()->createItem($item))
            {
                return false;
            }
        }

        $this->add_message(
            self::TYPE_NORMAL, $this->getTranslator()->trans('DefaultMenuItemsCreated', [], Manager::CONTEXT)
        );

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
                'ObjectCreated', ['OBJECT' => $this->getTranslator()->trans('RightsTree', [], Manager::CONTEXT)],
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

    public function initializeApplicationItem(string $applicationContext): Item
    {
        return $this->initializeItem(ApplicationItemRenderer::class, [
            ApplicationItemRenderer::CONFIGURATION_APPLICATION => $applicationContext,
            ApplicationItemRenderer::CONFIGURATION_USE_TRANSLATION => '1'
        ]);
    }

    public function initializeItem(string $type, array $settings = []): Item
    {
        $item = new Item();

        $item->setType($type);
        $item->setParentId('0');
        $item->setDisplay(Item::DISPLAY_BOTH);
        $item->setConfiguration($settings);

        return $item;
    }
}
