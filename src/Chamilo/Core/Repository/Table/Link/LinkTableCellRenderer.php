<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class LinkTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $data_class)
    {
        $type = $this->get_table()->get_type();
        
        if ($type == LinkTable::TYPE_PARENTS)
        {
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $data_class->get_parent());
        }
        elseif ($type == LinkTable::TYPE_CHILDREN)
        {
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $data_class->get_ref());
            if (in_array($object->get_type(), DataManager::get_active_helper_types()))
            {
                $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $object->get_reference());
            }
        }
        else
        {
            $object = $data_class;
        }
        
        switch ($column->get_name())
        {
            case Attributes::PROPERTY_APPLICATION :
                return Translation::get('TypeName', null, $object->get_application());
            case Attributes::PROPERTY_LOCATION :
                return $object->get_location();
            case Attributes::PROPERTY_DATE :
                return date('Y-m-d, H:i', $object->get_date());
            case ContentObject::PROPERTY_DESCRIPTION :
                return StringUtilities::getInstance()->truncate($object->get_description(), 50);
            case ContentObject::PROPERTY_TITLE :
                $url = $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_VIEW_CONTENT_OBJECTS, 
                        Manager::PARAM_CONTENT_OBJECT_ID => $object->get_id()));
                return '<a href="' . $url . '">' . StringUtilities::getInstance()->truncate($object->get_title(), 50) .
                     '</a>';
            case ContentObject::PROPERTY_TYPE :
                return $object->get_icon_image();
        }
        
        return parent::render_cell($column, $data_class);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        
        if ($object instanceof Attributes)
        {
            $link_id = $object->get_application() . '|' . $this->render_id_cell($object);
        }
        else
        {
            $link_id = $this->render_id_cell($object);
        }
        
        $type = $this->get_table()->get_type();
        
        if ($type == LinkTable::TYPE_INCLUDES)
        {
            return '&nbsp';
        }
        
        if ($type == LinkTable::TYPE_INCLUDED_IN)
        {
            return '&nbsp';
        }
        
        if ($this->get_component()->is_allowed_to_modify())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                    $this->get_delete_link_url($type, $this->get_component()->get_object()->get_id(), $link_id), 
                    ToolbarItem::DISPLAY_ICON, 
                    true));
        }
        
        return $toolbar->as_html();
    }

    private function get_delete_link_url($type, $object_id, $link_id)
    {
        $parameters = array();
        $parameters[\Chamilo\Core\Repository\Manager::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_DELETE_LINK;
        $parameters[\Chamilo\Core\Repository\Manager::PARAM_LINK_TYPE] = $type;
        $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $object_id;
        $parameters[\Chamilo\Core\Repository\Manager::PARAM_LINK_ID] = $link_id;
        
        return $this->get_component()->get_url($parameters);
    }
}
