<?php
namespace Chamilo\Application\Weblcms\Admin\Table\Entity;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Application\Weblcms\Admin\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

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
        $helper_class = $this->get_table()->get_helper_class_name();

        $rendered = $helper_class :: render_table_cell($this, $column, $result);

        if ($rendered)
        {
            return $rendered;
        }
        else
        {
            return parent :: render_cell($column, $result);
        }
    }

    /**
     * Returns the actions toolbar
     *
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                        Manager :: PARAM_ENTITY_TYPE => $this->get_component()->get_selected_entity_type(),
                        Manager :: PARAM_ENTITY_ID => $result[DataClass :: PROPERTY_ID])),
                ToolbarItem :: DISPLAY_ICON,
                true));

        return $toolbar->as_html();
    }
}