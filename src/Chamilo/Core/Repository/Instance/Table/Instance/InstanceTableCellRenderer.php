<?php
namespace Chamilo\Core\Repository\Instance\Table\Instance;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InstanceTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        if ($result->is_enabled())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Deactivate', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pause-cicle', [], null, 'fas'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE,
                        Manager::PARAM_INSTANCE_ID => $result->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Activate', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('play-cicle', [], null, 'fas'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE,
                        Manager::PARAM_INSTANCE_ID => $result->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                        Manager::PARAM_INSTANCE_ID => $result->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_INSTANCE_ID => $result->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ManageRights', null, \Chamilo\Core\Rights\Manager::context()),
                new FontAwesomeGlyph('lock'), $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_RIGHTS,
                    Manager::PARAM_INSTANCE_ID => $result->get_id()
                )
            ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }

    /**
     * Renders a single cell
     *
     * @param TableColumn $column
     * @param mixed $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case Instance::PROPERTY_IMPLEMENTATION :
                $name = htmlentities(Translation::get('ImplementationName', null, $result->get_implementation()));

                $glyph = new NamespaceIdentGlyph(
                    $result->get_implementation(), true, false, false,
                    IdentGlyph::SIZE_SMALL, [], $name
                );

                return $glyph->render();
                break;
        }

        return parent::render_cell($column, $result);
    }
}