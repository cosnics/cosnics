<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObject;

use Chamilo\Core\Repository\External\ExternalObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class DefaultExternalTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case ExternalObject::PROPERTY_TYPE :
                return $object->get_icon_image();
            case ExternalObject::PROPERTY_TITLE :
                $title_short = StringUtilities::getInstance()->truncate($object->get_title(), 50, false);
                return '<a href="' .
                     htmlentities($this->get_component()->get_external_repository_object_viewing_url($object)) .
                     '" title="' . htmlentities($object->get_title()) . '">' . $title_short . '</a>';
            case ExternalObject::PROPERTY_DESCRIPTION :
                return StringUtilities::getInstance()->truncate($object->get_description(), 50);
            case ExternalObject::PROPERTY_CREATED :
                return DatetimeUtilities::getInstance()->formatLocaleDate(null, $object->get_created());
        }
        return parent::render_cell($column, $object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->get_component()->get_external_repository_object_actions($object));
        return $toolbar->as_html();
    }
}
