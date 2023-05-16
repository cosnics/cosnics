<?php
namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format;

use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult;
use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseGroupRelation;
use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseUserRelation;
use Chamilo\Libraries\File\Import;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Csv based importer for course entities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Csv implements ImportFormatInterface
{

    /**
     *
     * @var Import
     */
    protected $csvImporter;

    /**
     *
     * @param Import $csvImporter
     */
    public function __construct(Import $csvImporter)
    {
        $this->csvImporter = $csvImporter;
    }

    /**
     * Parses a file and returns CourseEntityRelation classes
     *
     * @param UploadedFile $file
     *@param \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult $importerResult
     *
     * @return \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseEntityRelation[]
     *
     * @throws \Exception
     */
    public function parseFile(UploadedFile $file, CourseEntityRelationImporterResult $importerResult)
    {
        $importedRows = $this->csvImporter->csv_to_array($file->getPathname());
        if (!is_array($importedRows))
        {
            throw new Exception('Could not parse the imported file, not a valid csv file.');
        }

        $importedCourseEntityRelations = [];

        if(empty($importedRows))
        {
            return [];
        }

        $keys = array_keys($importedRows[0]);
        $importerResult->setRawImportDataHeader(implode(';', $keys));

        if(in_array('username', $keys))
        {
            $importerResult->setUserRelationImportType();
        }
        elseif(in_array('groupcode', $keys))
        {
            $importerResult->setGroupRelationImportType();
        }
        else
        {
            throw new Exception('Could not determine whether it\'s a group or a user relation');
        }


        foreach ($importedRows as $row)
        {
            $rawImportData = implode(';', $row);

            if (array_key_exists('username', $row))
            {
                $importedCourseEntityRelations[] = new ImportedCourseUserRelation(
                    $rawImportData,
                    $row['action'],
                    $row['coursecode'],
                    $row['status'],
                    $row['username']
                );
            }
            elseif (array_key_exists('groupcode', $row))
            {
                $importedCourseEntityRelations[] = new ImportedCourseGroupRelation(
                    $rawImportData,
                    $row['action'],
                    $row['coursecode'],
                    $row['status'],
                    $row['groupcode']
                );
            }
        }

        return $importedCourseEntityRelations;
    }

    /**
     * Checks whether or not the current parser can parse the given file
     *
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function canParseFile(UploadedFile $file)
    {
        $acceptedMimeTypes = [
            'text/x-csv', 'text/csv', 'application/vnd.ms-excel', 'application/octet-stream',
            'application/force-download', 'text/comma-separated-values'
        ];

        return in_array($file->getClientMimeType(), $acceptedMimeTypes);
    }
}