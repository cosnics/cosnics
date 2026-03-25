<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;

/**
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @todo This could be a DataMapper?
 */
class ItemFormDataHandler
{
    protected ItemRendererRegistry $itemRendererRegistry;

    public function __construct(ItemRendererRegistry $itemRendererRegistry)
    {
        $this->itemRendererRegistry = $itemRendererRegistry;
    }

    public function getDefaultFormData(Item $item): array
    {
        $defaultData = [];

        $defaultData[Item::PROPERTY_PARENT] = new OptionsTreeChoice($item->getParentId(), '');
        $defaultData[Item::PROPERTY_HIDDEN] = (bool) $item->getHidden();
        $defaultData[Item::PROPERTY_ICON_CLASS] = $item->getIconClass();
        $defaultData[Item::PROPERTY_TITLES] = $item->getTitles();

        try {
            $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);

            if ($itemRenderer instanceof ConfigurableItemInterface) {
                $defaultData[Item::PROPERTY_CONFIGURATION] = $itemRenderer->getDefaultFormConfigurationData($item);
            }
        }
        catch (NoSuchClassException) {
        }

        return $defaultData;
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->itemRendererRegistry;
    }

    public function handleData(string $itemType, mixed $submittedData): mixed
    {
        $processedData = [];

        $processedData[Item::PROPERTY_PARENT] = $submittedData[Item::PROPERTY_PARENT]->getValue();
        $processedData[Item::PROPERTY_HIDDEN] = $submittedData[Item::PROPERTY_HIDDEN] ? 1 : 0;
        $processedData[Item::PROPERTY_ICON_CLASS] = $submittedData[Item::PROPERTY_ICON_CLASS];
        $processedData[Item::PROPERTY_TITLES] = $submittedData[Item::PROPERTY_TITLES];

        try {
            $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($itemType);

            if ($itemRenderer instanceof ConfigurableItemInterface) {
                $processedData[Item::PROPERTY_CONFIGURATION] =
                    $itemRenderer->handleConfigurationData($submittedData[Item::PROPERTY_CONFIGURATION]);
            }
        }
        catch (NoSuchClassException) {
        }

        return $processedData;
    }
}