<?php
namespace Chamilo\Application\Weblcms\Storage\Repository\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;

/**
 * Interface to determine the necessary functions for the publication repository
 * 
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PublicationRepositoryInterface
{

    /**
     * Finds publications for a given tool
     *
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationsByTool($tool);

    /**
     * Finds publications for a given course and tool
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationsByCourseAndTool(Course $course, $tool);

    /**
     * Finds publications for a given course, tool and category
     * 
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     * @param int $categoryId
     *
     * @return ContentObjectPublication[]
     */
    public function findPublicationsByCategoryId(Course $course, $tool, $categoryId);

    /**
     * Finds the publications for which the properties are set to visible by a given set of publication ids
     * 
     * @param int[] $publicationIds
     *
     * @return mixed
     */
    public function findVisiblePublicationsByIds(array $publicationIds = array());

    /**
     * Finds one publication by a given id
     * 
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function findPublicationById($publicationId);

    /**
     * Returns the users for who the content object is published
     * 
     * @param ContentObjectPublication $publication
     *
     * @return User[]
     */
    public function findTargetUsersForPublication(ContentObjectPublication $publication);

    /**
     * Returns the course groups for who the content object is published
     * 
     * @param ContentObjectPublication $publication
     *
     * @return CourseGroup[]
     */
    public function findTargetCourseGroupsForPublication(ContentObjectPublication $publication);

    /**
     * Returns the platform groups for who the content object is published
     * 
     * @param ContentObjectPublication $publication
     *
     * @return Group[]
     */
    public function findTargetPlatformGroupsForPublication(ContentObjectPublication $publication);

    /**
     * Finds publication categories for a given course and tool
     * 
     * @param Course $course
     * @param string $tool
     *
     * @return ContentObjectPublicationCategory[]
     */
    public function findPublicationCategoriesByCourseAndTool(Course $course, $tool);

    /**
     * Finds publication categories for a given course, tool and category
     * 
     * @param Course $course
     * @param string $tool
     * @param int $categoryId
     *
     * @return ContentObjectPublicationCategory[]
     */
    public function findPublicationCategoriesByParentCategoryId(Course $course, $tool, $categoryId);

    /**
     * Finds a publication category by a given id
     * 
     * @param int $categoryId
     *
     * @return ContentObjectPublicationCategory
     */
    public function findPublicationCategoryById($categoryId);

    /**
     * Returns the target users of a content object publication
     * 
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return array
     */
    public function findPublicationTargetUsers(ContentObjectPublication $contentObjectPublication);
}