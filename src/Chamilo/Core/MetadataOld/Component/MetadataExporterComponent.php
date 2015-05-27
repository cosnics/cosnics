<?php
namespace Chamilo\Core\MetadataOld\Component;

use Chamilo\Core\MetadataOld\Export\MetadataStructureExporter;
use Chamilo\Core\MetadataOld\Export\Renderer\XmlMetadataStructureExportRenderer;
use Chamilo\Core\MetadataOld\Manager;
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
