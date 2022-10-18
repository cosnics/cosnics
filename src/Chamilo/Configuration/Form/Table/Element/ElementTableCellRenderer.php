<?php
namespace Chamilo\Configuration\Form\Table\Element;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table cell renderer for the schema
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param Element $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $update_url = $this->get_component()->get_update_element_url($result);
        $delete_url = $this->get_component()->get_delete_element_url($result);

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $update_url, ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $delete_url, ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->as_html();
    }

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param Element $result
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $result): string
    {
        switch ($column->get_name())
        {
            case Element::PROPERTY_TYPE :
                return $result->getTypeName($result->getType());
            case Element::PROPERTY_REQUIRED :
                if ($result->get_required())
                {
                    return Translation::get('ConfirmTrue', null, StringUtilities::LIBRARIES);
                }
                else
                {
                    return Translation::get('ConfirmFalse', null, StringUtilities::LIBRARIES);
                }
        }

        return parent::renderCell($column, $result);
    }
}