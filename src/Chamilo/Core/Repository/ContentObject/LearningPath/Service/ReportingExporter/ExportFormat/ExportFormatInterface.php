<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat;

/**
 * Common interface for export formats. Includes functionality necessary for the export writer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ExportFormatInterface
{
    /**
     * Converts the export format to an array
     *
     * @return array
     */
    public function toArray(): array;
}