<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObjectGallery;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableDataProvider;

class DefaultExternalObjectGalleryTableDataProvider extends DataClassGalleryTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->retrieve_external_repository_objects(
            $condition, 
            $order_property, 
            $offset, 
            $count);
    }

    public function count_data($condition)
    {
        return $this->get_component()->count_external_repository_objects($condition);
    }
}
