<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Common;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;

/**
 * Class used to define an extra type of export.
 * 
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ExportImplementation extends ContentObjectExportImplementation
{

    /**
     * Method returns the allowed types of exporting.
     * 
     * @return Array
     */
    public static function get_types(array $types = [])
    {
        return parent::get_types(array(ContentObjectExport::FORMAT_HTML));
    }
}
