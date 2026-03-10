<?php
namespace Chamilo\Core\Menu\UserInterface\Table;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\MiniButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\StaticTableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableAction;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableAction\TableActions;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Architecture\Interface\TableRowActionsSupport;
use Chamilo\Libraries\UserInterface\Table\Factory\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\UserInterface\Table\Service\DataClassListTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\ListHtmlTableRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\PageNavigationCalculator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\UserInterface\Table
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_TYPE = 'Type';
    public const TABLE_IDENTIFIER = Manager::PARAM_ITEM;

    protected ItemRendererRegistry $itemRendererFactory;

    protected ItemService $itemService;

    protected MiniButtonToolBarRenderer $miniButtonToolBarRenderer;

    public function __construct(
        ItemRendererRegistry $itemRendererFactory, ItemService $itemService, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, PageNavigationCalculator $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory,
        ClassnameUtilities $classnameUtilities, MiniButtonToolBarRenderer $miniButtonToolBarRenderer
    )
    {
        $this->itemRendererFactory = $itemRendererFactory;
        $this->itemService = $itemService;
        $this->miniButtonToolBarRenderer = $miniButtonToolBarRenderer;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory,
            $classnameUtilities
        );
    }

    public function getItemDeletingUrl(Item $item): string
    {
        return $this->getItemUrl($item, [ApplicationInterface::PARAM_ACTION => ActionEnum::DELETE->value]);
    }

    public function getItemEditingUrl(Item $item): string
    {
        return $this->getItemUrl($item, [ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE->value]);
    }

    public function getItemMovingUrl(Item $item, int $sortDirection): string
    {
        return $this->getItemUrl(
            $item,
            [ApplicationInterface::PARAM_ACTION => ActionEnum::MOVE->value, Manager::PARAM_DIRECTION => $sortDirection]
        );
    }

    public function getItemRendererFactory(): ItemRendererRegistry
    {
        return $this->itemRendererFactory;
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    public function getItemUrl(Item $item, array $parameters = []): string
    {
        $parameters[ApplicationInterface::PARAM_CONTEXT] = Manager::CONTEXT;
        $parameters[Manager::PARAM_ITEM] = $item->getId();

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getMiniButtonToolBarRenderer(): MiniButtonToolBarRenderer
    {
        return $this->miniButtonToolBarRenderer;
    }

    public function getTableActions(): TableActions
    {
        $deleteUrl = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::DELETE->value
            ]
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
     *
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        $itemRendererFactory = $this->getItemRendererFactory();

        return match ($column->getName()) {
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
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string
    {
        $numberOfSiblings = $this->getItemService()->countItemsByParentIdentifier($result->getParentId());

        $isFirstItem = $result->getSort() == 1;
        $isOnlyItem = $numberOfSiblings == 1;
        $isLastItem = $result->getSort() == $numberOfSiblings;

        $translator = $this->getTranslator();

        $buttonToolBar = new MiniButtonToolBar();

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('Edit', [], StringUtilities::LIBRARIES), inlineGlyph: new FontAwesomeGlyph(
                'pencil-alt'
            ), action: $this->getItemEditingUrl($result), display: DisplayTypeEnum::ICON, classes: ['btn-link']
            )
        );

        if ($isFirstItem || $isOnlyItem) {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('up-long', ['text-muted']), display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('MoveUp', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('up-long'), action: $this->getItemMovingUrl(
                    $result, ItemService::PARAM_DIRECTION_UP
                ), display: DisplayTypeEnum::ICON, classes: ['btn-link']
                )
            );
        }

        if ($isLastItem || $isOnlyItem) {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('down-long', ['text-muted']), display: DisplayTypeEnum::ICON,
                    classes: ['btn-link']
                )
            );
        }
        else {
            $buttonToolBar->addButton(
                new Button(
                    label: $translator->trans('MoveDown', [], StringUtilities::LIBRARIES),
                    inlineGlyph: new FontAwesomeGlyph('down-long'), action: $this->getItemMovingUrl(
                    $result, ItemService::PARAM_DIRECTION_DOWN
                ), display: DisplayTypeEnum::ICON, classes: ['btn-link']
                )
            );
        }

        $buttonToolBar->addButton(
            new Button(
                label: $translator->trans('Delete', [], StringUtilities::LIBRARIES), inlineGlyph: new FontAwesomeGlyph(
                'times'
            ), action: $this->getItemDeletingUrl($result), display: DisplayTypeEnum::ICON,
                confirmationMessage: $this->getTranslator()->trans(
                    'ConfirmChosenAction', [], StringUtilities::LIBRARIES
                ), classes: ['btn-link']
            )
        );

        return $this->getMiniButtonToolBarRenderer()->render($buttonToolBar);
    }
}
