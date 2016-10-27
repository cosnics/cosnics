<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Export\Zip;

use Chamilo\Core\Repository\ContentObject\File\Common\Export\ZipExportImplementation;

class ZipFlatExportImplementation extends ZipExportImplementation
{

    /**
     * Returns the path
     * 
     * @param ContentObject $content_object
     * @param string $filename
     *
     * @return mixed
     */
    function get_path_for_file_in_zip($content_object, $filename)
    {
        return $filename;
    }
}
