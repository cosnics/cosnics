<?php
namespace Chamilo\Core\Metadata\Component;

use Chamilo\Core\Metadata\Export\MetadataStructureExporter;
use Chamilo\Core\Metadata\Export\Renderer\XmlMetadataStructureExportRenderer;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Libraries\File\Filesystem;

/**
 * Component to export metadata
 */
class MetadataExporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $exporter = new MetadataStructureExporter(new XmlMetadataStructureExportRenderer());
        $exported_path = $exporter->export();
        Filesystem :: file_send_for_download($exported_path, true, 'metadata.xml', 'text/xml');
    }
}
