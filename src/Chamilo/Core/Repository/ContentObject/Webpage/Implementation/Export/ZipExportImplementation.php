<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Implementation\Export;

use Chamilo\Core\Repository\ContentObject\Webpage\Implementation\ExportImplementation;
use Chamilo\Libraries\File\Filesystem;

abstract class ZipExportImplementation extends ExportImplementation
{

    public function render()
    {
        $content_object = $this->get_content_object();

        $virtual_path = $this->get_content_object()->get_virtual_path();
        $path = $this->get_context()->get_temporary_directory() . $virtual_path;

        $filename = basename(Filesystem :: create_unique_name($path, $content_object->get_filename()));

        $this->get_context()->add_files(
            $this->get_content_object()->get_full_path(),
            $this->get_content_object()->get_virtual_path() . $filename);
    }

    /**
     * Returns the path
     *
     * @param ContentObject $content_object
     * @param string $filename
     *
     * @return mixed
     */
    abstract function get_path_for_file_in_zip($content_object, $filename);
}
