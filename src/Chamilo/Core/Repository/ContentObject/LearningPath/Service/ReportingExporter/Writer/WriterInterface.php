<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat\ExportFormatInterface;

/**
 * Common interface to write exported objects to a file. The writer uses ExportFormatInterface objects to define the
 * exported objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface WriterInterface
{
    /**
     * @param ExportFormatInterface[] $exportedObjects
     */
    public function writeExportedObjects(array $exportedObjects = array());
}