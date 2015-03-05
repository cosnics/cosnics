<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Table\ExternalObjectGallery;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTable;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Interfaces\GalleryTableOrderDirectionProhibition;

class ExternalObjectGalleryObjectTable extends DataClassGalleryTable implements GalleryTableOrderDirectionProhibition
{
    const DEFAULT_ROW_COUNT = 3;
    const DEFAULT_COLUMN_COUNT = 3;
}
