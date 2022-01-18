<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain\ImportGroupStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class Importer
{
    protected CsvImportParser $csvImportParser;
    protected CourseGroupService $courseGroupService;

    public function __construct(CsvImportParser $csvImportParser, CourseGroupService $courseGroupService)
    {
        $this->csvImportParser = $csvImportParser;
        $this->courseGroupService = $courseGroupService;
    }

    /**
     * @param UploadedFile $file
     * @param Course $course
     * @param CourseGroup|null $parentCourseGroup
     *
     * @return ImportGroupStatus[]
     */
    public function importGroups(UploadedFile $file, Course $course, ?CourseGroup $parentCourseGroup = null)
    {
        $existingCourseGroupNames = [];
        $directChildren = $this->courseGroupService->getDirectChildrenFromGroup($parentCourseGroup);
        foreach ($directChildren as $directChild)
        {
            $existingCourseGroupNames[] = $directChild->get_name();
        }

        $importGroupStatuses = [];

        $parsedGroups = $this->csvImportParser->parseImportedFile($file);
        foreach ($parsedGroups as $parsedGroup)
        {
            if (in_array($parsedGroup->getTitle(), $existingCourseGroupNames))
            {
                $importGroupStatuses[] =
                    new ImportGroupStatus($parsedGroup->getTitle(), ImportGroupStatus::STATUS_SKIPPING);

                continue;
            }

            try
            {
                $this->courseGroupService->createCourseGroup(
                    $parsedGroup->getTitle(), $course->getId(),
                    $parentCourseGroup instanceof CourseGroup ? $parentCourseGroup->getId() : 0,
                    $parsedGroup->getDescription(), $parsedGroup->getMaximumMembers(),
                    $parsedGroup->isSelfRegistrationAllowed(), $parsedGroup->isSelfUnregistrationAllowed()
                );

                $importGroupStatuses[] =
                    new ImportGroupStatus($parsedGroup->getTitle(), ImportGroupStatus::STATUS_CREATED);
            }
            catch(\Exception $ex)
            {
                $importGroupStatuses[] =
                    new ImportGroupStatus($parsedGroup->getTitle(), ImportGroupStatus::STATUS_FAILED, $ex->getMessage());
            }
        }

        return $importGroupStatuses;
    }
}
