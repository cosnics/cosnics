<?php
namespace Chamilo\Core\Repository\External\Table;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTable;

class ExternalObjectGalleryTable extends DataClassGalleryTable
{

    public static function factory($component)
    {
        $class = $component->get_external_repository_browser()->get_external_repository()->get_implementation() .
             '\Table\ExternalObjectGallery\ExternalObjectGalleryTable';
        return new $class($component);
    }
}
