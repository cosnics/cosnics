<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Table\ExternalObject;

use Chamilo\Core\Repository\External\Table\ExternalObject\DefaultExternalTableCellRenderer;
use Chamilo\Core\Repository\Implementation\Flickr\ExternalObject;
use Chamilo\Libraries\Format\Table\Column\TableColumn;

class ExternalObjectTableCellRenderer extends DefaultExternalTableCellRenderer
{

    public function renderCell(TableColumn $column, $object): string
    {
        switch ($column->get_name())
        {
            case ExternalObject::PROPERTY_LICENSE :
                return $object->get_license_icon();
        }
        return parent::renderCell($column, $object);
    }
}
