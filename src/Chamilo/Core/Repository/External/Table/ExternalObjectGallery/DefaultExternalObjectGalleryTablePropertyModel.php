<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObjectGallery;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty;

abstract class DefaultExternalObjectGalleryTablePropertyModel extends DataClassGalleryTablePropertyModel
{

    /**
     *
     * @see common\libraries.NewGalleryObjectTablePropertyModel::initialize_properties()
     */
    public function initialize_properties()
    {
        $connector = $this->get_component()->get_external_repository_browser()->get_parent()->get_external_repository_manager_connector();

        foreach ($connector :: get_sort_properties() as $property)
        {
            $this->add_property(new DataClassGalleryTableProperty($property->get_class(), $property->get_property()));
        }
    }
}
