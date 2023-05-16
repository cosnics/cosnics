<?php
namespace Chamilo\Core\Menu\Table;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_TYPE = 'Type';

    public const TABLE_IDENTIFIER = Manager::PARAM_ITEM;

    protected ItemRendererFactory $itemRendererFactory;

    protected ItemService $itemService;

    protected RightsService $rightsService;

    public function __construct(
        ItemRendererFactory $itemRendererFactory, ItemService $itemService, RightsService $rightsService,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->itemRendererFactory = $itemRendererFactory;
        $this->itemService = $itemService;
        $this->rightsService = $rightsService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getItemDeletingUrl(Item $item): string
    {
        return $this->getItemUrl($item, [Application::PARAM_ACTION => Manager::ACTION_DELETE]);
    }

    public function getItemEditingUrl(Item $item): string
    {
        return $this->getItemUrl($item, [Application::PARAM_ACTION => Manager::ACTION_EDIT]);
    }

    public function getItemMovingUrl(Item $item, int $sortDirection): string
    {
        return $this->getItemUrl(
            $item, [Application::PARAM_ACTION => Manager::ACTION_MOVE, Manager::PARAM_DIRECTION => $sortDirection]
        );
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    public function getItemRightsUrl(Item $item): string
    {
        return $this->getItemUrl($item, [Application::PARAM_ACTION => Manager::ACTION_RIGHTS]);
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    public function getItemUrl(Item $item, array $parameters = []): string
    {
        $parameters[Application::PARAM_CONTEXT] = Manager::CONTEXT;
        $parameters[Manager::PARAM_ITEM] = $item->getId();

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getTableActions(): TableActions
    {
        $deleteUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_DELETE]
        );

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->addAction(
            new TableAction(
                $deleteUrl, $this->getTranslator()->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $translator->trans('Type', [], Manager::CONTEXT)));

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Item::class, Item::PROPERTY_SORT, null, false)
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ItemTitle::class, ItemTitle::PROPERTY_TITLE, null, false
            )
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $item): string
    {
        switch ($column->get_name())
        {
            case ItemTitle::PROPERTY_TITLE :
                $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($item);

                return $itemRenderer->renderTitle($item);
            case self::PROPERTY_TYPE :
                return $item->getGlyph()->render();
        }

        return parent::renderCell($column, $resultPosition, $item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $item): string
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
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
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
                    new FontAwesomeGlyph('sort-down', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
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
}
