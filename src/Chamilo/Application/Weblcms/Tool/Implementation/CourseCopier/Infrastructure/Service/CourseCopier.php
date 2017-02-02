<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Repository\CourseCopierRepositoryInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupServiceInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Course Copier service
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseCopier implements CourseCopierInterface
{

    /**
     *
     * @var CourseCopierRepositoryInterface
     */
    protected $courseCopierRepository;

    /**
     *
     * @var CourseGroupServiceInterface
     */
    protected $courseGroupService;

    /**
     * CourseCopier constructor.
     * 
     * @param CourseCopierRepositoryInterface $courseCopierRepository
     * @param CourseGroupServiceInterface $courseGroupService
     */
    public function __construct(CourseCopierRepositoryInterface $courseCopierRepository, 
        CourseGroupServiceInterface $courseGroupService)
    {
        $this->courseCopierRepository = $courseCopierRepository;
        $this->courseGroupService = $courseGroupService;
    }

    /**
     * Copies the course by the given parameters
     * 
     * @param User $user
     * @param Course $currentCourse
     * @param int[] $targetCourseIds
     * @param int[] $selectedContentObjectPublicationIds
     * @param int[] $selectedPublicationCategoryIds
     * @param bool $ignoreCategories
     * @param bool $copyCourseGroups
     */
    public function copyCourse(User $user, Course $currentCourse, $targetCourseIds = array(), 
        $selectedContentObjectPublicationIds = array(), $selectedPublicationCategoryIds = array(), $ignoreCategories = false, 
        $copyCourseGroups = true)
    {
        if ($ignoreCategories)
        {
            $this->copyContentObjectPublications($targetCourseIds, $selectedContentObjectPublicationIds);
        }
        else
        {
            $this->copyContentObjectPublicationsAndCategories(
                $targetCourseIds, 
                $selectedContentObjectPublicationIds, 
                $selectedPublicationCategoryIds);
        }
        
        if ($copyCourseGroups)
        {
            $this->copyCourseGroups($currentCourse, $targetCourseIds, $user);
        }
    }

    /**
     * Copies the content object publications to the given target courses
     * 
     * @param int[] $targetCourseIds
     * @param int[] $selectedContentObjectPublicationIds
     * @param array $categoryIdMapping
     */
    protected function copyContentObjectPublications($targetCourseIds = array(), $selectedContentObjectPublicationIds = array(), 
        $categoryIdMapping = array())
    {
        foreach ($selectedContentObjectPublicationIds as $publicationId)
        {
            $contentObjectPublication = $this->courseCopierRepository->findContentObjectPublicationById($publicationId);

            $publicationExtension = $this->courseCopierRepository->findContentObjectPublicationExtension(
                $contentObjectPublication);

            if(!$publicationExtension instanceof DataClass)
            {
                $publicationExtension = null;
            }

            $parentId = $contentObjectPublication->get_category_id();
            $oldId = $contentObjectPublication->getId();
            
            foreach ($targetCourseIds as $courseId)
            {
                try
                {
                    $this->copyContentObjectPublicationToCourse(
                        $contentObjectPublication, 
                        $courseId, 
                        $parentId, 
                        $oldId, 
                        $publicationExtension, 
                        $categoryIdMapping);
                }
                catch (\Exception $ex)
                {
                }
            }
        }
    }

    /**
     * Copies the content object publications and publication categories to the given target courses
     * 
     * @param int[] $targetCourseIds
     * @param int[] $selectedContentObjectPublicationIds
     * @param int[] $selectedPublicationCategoryIds
     */
    protected function copyContentObjectPublicationsAndCategories($targetCourseIds = array(), 
        $selectedContentObjectPublicationIds = array(), $selectedPublicationCategoryIds = array())
    {
        $categoryIdMapping = $this->copyPublicationCategories($targetCourseIds, $selectedPublicationCategoryIds);
        
        $this->copyContentObjectPublications($targetCourseIds, $selectedContentObjectPublicationIds, $categoryIdMapping);
    }

    /**
     * Copies a single content object publication to the given course
     * 
     * @param ContentObjectPublication $contentObjectPublication
     * @param int $courseId
     * @param int $parentId
     * @param int $oldId
     * @param DataClass $publicationExtension
     * @param array $categoryIdMapping
     *
     * @throws \Exception
     */
    protected function copyContentObjectPublicationToCourse(ContentObjectPublication $contentObjectPublication, 
        $courseId, $parentId, $oldId, DataClass $publicationExtension = null, $categoryIdMapping = array())
    {
        $contentObjectPublication->setId(null);
        $contentObjectPublication->set_course_id($courseId);
        
        if ($parentId != 0)
        {
            $contentObjectPublication->set_category_id($categoryIdMapping[$courseId][$parentId]);
        }
        
        if (! $contentObjectPublication->create())
        {
            throw new \Exception('Could not copy the content object publication with id ' . $oldId);
        }
        
        if ($publicationExtension instanceof DataClass)
        {
            $publicationExtension->set_publication_id($contentObjectPublication->get_id());
            if (! $publicationExtension->create())
            {
                throw new \Exception(
                    'Could not copy the content object publication extension class for ' .
                         'content object publication with id ' . $oldId);
            }
        }
    }

    /**
     * Copies the categories to other courses.
     * 
     * @param int[] $targetCourseIds
     * @param int[] $categoryIds
     *
     * @return array|bool
     *
     * @throws \Exception
     */
    protected function copyPublicationCategories($targetCourseIds, $categoryIds)
    {
        $categoryIdMapping = array();
        // Retrieve all publication categories and copy them using recursive method
        $publicationCategories = $this->courseCopierRepository->findPublicationCategoriesByIds($categoryIds);
        
        foreach ($publicationCategories as $publicationCategory)
        {
            $parentId = $publicationCategory->get_parent();
            $oldId = $publicationCategory->getId();
            
            foreach ($targetCourseIds as $courseId)
            {
                try
                {
                    $publicationCategory = $this->copyPublicationCategoryToCourse(
                        $publicationCategory, 
                        $courseId, 
                        $parentId, 
                        $categoryIdMapping, 
                        $oldId);
                    
                    $categoryIdMapping[$courseId][$oldId] = $publicationCategory->getId();
                }
                catch (\Exception $ex)
                {
                }
            }
        }
        
        return $categoryIdMapping;
    }

    /**
     * Copies a single publication category to a given course
     * 
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param int $course_code
     * @param int $parentId
     * @param array $categoryIdMapping
     * @param int $oldId
     *
     * @return ContentObjectPublicationCategory
     *
     * @throws \Exception
     */
    protected function copyPublicationCategoryToCourse($publicationCategory, $course_code, $parentId, $categoryIdMapping, 
        $oldId)
    {
        $publicationCategory->setId(null);
        $publicationCategory->set_allow_change(true);
        $publicationCategory->set_course($course_code);
        
        if ($parentId != 0)
        {
            $publicationCategory->set_parent($categoryIdMapping[$course_code][$parentId]);
        }
        
        if (! $publicationCategory->create())
        {
            throw new \Exception('Could not copy category with id ' . $oldId);
        }
        
        return $publicationCategory;
    }

    /**
     * Copies the course groups from the current course to the given target courses
     * 
     * @param Course $currentCourse
     * @param int[] $targetCourseIds
     * @param User $user
     *
     * @throws \Exception
     */
    protected function copyCourseGroups(Course $currentCourse, $targetCourseIds = array(), User $user)
    {
        $currentRootCourseGroup = $this->courseCopierRepository->findRootCourseGroupForCourse($currentCourse->getId());
        
        $courseGroups = $currentRootCourseGroup->get_children(false);
        
        foreach ($targetCourseIds as $targetCourseId)
        {
            $targetCourseRootCourseGroup = $this->courseCopierRepository->findRootCourseGroupForCourse($targetCourseId);
            
            while ($courseGroup = $courseGroups->next_result())
            {
                try
                {
                    $this->copyCourseGroupsToNewParent(
                        $courseGroup, 
                        $targetCourseId, 
                        $targetCourseRootCourseGroup, 
                        $user);
                }
                catch (\Exception $ex)
                {
                }
            }
        }
    }

    /**
     * Copies a list of course groups to a new given parent in a target course
     * 
     * @param CourseGroup $courseGroup
     * @param $targetCourseId
     * @param CourseGroup $parentCourseGroup
     *
     * @param User $user
     *
     * @throws \Exception
     */
    protected function copyCourseGroupsToNewParent(CourseGroup $courseGroup, $targetCourseId, 
        CourseGroup $parentCourseGroup, User $user)
    {
        $oldId = $courseGroup->getId();
        
        $courseGroupChildren = $courseGroup->get_children(false);
        
        $courseGroup->setId(null);
        $courseGroup->set_course_code($targetCourseId);
        $courseGroup->set_parent_id($parentCourseGroup->getId());
        
        if (! $courseGroup->create())
        {
            throw new \Exception('Could not create course group with id "' . $oldId . '"');
        }
        
        if ($courseGroup->get_document_category_id())
        {
            $this->courseGroupService->createDocumentCategoryForCourseGroup($courseGroup);
        }
        
        if ($courseGroup->get_forum_category_id())
        {
            $this->courseGroupService->createForumCategoryAndPublicationForCourseGroup($courseGroup, $user);
        }
        
        while ($courseGroupChild = $courseGroupChildren->next_result())
        {
            try
            {
                $this->copyCourseGroupsToNewParent($courseGroupChild, $targetCourseId, $courseGroup, $user);
            }
            catch (\Exception $ex)
            {
            }
        }
    }
}