<?php

namespace Chamilo\Application\Weblcms\Service\Import\CourseEntity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseEntityImporterInterface
{
    /**
     * Imports course entities from a given file
     *
     * @param UploadedFile $file
     */
    public function importCourseEntitiesFromFile(UploadedFile $file);
}