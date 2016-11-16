<?php
namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format;

use Chamilo\Application\Weblcms\Domain\ValueObject\ImportedCourseGroupRelation;
use Chamilo\Application\Weblcms\Domain\ValueObject\ImportedCourseUserRelation;
use Chamilo\Libraries\File\Import;
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
     *
     * @return ImportedCourseEntityRelation[]
     *
     * @throws \Exception
     */
    public function parseFile(UploadedFile $file)
    {
        $importedRows = $this->csvImporter->csv_to_array($file->getPathname());
        if (! is_array($importedRows))
        {
            throw new \Exception('Could not parse the imported file, not a valid csv file.');
        }
        
        $importedCourseEntityRelations = array();
        
        foreach ($importedRows as $row)
        {
            if (array_key_exists('username', $row))
            {
                $importedCourseEntityRelations[] = new ImportedCourseUserRelation(
                    $row['action'], 
                    $row['coursecode'], 
                    $row['status'], 
                    $row['username']);
            }
            elseif (array_key_exists('groupcode', $row))
            {
                $importedCourseEntityRelations[] = new ImportedCourseGroupRelation(
                    $row['action'], 
                    $row['coursecode'], 
                    $row['status'], 
                    $row['groupcode']);
            }
        }
        
        return $importedCourseEntityRelations;
    }

    /**
     * Returns the possible import mime types
     * 
     * @return string
     */
    public function getImportMimeTypes()
    {
        return array('application/vnd.ms-excel', 'text/csv', 'text/comma-separated-values');
    }
}