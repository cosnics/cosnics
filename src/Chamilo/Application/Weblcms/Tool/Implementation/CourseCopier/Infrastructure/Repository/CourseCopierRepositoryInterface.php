<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Interface for a course copier repository
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseCopierRepositoryInterface
{

    /**
     * Returns the root course group for a given course
     * 
     * @param int $courseId
     *
     * @return CourseGroup
     */
    public function findRootCourseGroupForCourse($courseId);

    /**
     * Finds publication categories by a given array of category ids, ordered by parent and display order
     * 
     * @param int[] $categoryIds
     *
     * @return ContentObjectPublicationCategory[]
     */
    public function findPublicationCategoriesByIds($categoryIds = []);

    /**
     * Finds a content object publication by a given id
     * 
     * @param int $contentObjectPublicationId
     *
     * @return ContentObjectPublication
     */
    public function findContentObjectPublicationById($contentObjectPublicationId);

    /**
     * Finds a possible extension object for a given content object publication
     * 
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return DataClass
     */
    public function findContentObjectPublicationExtension(ContentObjectPublication $contentObjectPublication);
}