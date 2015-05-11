<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class GalleryTableDataProvider extends DataClassGalleryTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieve_active_content_objects(
            $this->get_table()->get_type(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        return DataManager :: count_active_content_objects(
            $this->get_table()->get_type(), 
            new DataClassCountParameters($condition));
    }
}
