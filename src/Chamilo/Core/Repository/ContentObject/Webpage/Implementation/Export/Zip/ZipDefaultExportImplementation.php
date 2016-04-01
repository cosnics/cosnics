<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Implementation\Export\Zip;

use Chamilo\Core\Repository\ContentObject\Webpage\Implementation\Export\ZipExportImplementation;

class ZipDefaultExportImplementation extends ZipExportImplementation
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
        return $content_object->get_virtual_path() . $filename;
    }
}
