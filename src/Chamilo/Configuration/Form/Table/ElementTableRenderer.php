<?php
namespace Chamilo\Configuration\Form\Table;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Configuration\Form\Table
 *
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_DYNAMIC_FORM_ID;

    protected function initializeColumns(): void
    {
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(Element::class, Element::PROPERTY_TYPE));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(Element::class, Element::PROPERTY_NAME));
        $this->addColumn($this->getDataClassPropertyTableColumnFactory()->getColumn(Element::class, Element::PROPERTY_REQUIRED));
    }

    /**
     * @param \Chamilo\Configuration\Form\Storage\DataClass\Element $element
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $element): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case Element::PROPERTY_TYPE :
                return Element::getTypeName($element->getType());
            case Element::PROPERTY_REQUIRED :
                if ($element->get_required())
                {
                    return $translator->trans('ConfirmTrue', [], StringUtilities::LIBRARIES);
                }
                else
                {
                    return $translator->trans('ConfirmFalse', [], StringUtilities::LIBRARIES);
                }
        }

        return parent::renderCell($column, $resultPosition, $element);
    }

    /**
     * @param \Chamilo\Configuration\Form\Storage\DataClass\Element $element
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $element): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_UPDATE_FORM_ELEMENT,
            Manager::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $deleteUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_DELETE_FORM_ELEMENT,
            Manager::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
