<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Common;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;

class ExportImplementation extends ContentObjectExportImplementation
{

    public static function get_types()
    {
        return parent::get_types(array(ContentObjectExport::FORMAT_ICAL, ContentObjectExport::FORMAT_ZIP));
    }
}
