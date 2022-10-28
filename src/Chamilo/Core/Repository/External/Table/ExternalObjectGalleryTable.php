<?php
namespace Chamilo\Core\Repository\External\Table;

use Chamilo\Libraries\Format\Table\Extension\DataClassGalleryTableRenderer;

class ExternalObjectGalleryTable extends DataClassGalleryTableRenderer
{

    public static function factory($component)
    {
        $class = $component->get_external_repository_browser()->get_external_repository()->get_implementation() .
             '\Table\ExternalObjectGallery\ExternalObjectGalleryTable';
        return new $class($component);
    }
}
