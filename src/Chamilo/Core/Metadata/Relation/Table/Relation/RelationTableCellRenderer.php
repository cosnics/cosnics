<?php
namespace Chamilo\Core\Metadata\Relation\Table\Relation;

use Chamilo\Core\Metadata\Relation\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 *
 * @package Chamilo\Core\Metadata\Schema\Table\Schema
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation $relationType
     *
     * @return string
     */
    public function get_actions($relationType)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                        Manager::PARAM_RELATION_ID => $relationType->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_RELATION_ID => $relationType->get_id()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->as_html();
    }
}