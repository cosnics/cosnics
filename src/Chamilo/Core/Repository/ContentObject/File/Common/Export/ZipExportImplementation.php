<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Export;

use Chamilo\Core\Repository\ContentObject\File\Common\ExportImplementation;

abstract class ZipExportImplementation extends ExportImplementation
{

    /**
     * Renders the export
     */
    public function render()
    {
        $content_object = $this->get_content_object();

        $virtual_path = $this->get_content_object()->get_virtual_path();
        $path = $this->get_context()->get_temporary_directory() . $virtual_path;

        $filename = basename($this->getFilesystemTools()->createUniqueName($path, $content_object->get_filename()));

        $this->get_context()->add_files(
            $this->get_content_object()->get_full_path(), $this->get_path_for_file_in_zip($content_object, $filename)
        );
    }

    /**
     * Returns the path
     *
     * @param ContentObject $content_object
     * @param string $filename
     *
     * @return mixed
     */
    public abstract function get_path_for_file_in_zip($content_object, $filename);
}
