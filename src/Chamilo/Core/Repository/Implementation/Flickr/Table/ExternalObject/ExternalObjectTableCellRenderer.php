<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Table\ExternalObject;

use Chamilo\Core\Repository\External\Table\ExternalObject\DefaultExternalTableCellRenderer;
use Chamilo\Core\Repository\Implementation\Flickr\ExternalObject;

class ExternalObjectTableCellRenderer extends DefaultExternalTableCellRenderer
{

    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case ExternalObject::PROPERTY_LICENSE :
                return $object->get_license_icon();
        }
        return parent::render_cell($column, $object);
    }
}
