<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\User;

use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
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

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Vocabulary', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('language', [], null, 'fas'), $this->get_component()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE,
                    \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $this->get_component()
                        ->getSelectedElementId(), Manager::PARAM_USER_ID => $result[User::PROPERTY_ID]
                )
            ), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }
}