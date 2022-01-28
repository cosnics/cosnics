<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class CsvImportParser
{
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_MAX_MEMBERS = 'maximum_members';
    const PROPERTY_SELF_REGISTRATION_ALLOWED = 'self_registration_allowed';
    const PROPERTY_SELF_UNREGISTRATION_ALLOWED = 'self_unregistration_allowed';

    /**
     * @param UploadedFile $file
     *
     * @return ImportedGroup[]
     */
    public function parseImportedFile(UploadedFile $file)
    {
        if(!$this->canParseFile($file))
        {
            throw new \InvalidArgumentException('The given file is not a valid CSV file');
        }

        $handle = fopen($file->getPathname(), "r");
        $headers = fgetcsv($handle, 1000, ";");

        $importedGroups = [];

        while(($row = fgetcsv($handle, null, ';')) != null)
        {
            $rowData = array();

            foreach ($row as $index => $value)
            {
                $rowData[$headers[$index]] = (trim($value));
            }

            if(empty($rowData[self::PROPERTY_TITLE]))
            {
                continue;
            }

            $importedGroups[] = new ImportedGroup(
                $rowData[self::PROPERTY_TITLE], $rowData[self::PROPERTY_DESCRIPTION],
                $rowData[self::PROPERTY_MAX_MEMBERS], boolval($rowData[self::PROPERTY_SELF_REGISTRATION_ALLOWED]),
                boolval($rowData[self::PROPERTY_SELF_UNREGISTRATION_ALLOWED])
            );
        }

        return $importedGroups;
    }

    /**
     * Checks whether the current parser can parse the given file
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function canParseFile(UploadedFile $file)
    {
        $allowedMimeTypes = [
            'text/x-csv', 'text/csv', 'application/vnd.ms-excel', 'application/octet-stream',
            'application/force-download', 'text/comma-separated-values'
        ];

        return in_array($file->getClientMimeType(), $allowedMimeTypes);
    }
}
