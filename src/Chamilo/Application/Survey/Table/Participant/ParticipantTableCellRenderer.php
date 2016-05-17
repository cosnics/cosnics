<?php
namespace Chamilo\Application\Survey\Table\Participant;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class ParticipantTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($object)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ViewResults'),
                Theme :: getInstance()->getCommonImagePath('Action/Next'),
                $this->get_component()->get_survey_participant_publication_viewer_url($object),
                ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('RemoveSelectedResults'),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_survey_participant_delete_url($object->get_user_id()),
                ToolbarItem :: DISPLAY_ICON));

        return $toolbar->as_html();
    }
}
?>