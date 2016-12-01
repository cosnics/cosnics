<?php
namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity\Format;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ImportFormatInterface
{

    /**
     * Parses a file and returns ImportedCourseEntityRelation classes
     * 
     * @param UploadedFile $file
     *
     * @return ImportedCourseEntityRelation[]
     */
    public function parseFile(UploadedFile $file);

    /**
     * Returns the possible import mime types
     * 
     * @return string
     */
    public function getImportMimeTypes();
}