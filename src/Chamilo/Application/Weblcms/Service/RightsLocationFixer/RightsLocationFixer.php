<?php

namespace Chamilo\Application\Weblcms\Service\RightsLocationFixer;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Storage\Repository\CourseRepository;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;

ini_set("memory_limit", "-1");
set_time_limit(0);

/**
 * Service to fix the rights location of a given course
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsLocationFixer
{
    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository
     */
    protected $rightsLocationRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\CourseRepository
     */
    protected $courseRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $currentLogger;

    /**
     * @var int
     */
    protected $missingLocationsCount = 0;

    /**
     * @var int
     */
    protected $wrongParentsCount = 0;

    /**
     * @var int
     */
    protected $wrongLeftRightValuesCount = 0;

    /**
     * @var int
     */
    protected $totalLocationsCount = 0;

    /**
     * @var int
     */
    protected $currentValueCounter = 1;

    /**
     * RightsLocationFixer constructor.
     *
     * @param \Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository $rightsLocationRepository
     * @param \Chamilo\Application\Weblcms\Storage\Repository\CourseRepository $courseRepository
     * @param \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository $publicationRepository
     */
    public function __construct(
        RightsLocationRepository $rightsLocationRepository,
        CourseRepository $courseRepository,
        PublicationRepository $publicationRepository
    )
    {
        $this->rightsLocationRepository = $rightsLocationRepository;
        $this->courseRepository = $courseRepository;
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * Fixes right locations for a given course, identified by his ID
     *
     * @param int $courseId
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function fixRightsLocationsForCourseId($courseId, LoggerInterface $logger)
    {
        $course = $this->courseRepository->findCourse($courseId);

        if (!$course instanceof Course)
        {
            throw new InvalidArgumentException('Could not find the course with id ' . $courseId);
        }

        $this->fixRightsLocations($course, $logger);
    }

    /**
     * Checks and fixes the rights locations for a given course
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function fixRightsLocations(Course $course, LoggerInterface $logger)
    {
        $this->currentLogger = $logger;

        $this->currentLogger->notice('Fixing locations for course ' . $course->getId());

        $rootLocation = $this->rightsLocationRepository->findRightsLocationInCourse(
            $course, WeblcmsRights::TYPE_ROOT, 0
        );

        if (!$rootLocation instanceof RightsLocation)
        {
            $this->currentLogger->notice('[MISSING LOCATION] Creating a new location for the root of the course');
            $rootLocation = $this->createRightsLocationInCourse($course, null, WeblcmsRights::TYPE_ROOT, 0);
        }

        $this->totalLocationsCount ++;

        $leftValue = $this->currentValueCounter;
        $this->fixRightsLocationsForCourseTools($course, $rootLocation);
        $this->currentValueCounter ++;

        $this->updateNestedTreeValues($rootLocation, null, $leftValue, $this->currentValueCounter);

        $this->currentLogger->notice('Total locations: ' . $this->totalLocationsCount);
        $this->currentLogger->notice('Missing locations: ' . $this->missingLocationsCount);
        $this->currentLogger->notice('Locations with wrong parent: ' . $this->wrongParentsCount);
        $this->currentLogger->notice('Locations with wrong left / right value: ' . $this->wrongLeftRightValuesCount);

        $this->resetCounters();
    }

    /**
     * Resets the counters
     */
    protected function resetCounters()
    {
        $this->missingLocationsCount = $this->wrongParentsCount = $this->totalLocationsCount = 0;
    }

    /**
     * Checks and fixes the rights locations for the tools of a course
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation $rootLocation
     */
    public function fixRightsLocationsForCourseTools(Course $course, RightsLocation $rootLocation)
    {
        $courseTools = $this->courseRepository->findToolRegistrations();
        foreach ($courseTools as $courseTool)
        {
            $toolLocation = $this->rightsLocationRepository->findRightsLocationInCourse(
                $course, WeblcmsRights::TYPE_COURSE_MODULE, $courseTool->getId()
            );

            if (!$toolLocation instanceof RightsLocation)
            {
                $this->currentLogger->notice(
                    '[MISSING LOCATION] Creating a new location for the tool ' . $courseTool->get_name()
                );

                $toolLocation = $this->createRightsLocationInCourse(
                    $course, $rootLocation, WeblcmsRights::TYPE_COURSE_MODULE, $courseTool->getId()
                );
            }

            $this->totalLocationsCount ++;

            $leftValue = ++ $this->currentValueCounter;
            $this->fixRightsLocationsForCategoriesAndPublications($course, $toolLocation, $courseTool);
            $this->currentValueCounter ++;

            $this->updateNestedTreeValues($toolLocation, $rootLocation, $leftValue, $this->currentValueCounter);
        }
    }

    /**
     * Checks and fixes the rights locations in a given course tool for a given course
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation $parentLocation
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool $courseTool
     * @param int $categoryId
     */
    protected function fixRightsLocationsForCategoriesAndPublications(
        Course $course, RightsLocation $parentLocation, CourseTool $courseTool, $categoryId = 0
    )
    {
        $publications = $this->publicationRepository->findPublicationsByCategoryId(
            $course, $courseTool->get_name(), $categoryId
        );

        foreach ($publications as $publication)
        {
            $publicationLocation = $this->rightsLocationRepository->findRightsLocationInCourse(
                $course, WeblcmsRights::TYPE_PUBLICATION, $publication->getId()
            );

            if (!$publicationLocation instanceof RightsLocation)
            {
                $this->currentLogger->notice(
                    '[MISSING LOCATION] Creating a new location for the publication ' . $publication->getId()
                );

                $publicationLocation = $this->createRightsLocationInCourse(
                    $course, $parentLocation, WeblcmsRights::TYPE_PUBLICATION, $publication->getId()
                );
            }

            $this->totalLocationsCount ++;

            $leftValue = ++ $this->currentValueCounter;
            $this->currentValueCounter ++;
            $this->updateNestedTreeValues(
                $publicationLocation, $parentLocation, $leftValue, $this->currentValueCounter
            );
        }

        $categories = $this->publicationRepository->findPublicationCategoriesByParentCategoryId(
            $course, $courseTool->get_name(), $categoryId
        );

        foreach ($categories as $category)
        {
            $categoryLocation = $this->rightsLocationRepository->findRightsLocationInCourse(
                $course, WeblcmsRights::TYPE_COURSE_CATEGORY, $category->getId()
            );

            if (!$categoryLocation instanceof RightsLocation)
            {
                $this->currentLogger->notice(
                    '[MISSING LOCATION] Creating a new location for the publication category ' . $category->getId()
                );

                $categoryLocation = $this->createRightsLocationInCourse(
                    $course, $parentLocation, WeblcmsRights::TYPE_COURSE_CATEGORY, $category->getId()
                );
            }

            $this->totalLocationsCount ++;

            $leftValue = ++ $this->currentValueCounter;
            $this->fixRightsLocationsForCategoriesAndPublications($course, $categoryLocation, $courseTool, $category->getId());
            $this->currentValueCounter ++;

            $this->updateNestedTreeValues($categoryLocation, $parentLocation, $leftValue, $this->currentValueCounter);
        }
    }

    /**
     * Creates a rights location in the given course with a type, identifier and parent location
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation|null $parentRightsLocation
     * @param int $type
     * @param int $identifier
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation
     */
    protected function createRightsLocationInCourse(
        Course $course, RightsLocation $parentRightsLocation = null, $type = WeblcmsRights::TYPE_ROOT, $identifier = 0
    )
    {
        $this->missingLocationsCount ++;

        $rightsLocation = new RightsLocation();

        $parentId = ($parentRightsLocation instanceof RightsLocation) ? $parentRightsLocation->getId() : 0;
        $rightsLocation->set_parent_id($parentId);
        $rightsLocation->set_tree_type(WeblcmsRights::TREE_TYPE_COURSE);
        $rightsLocation->set_tree_identifier($course->getId());
        $rightsLocation->set_type($type);
        $rightsLocation->set_identifier($identifier);
        $rightsLocation->set_left_value(0);
        $rightsLocation->set_right_value(0);
        $rightsLocation->set_inherit(true);
        $rightsLocation->set_locked(false);

        if (!$this->rightsLocationRepository->createRightsLocationDirectlyInDatabase($rightsLocation))
        {
            throw new RuntimeException(
                sprintf(
                    'Could not create the rights location for course %s, parentLocation %s, type %s and identifier %s',
                    $course->getId(), $parentId, $type, $identifier
                )
            );
        }

        return $rightsLocation;
    }

    /**
     * Checks and possibly updates the parent location of a given location when it differs
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation $targetLocation
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation $parentLocation
     * @param int $leftValue
     * @param int $rightValue
     */
    protected function updateNestedTreeValues(
        RightsLocation $targetLocation, RightsLocation $parentLocation = null, $leftValue = 1, $rightValue = 2
    )
    {
        $changed = false;

        if ($parentLocation instanceof RightsLocation && $targetLocation->get_parent_id() != $parentLocation->getId())
        {
            $targetLocation->set_parent_id($parentLocation->getId());
            $this->wrongParentsCount ++;
            $changed = true;

            $this->currentLogger->notice(
                sprintf(
                    '[MISSING LOCATION] Changing the parent location for the location %s to %s ',
                    $targetLocation->getId(), $parentLocation->getId()
                )
            );
        }

        if ($targetLocation->get_left_value() != $leftValue || $targetLocation->get_right_value() != $rightValue)
        {
            $targetLocation->set_left_value($leftValue);
            $targetLocation->set_right_value($rightValue);
            $this->wrongLeftRightValuesCount ++;
            $changed = true;

            $this->currentLogger->notice(
                sprintf(
                    '[MISSING LOCATION] Changing left and / or right value of location %s to LEFT %s and RIGHT %s ',
                    $targetLocation->getId(), $leftValue, $rightValue
                )
            );
        }

        if ($changed)
        {
            if (!$this->rightsLocationRepository->updateRightsLocationDirectlyInDatabase($targetLocation))
            {
                throw new RuntimeException(
                    sprintf(
                        'Could not change the parent location from location %s to parent location %s',
                        $targetLocation->getId(), $parentLocation->getId()
                    )
                );
            }
        }
    }
}
