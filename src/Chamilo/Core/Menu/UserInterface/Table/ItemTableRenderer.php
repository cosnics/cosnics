<?php
namespace Chamilo\Core\Menu\UserInterface\Table;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererCollection;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\Toolbar;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\ToolbarItem;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\StaticTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction\TableAction;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\FormAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableRowActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\DataClassListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\Pager;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_TYPE = 'Type';

    public const TABLE_IDENTIFIER = Manager::PARAM_ITEM;

    protected ItemRendererCollection $itemRendererFactory;

    protected ItemService $itemService;

    public function __construct(
        ItemRendererCollection $itemRendererFactory, ItemService $itemService, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory, ClassnameUtilities $classnameUtilities
    )
    {
        $this->itemRendererFactory = $itemRendererFactory;
        $this->itemService = $itemService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
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

    public function getItemRendererFactory(): ItemRendererCollection
    {
        return $this->itemRendererFactory;
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

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $translator->trans('Type', [], Manager::CONTEXT)));

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Item::class, Item::PROPERTY_SORT, null, false)
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Item::class, Item::PROPERTY_TITLES, null, false
            )
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        $itemRendererFactory = $this->getItemRendererFactory();

        return match ($column->getName())
        {
            Item::PROPERTY_TITLES => $itemRendererFactory->getItemRendererForItem($result)
                ->renderTitleForCurrentLanguage($result),
            self::PROPERTY_TYPE => $itemRendererFactory->getItemRendererForItem($result)->getRendererTypeGlyph()
                ->render(),
            default => parent::renderCell($column, $resultPosition, $result),
        };
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $result
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $numberOfSiblings = $this->getItemService()->countItemsByParentIdentifier($result->getParentId());

        $isFirstItem = $result->getSort() == 1;
        $isOnlyItem = $numberOfSiblings == 1;
        $isLastItem = $result->getSort() == $numberOfSiblings;

        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->addItem(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->getItemEditingUrl($result), ToolbarItem::DISPLAY_ICON
            )
        );

        if ($isFirstItem || $isOnlyItem)
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('up-long', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('up-long'),
                    $this->getItemMovingUrl($result, ItemService::PARAM_DIRECTION_UP), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($isLastItem || $isOnlyItem)
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('down-long', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->addItem(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('down-long'),
                    $this->getItemMovingUrl($result, ItemService::PARAM_DIRECTION_DOWN), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $toolbar->addItem(
            new ToolbarItem(
                label: $translator->trans('Delete', [], StringUtilities::LIBRARIES), image: new FontAwesomeGlyph(
                'times'
            ), href: $this->getItemDeletingUrl($result), display: ToolbarItem::DISPLAY_ICON, confirmation: true,
                confirmationMessage: $this->getTranslator()->trans(
                    'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                )
            )
        );

        return $toolbar->render();
    }
}
