<?php
namespace Chamilo\Application\Survey\Table\User;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class UserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function render_cell($column, $user)
    {
        switch ($column->get_name())
        {
            case User::PROPERTY_EMAIL :
                return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a><br/>';
        }
        
        return parent::render_cell($column, $user);
    }

    public function get_actions($user)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ViewSurvey'), 
                Theme::getInstance()->getCommonImagePath('Action/Next'), 
                $this->get_component()->get_survey_publication_viewer_url(
                    $this->get_component()->get_publication_id(), 
                    $user->get_id()), 
                ToolbarItem::DISPLAY_ICON));
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('CancelInvitation'), 
                Theme::getInstance()->getCommonImagePath('Action/Unsubscribe'), 
                $this->get_component()->get_survey_participant_delete_url($user->get_id()), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>