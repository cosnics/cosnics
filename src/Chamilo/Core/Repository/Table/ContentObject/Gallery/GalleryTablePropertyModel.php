<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTablePropertyModel;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty;

class GalleryTablePropertyModel extends DataClassGalleryTablePropertyModel
{

    public function initialize_properties()
    {
        $this->add_property(
            new DataClassGalleryTableProperty(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        $this->add_property(
            new DataClassGalleryTableProperty(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
    }
}
