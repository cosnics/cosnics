<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Export\Zip;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Export\ZipExportImplementation;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\FilesystemTools;

class ZipDefaultExportImplementation extends ZipExportImplementation
{

    public function render()
    {
        $export_parameters = new ExportParameters(
            $this->get_context()->get_parameters()->getWorkspace(), $this->get_context()->get_parameters()->get_user(),
            ContentObjectExport::FORMAT_ICAL, [$this->get_content_object()->get_id()]
        );
        $exporter = ContentObjectExportController::factory($export_parameters);
        $file = $exporter->run();

        $virtual_path = $this->get_content_object()->get_virtual_path();
        $path = $this->get_context()->get_temporary_directory() . $virtual_path;

        $filename = $this->get_content_object()->get_title() . '.ics';
        $filename = basename($this->getFilesystemTools()->createUniqueName($path, $filename));

        $this->get_context()->add_files($file, $this->get_content_object()->get_virtual_path() . $filename);
    }
}
