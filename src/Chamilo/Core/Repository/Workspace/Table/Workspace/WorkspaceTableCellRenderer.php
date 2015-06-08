<?php
namespace Chamilo\Core\Repository\Workspace\Table\Workspace;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

class WorkspaceTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $workspace)
    {
        switch ($column->get_name())
        {
            case Workspace :: PROPERTY_CREATOR_ID :
                return $workspace->getCreator()->get_fullname();
            case Workspace :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES),
                    $workspace->getCreationDate());
            case Workspace :: PROPERTY_NAME :
                return '<a href="' . $this->getWorkspaceUrl($workspace) . '">' .
                     parent :: render_cell($column, $workspace) . '</a>';
        }

        return parent :: render_cell($column, $workspace);
    }

    public function get_actions($workspace)
    {
        return $this->getToolbar($workspace)->as_html();
    }

    public function getToolbar($workspace)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE,
                        Manager :: PARAM_WORKSPACE_ID => $workspace->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Rights', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Rights'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_RIGHTS,
                        Manager :: PARAM_WORKSPACE_ID => $workspace->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                        Manager :: PARAM_WORKSPACE_ID => $workspace->get_id())),
                ToolbarItem :: DISPLAY_ICON,
                true));

        return $toolbar;
    }

    private function getWorkspaceUrl($workspace)
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: package(),
                \Chamilo\Core\Repository\Manager :: PARAM_WORKSPACE_ID => $workspace->getId()));

        return $redirect->getUrl();
    }
}
