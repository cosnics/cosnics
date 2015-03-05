<?php
namespace Chamilo\Core\Lynx\Source\Table\Source;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;

class SourceTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     * 
     * @param $course Course
     * @return String
     */
    public function get_actions($registration)
    {
        $toolbar = new Toolbar();
        
        // $toolbar->add_item(
        // new ToolbarItem(Translation :: get('ViewPackageDetails'),
        // Theme :: getInstance()->getCommonImagePath('action_details'),
        // $this->get_component()->get_url(
        // array(Manager :: PARAM_ACTION => Manager :: ACTION_VIEW, Manager :: PARAM_CONTEXT =>
        // $registration->get_context())),
        // ToolbarItem :: DISPLAY_ICON));
        
        // if (! $registration->is_up_to_date())
        // {
        // $toolbar->add_item(
        // new ToolbarItem(Translation :: get('Update', array(), Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('action_update'),
        // $this->get_component()->get_url(
        // array(Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, Manager :: PARAM_REGISTRATION =>
        // $registration->get_id(), Manager :: PARAM_INSTALL_TYPE => Manager :: INSTALL_REMOTE)),
        // ToolbarItem :: DISPLAY_ICON));
        // }
        // else
        // {
        // $toolbar->add_item(
        // new ToolbarItem(Translation :: get('PackageIsAlreadyUpToDate'),
        // Theme :: getInstance()->getCommonImagePath('action_update_na'), '', ToolbarItem :: DISPLAY_ICON));
        // }
        
        // if ($registration->is_active())
        // {
        // $toolbar->add_item(
        // new ToolbarItem(Translation :: get('Deactivate', array(), Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('action_deactivate'),
        // $this->get_component()->get_url(
        // array(Manager :: PARAM_ACTION => Manager :: ACTION_DEACTIVATE, Manager :: PARAM_REGISTRATION =>
        // $registration->get_id())),
        // ToolbarItem :: DISPLAY_ICON));
        // }
        // else
        // {
        // $toolbar->add_item(
        // new ToolbarItem(Translation :: get('Activate', array(), Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('action_activate'),
        // $this->get_component()->get_url(
        // array(Manager :: PARAM_ACTION => Manager :: ACTION_ACTIVATE, Manager :: PARAM_REGISTRATION =>
        // $registration->get_id())),
        // ToolbarItem :: DISPLAY_ICON));
        
        // }
        
        // $toolbar->add_item(
        // new ToolbarItem(Translation :: get('Deinstall', array(), Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('action_deinstall'),
        // $this->get_component()->get_url(
        // array(Manager :: PARAM_ACTION => Manager :: ACTION_REMOVE, Manager :: PARAM_SECTION =>
        // $registration->get_type(), Manager :: PARAM_PACKAGE => $registration->get_id())),
        // ToolbarItem :: DISPLAY_ICON, true));
        
        return $toolbar->as_html();
    }
}
