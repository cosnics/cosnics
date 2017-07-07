<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat\ExportFormatInterface;

/**
 * Writes exported objects to a CSV file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CsvWriter implements WriterInterface
{
    /**
     * The path to the file
     *
     * @var string
     */
    protected $filename;

    /**
     * CsvWriter constructor.
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param ExportFormatInterface[] $exportedObjects
     */
    public function writeExportedObjects(array $exportedObjects = array())
    {
        if(empty($exportedObjects))
        {
            return;
        }

        $fileHandle = fopen($this->filename, 'w');

        fputcsv($fileHandle, array_keys($exportedObjects[0]->toArray()), ';');

        foreach($exportedObjects as $exportedObject)
        {
            fputcsv($fileHandle, $exportedObject->toArray(), ';');
        }

        fclose($fileHandle);
    }
}