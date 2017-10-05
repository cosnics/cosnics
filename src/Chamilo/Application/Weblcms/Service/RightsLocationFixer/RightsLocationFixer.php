<?php

namespace Chamilo\Application\Weblcms\Service\RightsLocationFixer;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Psr\Log\LoggerInterface;

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
     * Checks and fixes the rights locations for a given course
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function fixRightsLocations(Course $course, LoggerInterface $logger)
    {
        $rootLocation = $this->rightsLocationRepository->findRightsLocationInCourse(
            $course, WeblcmsRights::TYPE_ROOT, 0
        );

        if (!$rootLocation instanceof RightsLocation)
        {
            $rootLocation = $this->createRightsLocationInCourse($course, null, WeblcmsRights::TYPE_ROOT, 0);
        }

        $this->fixRightsLocationsForCourseTools($course, $rootLocation);
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
                $toolLocation = $this->createRightsLocationInCourse(
                    $course, $rootLocation->getId(), WeblcmsRights::TYPE_COURSE_MODULE, $courseTool->getId()
                );
            }

            $this->updateParentLocation($toolLocation, $rootLocation);

            $this->fixRightsLocationsInCourseTool($course, $toolLocation, $courseTool);
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
    protected function fixRightsLocationsInCourseTool(
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
                $publicationLocation = $this->createRightsLocationInCourse(
                    $course, $parentLocation->getId(), WeblcmsRights::TYPE_PUBLICATION, $publication->getId()
                );
            }

            $this->updateParentLocation($publicationLocation, $parentLocation);
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
                $categoryLocation = $this->createRightsLocationInCourse(
                    $course, $parentLocation->getId(), WeblcmsRights::TYPE_COURSE_CATEGORY, $category->getId()
                );
            }

            $this->updateParentLocation($categoryLocation, $parentLocation);

            $this->fixRightsLocationsInCourseTool($course, $categoryLocation, $courseTool, $category->getId());
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
            throw new \RuntimeException(
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
     */
    protected function updateParentLocation(RightsLocation $targetLocation, RightsLocation $parentLocation)
    {
        if ($targetLocation->get_parent_id() != $parentLocation->getId())
        {
            $targetLocation->set_parent_id($parentLocation->getId());

            if (!$this->rightsLocationRepository->updateRightsLocationDirectlyInDatabase($targetLocation))
            {
                throw new \RuntimeException(
                    sprintf(
                        'Could not change the parent location from location %s to parent location %s',
                        $targetLocation->getId(), $parentLocation->getId()
                    )
                );
            }
        }
    }
}

//function fix_locations_in_category(
//    $course_id, $type, $identifier, $parent_location_id, &$missing_location_counter, &$wrong_parent_counter, $tool_name
//)
//{
//    if ($type != 0)
//    {
//        //fix it's content objects
//        if ($type == 4) //category
//        {
//            $content_objects_sql = 'SELECT id
//        FROM  `weblcms_content_object_publication` WHERE category_id=' . $identifier . ' AND course_id = ' . $course_id;
//        }
//        if ($type == 3) //tool
//        {
//            $content_objects_sql = "SELECT id
//        FROM  `weblcms_content_object_publication` WHERE category_id=0 AND course_id = " . $course_id . " AND tool='" .
//                $tool_name . "'";
//        }//echo "\n".$content_objects_sql."\n";
//
//        $stmt = $conn->prepare($content_objects_sql);
//        $stmt->execute();
//        $content_objects = $stmt->fetchAll();
//        foreach ($content_objects as $content_objects_row)
//        {
//            $content_object_id = $content_objects_row["id"];
//            echo 'content object id: ' . $content_object_id;
//            echo "\n";
//
//            //retrieve their corresponding location id
//            $content_object_location_sql = 'SELECT id, parent_id
//        FROM  `weblcms_rights_location`
//        WHERE  `tree_identifier` =  ' . $course_id . '
//        AND  `type` = 5
//        AND  `tree_type` = 1 AND `identifier` = ' . $content_object_id;
//            $stmt = $conn->prepare($content_object_location_sql);
//            $stmt->execute();
//            $location_result = $stmt->fetchAll();
//
//            if (count($location_result) == 0)
//            {
//                echo "No content object location found! Creating...";
//                $missing_location_counter ++;
//                $create_sql = "INSERT INTO `weblcms_rights_location`
//        (`id`, `left_value`, `right_value`, `parent_id`, `inherit`, `locked`, `identifier`, `tree_identifier`, `type`, `tree_type`)
//        VALUES (NULL, '', '', '" . $location_row["parent_id"] . "', '1', '0', '" . $content_object_id . "', '" .
//                    $course_id . "', '5', '1')";
//                $stmt = $conn->prepare($create_sql);
//                $stmt->execute();
//                echo "\n";
//            }
//            else
//            {
//                echo "Content object location found :";
//                $location_row = $location_result[0];
//                $content_object_location_id = $location_row["id"];
//                echo $content_object_location_id;
//                echo "\n";
//                //if the parent id differs from the $parent_location_id, then this needs to be updated (this happens when the parent location was regenerated)
//                if ($location_row["parent_id"] != $new_parent_location_id)
//                {
//                    $wrong_parent_counter ++;
//
//                    echo "parent location ( " . $new_parent_location_id .
//                        ") was created, so parent_id needs to be updated";
//                    echo "\n";
//                    $update_sql = "UPDATE `weblcms_rights_location` SET  `parent_id` =  " . $new_parent_location_id . "
//                            WHERE  `weblcms_rights_location`.`id` =" . $content_object_location_id;
//                    $stmt = $conn->prepare($update_sql);
//                    $stmt->execute();
//                }
//            }
//        }
//    }
//
//    //fix child containers
//    if ($type == 0) //childs are tools
//    {
//        $child_containers_sql = 'SELECT id, name FROM weblcms_course_tool';
//    }
//    else
//    {
//        if ($type == 3)//child is first category in tool
//        {
//            $child_containers_sql =
//                'SELECT id FROM weblcms_content_object_publication_category WHERE course_id = ' . $course_id . " AND
//            parent_id = 0 AND tool ='" . $tool_name . "'";
//        }
//        else //child is subcategory
//        {
//            $child_containers_sql =
//                'SELECT id FROM weblcms_content_object_publication_category WHERE course_id = ' . $course_id . ' AND
//            parent_id =' . $identifier;
//        }
//    }
//
//    $stmt = $conn->prepare($child_containers_sql);
//    $stmt->execute();
//    $child_containers_result = $stmt->fetchAll();
//
//    foreach ($child_containers_result as $child_containers_row)
//    {
//
//        $identifier = $child_containers_row["id"];
//        $tool_name = $child_containers_row["name"];
//
//        if ($type == 0)
//        {
//            fix_locations_in_category(
//                $course_id, 3, $identifier, $new_parent_location_id, $missing_location_counter, $wrong_parent_counter,
//                $tool_name
//            );
//        }
//        else
//        {
//            fix_locations_in_category(
//                $course_id, 4, $identifier, $new_parent_location_id, $missing_location_counter, $wrong_parent_counter,
//                $tool_name
//            );
//        }
//    }
//
//    echo "results: \n";
//    echo "number of missing locations: " . (int) $missing_location_counter;
//    echo "\n";
//    echo "number of wrong parent locations: " . (int) $wrong_parent_counter;
//    echo "\n";
//}

