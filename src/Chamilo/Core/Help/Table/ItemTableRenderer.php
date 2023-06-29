<?php
namespace Chamilo\Core\Help\Table;

use Chamilo\Core\Help\Manager;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Help\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{

    public const TABLE_IDENTIFIER = Manager::PARAM_HELP_ITEM;

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(HelpItem::class, HelpItem::PROPERTY_CONTEXT)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(HelpItem::class, HelpItem::PROPERTY_IDENTIFIER)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(HelpItem::class, HelpItem::PROPERTY_LANGUAGE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(HelpItem::class, HelpItem::PROPERTY_URL)
        );
    }

    /**
     * @param \Chamilo\Core\Help\Storage\DataClass\HelpItem $helpItem
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $helpItem): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_UPDATE_HELP_ITEM,
            Manager::PARAM_HELP_ITEM => $helpItem->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
