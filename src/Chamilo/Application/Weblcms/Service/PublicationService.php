<?php
namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\Repository\Interfaces\PublicationRepositoryInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\PublicationServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\RightsServiceInterface;

/**
 * Service to manage publications
 * 
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationService implements PublicationServiceInterface
{

    /**
     * The course service
     * 
     * @var CourseServiceInterface
     */
    private $courseService;

    /**
     * The weblcms rights service
     * 
     * @var RightsServiceInterface
     */
    private $rightsService;

    /**
     * The publication repository
     * 
     * @var PublicationRepositoryInterface
     */
    private $publicationRepository;

    /**
     * Constructor
     * 
     * @param PublicationRepositoryInterface $publicationRepository
     */
    public function __construct(PublicationRepositoryInterface $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * Setter injector for this dependency due to a cyclic dependency issue
     * 
     * @param RightsServiceInterface $rightsService
     *
     * @return self
     */
    public function setRightsService(RightsServiceInterface $rightsService)
    {
        $this->rightsService = $rightsService;
        
        return $this;
    }

    /**
     * Setter injector for this dependency due to a cyclic dependency issue
     * 
     * @param CourseServiceInterface $courseService
     *
     * @return self
     */
    public function setCourseService(CourseServiceInterface $courseService)
    {
        $this->courseService = $courseService;
        
        return $this;
    }

    /**
     * **************************************************************************************************************
     * Publication Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns a publication by a given id
     * 
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     */
    public function getPublication($publicationId)
    {
        return $this->publicationRepository->findPublicationById($publicationId);
    }

    /**
     * Returns a publication by a given id with rights checks for the given user
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $publicationId
     *
     * @return ContentObjectPublication
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getPublicationForUser(User $user, $publicationId)
    {
        $contentObjectPublication = $this->publicationRepository->findPublicationById($publicationId);
        if (!$contentObjectPublication)
        {
            throw new ObjectNotExistException('ContentObjectPublication', $publicationId);
        }

        $course = $this->courseService->getCourseById($contentObjectPublication->get_course_id());
        if (!$course)
        {
            throw new ObjectNotExistException('Course', $contentObjectPublication->get_course_id());
        }

        if (!$this->rightsService->canUserViewPublication($user, $contentObjectPublication, $course))
        {
            throw new NotAllowedException();
        }

        return $contentObjectPublication;
    }

    /**
     * @param Course $course
     *
     * @return ContentObjectPublication[]
     */
    public function getPublicationsByCourse(Course $course)
    {
        return $this->publicationRepository->findPublicationsByCourse($course);
    }

    /**
     * Returns the publications for a given tool
     *
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     */
    public function getPublicationsByTool($tool)
    {
        return $this->publicationRepository->findPublicationsByTool($tool);
    }

    /**
     * Returns the publications for a given course and tool
     *
     * @param Course $course
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     */
    public function getPublicationsByCourseAndTool(Course $course, $tool)
    {
        return $this->publicationRepository->findPublicationsByCourseAndTool($course, $tool);
    }

    /**
     * Returns the publication categories for a given course, tool and category
     *
     * @param Course $course
     * @param $tool
     * @param ContentObjectPublicationCategory $category
     *
     * @return ContentObjectPublication[]
     */
    public function getPublicationsByCourseAndToolAndCategory(
        Course $course, $tool,
        ContentObjectPublicationCategory $category = null
    )
    {
        $categoryId = is_null($category) ? 0 : $category->get_id();

        return $this->publicationRepository->findPublicationsByCategoryId($course, $tool, $categoryId);
    }

    /**
     * Returns the users for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return User[]
     */
    public function getTargetUsersForPublication(ContentObjectPublication $publication)
    {
        return $this->publicationRepository->findTargetUsersForPublication($publication);
    }

    /**
     * Returns the number of users for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return int
     */
    public function countTargetUsersForPublication(ContentObjectPublication $publication)
    {
        return count($this->getTargetUsersForPublication($publication));
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return int[]
     */
    public function getTargetUserIdsForPublication(ContentObjectPublication $publication)
    {
        return $this->publicationRepository->findTargetUserIdsForPublication($publication);
    }

    /**
     * Returns the course groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return CourseGroup[]
     */
    public function getTargetCourseGroupsForPublication(ContentObjectPublication $publication)
    {
        return $this->publicationRepository->findTargetCourseGroupsForPublication($publication);
    }

    /**
     * Returns the number of course groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return int
     */
    public function countTargetCourseGroupsForPublication(ContentObjectPublication $publication)
    {
        return count($this->getTargetCourseGroupsForPublication($publication));
    }

    /**
     * Returns the platform groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return Group[]
     */
    public function getTargetPlatformGroupsForPublication(ContentObjectPublication $publication)
    {
        return $this->publicationRepository->findTargetPlatformGroupsForPublication($publication);
    }

    /**
     * Returns the number of platform groups for who the content object is published
     *
     * @param ContentObjectPublication $publication
     *
     * @return int
     */
    public function countTargetPlatformGroupsForPublication(ContentObjectPublication $publication)
    {
        return count($this->getTargetPlatformGroupsForPublication($publication));
    }

    /**
     * Returns the publications for a given course and tool which are accessible by the given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param Course $course
     * @param string $tool
     *
     * @return ContentObjectPublication[]
     * @throws NotAllowedException
     *
     */
    public function getPublicationsForUser(\Chamilo\Core\User\Storage\DataClass\User $user, Course $course, $tool)
    {
        if ($this->courseService->isUserTeacherInCourse($user, $course) || $user->is_platform_admin())
        {
            return $this->getPublicationsByCourseAndTool($course, $tool);
        }

        if (!$this->rightsService->canUserViewTool($user, $tool, $course))
        {
            return array();
        }

        $contentObjectPublicationIds =
            $this->rightsService->getPublicationIdsWithViewRightInTool($user, $tool, $course);

        $contentObjectPublicationCategories = $this->getPublicationCategoriesForUser($user, $course, $tool);

        foreach ($contentObjectPublicationCategories as $contentObjectPublicationCategory)
        {
            $contentObjectPublicationIds = array_merge(
                $contentObjectPublicationIds,
                $this->rightsService->getPublicationIdsWithViewRightInCategory(
                    $user,
                    $contentObjectPublicationCategory,
                    $course
                )
            );
        }

        return $this->publicationRepository->findVisiblePublicationsByIds($contentObjectPublicationIds);
    }

    /**
     * Returns the publications for a given course, tool and category which are accessible by the given user.
     * If no category is given the publications from the tool root are returned.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param Course $course
     * @param $tool
     * @param ContentObjectPublicationCategory $category
     *
     * @return ContentObjectPublication[]
     */
    public function getPublicationsForUserInCategory(
        \Chamilo\Core\User\Storage\DataClass\User $user, Course $course,
        $tool, ContentObjectPublicationCategory $category = null
    )
    {
        if ($this->courseService->isUserTeacherInCourse($user, $course) || $user->is_platform_admin())
        {
            return $this->getPublicationsByCourseAndToolAndCategory($course, $tool, $category);
        }

        if (!$category)
        {
            if (!$this->rightsService->canUserViewTool($user, $tool, $course))
            {
                return array();
            }

            $publicationIds = $this->rightsService->getPublicationIdsWithViewRightInTool($user, $tool, $course);
        }
        else
        {
            if (!$this->rightsService->canUserViewPublicationCategory($user, $category, $course))
            {
                return array();
            }

            $publicationIds = $this->rightsService->getPublicationIdsWithViewRightInCategory($user, $category, $course);
        }

        return $this->publicationRepository->findVisiblePublicationsByIds($publicationIds);
    }

    /**
     * @param User $user
     * @param Course $course
     * @param ContentObject $contentObject
     * @param string $tool
     * @param int $categoryId
     * @param bool $hidden
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @param bool $showOnHomepage
     * @param bool $allowCollaboration
     */
    public function createPublication(
        User $user, Course $course, ContentObject $contentObject, string $tool,
        int $categoryId = 0, bool $hidden = false, \DateTime $from = null, \DateTime $to = null,
        bool $showOnHomepage = false, bool $allowCollaboration = true
    )
    {
        $publication = new ContentObjectPublication();
        $publication->set_content_object_id($contentObject->getId());
        $publication->setContentObject($contentObject);
        $publication->set_course_id($course->getId());
        $publication->set_tool($tool);
        $publication->set_publisher_id($user->getId());
        $publication->set_publication_publisher($user);
        $publication->set_category_id($categoryId);
        $publication->set_from_date($from instanceof \DateTime ? $from->getTimestamp() : 0);
        $publication->set_to_date($to instanceof \DateTime ? $to->getTimestamp() : 0);
        $publication->set_publication_date(time());
        $publication->set_modified_date(time());
        $publication->set_hidden($hidden);
        $publication->set_show_on_homepage($showOnHomepage);
        $publication->set_allow_collaboration($allowCollaboration);

        if(!$this->publicationRepository->createPublication($publication))
        {
            throw new \RuntimeException('Could not create the publication in the database');
        }

        return $publication;
    }

    /**
     * **************************************************************************************************************
     * PublicationCategory Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the categories for a given course and tool
     *
     * @param Course $course
     *
     * @return ContentObjectPublicationCategory[]
     */
    public
    function getPublicationCategoriesForCourse(
        Course $course
    )
    {
        return $this->publicationRepository->findPublicationCategoriesByCourse($course);
    }

    /**
     * Returns the categories for a given course and tool
     *
     * @param Course $course
     * @param string $tool
     *
     * @return ContentObjectPublicationCategory[]
     */
    public
    function getPublicationCategoriesForCourseAndTool(
        Course $course, $tool
    )
    {
        return $this->publicationRepository->findPublicationCategoriesByCourseAndTool($course, $tool);
    }

    /**
     * @param Course $course
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory|null $parentCategory
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory[]
     */
    public
    function getPublicationCategoriesForCourseAndToolInCategory(
        Course $course, string $tool, ContentObjectPublicationCategory $parentCategory = null
    )
    {


        return $this->publicationRepository->findPublicationCategoriesByParentCategoryId(
            $course,
            $tool,
            is_null($parentCategory) ? 0 : $parentCategory->getId()
        );
    }

    /**
     * Returns the publication categories which a user can access
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param Course $course
     * @param $tool
     *
     * @return ContentObjectPublicationCategory[]
     */
    public
    function getPublicationCategoriesForUser(
        \Chamilo\Core\User\Storage\DataClass\User $user, Course $course,
        $tool
    )
    {
        $contentObjectPublicationCategories = $this->getPublicationCategoriesForCourseAndTool($course, $tool);

        return $this->filterAccessibleCategoriesForUser($user, $course, $contentObjectPublicationCategories);
    }

    /**
     * Returns the child publication categories for a user in a given category.
     * If no category is given the root
     * categories are returned
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param Course $course
     * @param $tool
     * @param ContentObjectPublicationCategory $category
     *
     * @return ContentObjectPublicationCategory[]
     */
    public
    function getPublicationCategoriesForUserInCategory(
        \Chamilo\Core\User\Storage\DataClass\User $user,
        Course $course, $tool, ContentObjectPublicationCategory $category = null
    )
    {
        $categoryId = is_null($category) ? 0 : $category->get_id();

        $contentObjectPublicationCategories = $this->publicationRepository->findPublicationCategoriesByParentCategoryId(
            $course,
            $tool,
            $categoryId
        );

        return $this->filterAccessibleCategoriesForUser($user, $course, $contentObjectPublicationCategories);
    }

    /**
     * Filters a list of categories to find the categories for which the given user has access
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param Course $course
     * @param ContentObjectPublicationCategory[] $contentObjectPublicationCategories
     *
     * @return ContentObjectPublicationCategory[]
     */
    protected
    function filterAccessibleCategoriesForUser(
        \Chamilo\Core\User\Storage\DataClass\User $user, Course $course,
        array $contentObjectPublicationCategories
    )
    {
        $categoriesForUser = array();

        foreach ($contentObjectPublicationCategories as $contentObjectPublicationCategory)
        {
            if (!$this->rightsService->canUserViewPublicationCategory(
                $user,
                $contentObjectPublicationCategory,
                $course
            ))
            {
                continue;
            }

            $categoriesForUser[] = $contentObjectPublicationCategory;
        }

        return $categoriesForUser;
    }

    /**
     * Returns a category by a given id
     *
     * @param int $categoryId
     *
     * @return ContentObjectPublicationCategory
     */
    public
    function getPublicationCategoryById(
        $categoryId
    )
    {
        return $this->publicationRepository->findPublicationCategoryById($categoryId);
    }

    /**
     * Returns a category by a given id with rights checks for the given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $categoryId
     *
     * @return ContentObjectPublicationCategory
     * @throws ObjectNotExistException
     *
     * @throws NotAllowedException
     */
    public
    function getPublicationCategoryForUser(
        User $user, $categoryId
    )
    {
        $publicationCategory = $this->publicationRepository->findPublicationCategoryById($categoryId);

        if (!$publicationCategory)
        {
            throw new ObjectNotExistException('category', $categoryId);
        }

        $course = $this->courseService->getCourseById($publicationCategory->get_course());

        if (!$course)
        {
            throw new ObjectNotExistException('course', $publicationCategory->get_course());
        }

        if (!$this->rightsService->canUserViewPublicationCategory($user, $publicationCategory, $course))
        {
            throw new NotAllowedException();
        }

        return $publicationCategory;
    }
}
