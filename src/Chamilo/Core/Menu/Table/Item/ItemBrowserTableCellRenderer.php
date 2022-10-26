<?php
namespace Chamilo\Core\Menu\Table\Item;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\Menu\Table\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemBrowserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @param $table
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     *
     * @throws \Exception
     */
    public function __construct(
        $table, Translator $translator, ItemService $itemService, RightsService $rightsService,
        ItemRendererFactory $itemRendererFactory
    )
    {
        parent::__construct($table);

        $this->translator = $translator;
        $this->itemService = $itemService;
        $this->rightsService = $rightsService;
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function getItemDeletingUrl(Item $item)
    {
        return $this->getItemUrl($item, [Manager::PARAM_ACTION => Manager::ACTION_DELETE]);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function getItemEditingUrl(Item $item)
    {
        return $this->getItemUrl($item, [Manager::PARAM_ACTION => Manager::ACTION_EDIT]);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param int $sortDirection
     *
     * @return string
     */
    public function getItemMovingUrl(Item $item, int $sortDirection)
    {
        return $this->getItemUrl(
            $item, [Manager::PARAM_ACTION => Manager::ACTION_MOVE, Manager::PARAM_DIRECTION => $sortDirection]
        );
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function getItemRightsUrl(Item $item)
    {
        return $this->getItemUrl($item, [Manager::PARAM_ACTION => Manager::ACTION_RIGHTS]);
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param string[] $parameters
     *
     * @return string
     */
    public function getItemUrl(Item $item, array $parameters = [])
    {
        $parameters[Application::PARAM_CONTEXT] = Manager::package();
        $parameters[Manager::PARAM_ITEM] = $item->getId();
        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function get_actions($item)
    {
        $numberOfSiblings = $this->getItemService()->countItemsByParentIdentifier($item->getParentId());
        $areRightsEnabled = $this->getRightsService()->areRightsEnabled();

        $isFirstItem = $item->getSort() == 1;
        $isOnlyItem = $numberOfSiblings == 1;
        $isLastItem = $item->getSort() == $numberOfSiblings;

        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getItemEditingUrl($item), ToolbarItem::DISPLAY_ICON
            )
        );

        if ($areRightsEnabled)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Rights', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                    $this->getItemRightsUrl($item), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($isFirstItem || $isOnlyItem)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $this->getItemMovingUrl($item, ItemService::PARAM_DIRECTION_UP), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($isLastItem || $isOnlyItem)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $this->getItemMovingUrl($item, ItemService::PARAM_DIRECTION_DOWN), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->getItemDeletingUrl($item), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $item): string
    {
        switch ($column->get_name())
        {
            case ItemTitle::PROPERTY_TITLE :
                $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($item);

                return $itemRenderer->renderTitle($item);
            case ItemBrowserTableColumnModel::PROPERTY_TYPE :
                return $item->getGlyph()->render();
        }

        return parent::renderCell($column, $item);
    }
}
