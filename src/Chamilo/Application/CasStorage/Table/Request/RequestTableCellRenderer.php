<?php
namespace Chamilo\Application\CasStorage\Table\Request;

use Chamilo\Application\CasStorage\Manager;
use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class RequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case AccountRequest::PROPERTY_REQUESTER_ID :
                return $object->get_requester_user()->get_fullname();
            case AccountRequest::PROPERTY_REQUEST_DATE :
                return DatetimeUtilities::format_locale_date(null, $object->get_request_date());
            case AccountRequest::PROPERTY_STATUS :
                return $object->get_status_icon();
        }
        return parent::render_cell($column, $object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_EDIT, 
                        Manager::PARAM_REQUEST_ID => $object->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_REQUEST_ID => $object->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        if ($object->is_pending() || $object->is_rejected())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Accept', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Action/Accept'), 
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_ACCEPT, 
                            Manager::PARAM_REQUEST_ID => $object->get_id())), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('AcceptNotAvailable', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Action/AcceptNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        if ($object->is_pending())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Reject', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Action/Reject'), 
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_REJECT, 
                            Manager::PARAM_REQUEST_ID => $object->get_id())), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('RejectNotAvailable', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Action/RejectNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }
}
