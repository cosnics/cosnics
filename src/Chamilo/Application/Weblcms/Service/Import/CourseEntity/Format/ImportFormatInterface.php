<?php
namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format;

use Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ImportFormatInterface
{
    /**
     * Parses a file and returns ImportedCourseEntityRelation classes
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\CourseEntityRelationImporterResult $importerResult
     *
     * @return \Chamilo\Application\Weblcms\Domain\Importer\CourseEntity\ImportedCourseEntityRelation[]
     */
    public function parseFile(UploadedFile $file, CourseEntityRelationImporterResult $importerResult);

    /**
     * Checks whether or not the current parser can parse the given file
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return bool
     */
    public function canParseFile(UploadedFile $file);
}