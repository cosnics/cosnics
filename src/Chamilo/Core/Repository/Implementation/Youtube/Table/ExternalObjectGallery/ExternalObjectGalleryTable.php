<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Table\ExternalObjectGallery;

use Chamilo\Libraries\Format\Table\Extension\DataClassGalleryTableRenderer;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Interfaces\GalleryTableOrderDirectionProhibition;

class ExternalObjectGalleryTable extends DataClassGalleryTableRenderer implements GalleryTableOrderDirectionProhibition
{
    const DEFAULT_COLUMN_COUNT = 3;
}
