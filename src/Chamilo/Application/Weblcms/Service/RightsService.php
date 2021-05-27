<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\CourseSettingsServiceInterface;
use Chamilo\Application\Weblcms\Service\Interfaces\PublicationServiceInterface;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Weblcms\Service\Interfaces\RightsServiceInterface;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;

/**
 * Service to manage the weblcms rights
 *
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsService implements RightsServiceInterface
{

    /**
     * The original weblcms rights class
     *
     * @var WeblcmsRights
     */
    private $weblcmsRights;

    /**
     * The course service
     *
     * @var CourseService
     */
    private $courseService;

    /**
     * The course settings service
     *
     * @var CourseSettingsService
     */
    private $courseSettingsService;

    /**
     * The publication service
     *
     * @var PublicationService
     */
    private $publicationService;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository
     */
    protected $rightsLocationRepository;

    /**
     * Caches the rights
     *
     * @var bool[]
     */
    private $rightsCache;

    /**
     * Boolean to determine whether or not we use the rights system in the "view as user" due to alternative rights
     *
     * @var bool
     */
    private $viewAsUserMode;

    /**
     * Constructor
     *
     * @param WeblcmsRights $weblcmsRights
     * @param CourseSettingsServiceInterface $courseSettingsService
     * @param \Chamilo\Application\Weblcms\Storage\Repository\RightsLocationRepository $rightsLocationRepository
     */
    public function __construct(
        WeblcmsRights $weblcmsRights, CourseSettingsServiceInterface $courseSettingsService,
        RightsLocationRepository $rightsLocationRepository
    )
    {
        $this->weblcmsRights = $weblcmsRights;
        $this->courseSettingsService = $courseSettingsService;
        $this->viewAsUserMode = false;
        $this->rightsLocationRepository = $rightsLocationRepository;
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
     * Setter injector for this dependency due to a cyclic dependency issue
     *
     * @param PublicationServiceInterface $publicationService
     *
     * @return self
     */
    public function setPublicationService(PublicationServiceInterface $publicationService)
    {
        $this->publicationService = $publicationService;

        return $this;
    }

    /**
     * Sets the viewAsUserMode variable
     *
     * @param bool $viewAsUserMode
     *
     * @return $this
     */
    public function setViewAsUserMode($viewAsUserMode)
    {
        $this->viewAsUserMode = $viewAsUserMode;

        return $this;
    }

    /**
     * Returns the publication identifiers where a given user has the view right for in a given category for a given
     * course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $category
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return int[]
     */
    public function getPublicationIdsWithViewRightInCategory(
        User $user, ContentObjectPublicationCategory $category,
        Course $course
    )
    {
        $categoryLocation = $this->weblcmsRights->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_COURSE_CATEGORY,
            $category->getId(),
            $course->getId()
        );

        return $this->weblcmsRights->get_publication_identifiers_with_right_granted(
            WeblcmsRights::VIEW_RIGHT,
            $categoryLocation,
            $course,
            $user
        );
    }

    /**
     * Returns the publication identifiers where a given user has the view right for in a given category for a given
     * course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return int[]
     */
    public function getPublicationIdsWithViewRightInTool(User $user, $tool, Course $course)
    {
        if ($tool == 'home')
        {
            $toolLocation = $this->weblcmsRights->get_courses_subtree_root($course->getId());
        }
        else
        {
            $toolRegistration = $this->courseService->getToolRegistration($tool);

            $toolLocation = $this->weblcmsRights->get_weblcms_location_by_identifier_from_courses_subtree(
                WeblcmsRights::TYPE_COURSE_MODULE,
                $toolRegistration->getId(),
                $course->getId()
            );
        }

        return $this->weblcmsRights->get_publication_identifiers_with_right_granted(
            WeblcmsRights::VIEW_RIGHT,
            $toolLocation,
            $course,
            $user
        );
    }

    /**
     * Checks if a user can view a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewPublication(User $user, ContentObjectPublication $publication, Course $course)
    {
        return $this->cacheFunctionCall(
            'calculateCanUserViewPublication',
            array($user, $publication, $course),
            array($user->getId(), $publication->getId(), $course->getId())
        );
    }

    /**
     * Checks if a user can edit a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserEditPublication(User $user, ContentObjectPublication $publication, Course $course)
    {
        return $this->courseService->isUserTeacherInCourse($user, $course) && $this->isCollaborationAllowed(
                $publication
            );
    }

    /**
     * Checks if the publication allows collaboration
     *
     * @param ContentObjectPublication $publication
     *
     * @return boolean
     */
    public function isCollaborationAllowed($publication)
    {
        return $publication->get_allow_collaboration();
    }

    /**
     * Checks if a user can delete a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserDeletePublication(User $user, ContentObjectPublication $publication, Course $course)
    {
    }

    /**
     * Checks if a user can view a publication category in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewPublicationCategory(
        User $user, ContentObjectPublicationCategory $publicationCategory,
        Course $course
    )
    {
        return $this->cacheFunctionCall(
            'calculateCanUserViewPublicationCategory',
            array($user, $publicationCategory, $course),
            array($user->getId(), $publicationCategory->getId(), $course->getId())
        );
    }

    /**
     * Checks if a user can edit a publication category in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserEditPublicationCategory(
        User $user, ContentObjectPublicationCategory $publicationCategory,
        Course $course
    )
    {
    }

    /**
     * Checks if a user can delete a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserDeletePublicationCategory(
        User $user, ContentObjectPublicationCategory $publicationCategory,
        Course $course
    )
    {
    }

    /**
     * Checks if a user can publish a publication in a tool of a course (and optionally in a category)
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param ContentObjectPublicationCategory $publicationCategory
     *
     * @return bool
     */
    public function canUserCreatePublication(
        User $user, $tool, Course $course,
        ContentObjectPublicationCategory $publicationCategory = null
    )
    {
    }

    /**
     * Checks if a user can view a tool in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewTool(User $user, $tool, Course $course)
    {
        return $this->cacheFunctionCall(
            'calculatecanUserViewTool',
            array($user, $tool, $course),
            array($user->getId(), $tool, $course->getId())
        );
    }

    /**
     * Checks if a user can view a course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewCourse(User $user, Course $course)
    {
        return $this->cacheFunctionCall(
            'calculateCanUserViewCourse',
            array($user, $course),
            array($user->getId(), $course->getId())
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $userIds
     *
     * @throws \Exception
     */
    public function removeUsersFromRightsByIds(Course $course, array $userIds = [])
    {
        $this->rightsLocationRepository->removeEntitiesFromRightsByIds(
            $course, UserEntityProvider::ENTITY_TYPE, $userIds
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param array $groupIds
     *
     * @throws \Exception
     */
    public function removeGroupsFromRightsByIds(Course $course, array $groupIds = [])
    {
        $this->rightsLocationRepository->removeEntitiesFromRightsByIds(
            $course, GroupEntityProvider::ENTITY_TYPE, $groupIds
        );
    }

    /**
     * **************************************************************************************************************
     * Rights Calculation Functionality *
     * **************************************************************************************************************
     */

    /**
     * Checks if a user can view a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    protected function calculateCanUserViewPublication(User $user, ContentObjectPublication $publication, Course $course
    )
    {
        if ($this->courseService->isUserTeacherInCourse($user, $course) || $user->is_platform_admin())
        {
            return true;
        }

        $categoryId = $publication->get_category_id();
        $category = $this->publicationService->getPublicationCategoryById($categoryId);

        if (!empty($category))
        {
            if (!$this->canUserViewPublicationCategoryRegardlessOfRightSystem($user, $category, $course))
            {
                return false;
            }
        }
        else
        {
            if (!$this->canUserViewToolRegardlessOfRightSystem($user, $publication->get_tool(), $course))
            {
                return false;
            }
        }

        if ($this->weblcmsRights->is_allowed_in_courses_subtree(
            WeblcmsRights::EDIT_RIGHT,
            $publication->getId(),
            WeblcmsRights::TYPE_PUBLICATION,
            $course->getId(),
            $user->getId()
        ))
        {
            return true;
        }

        if (!$publication->is_visible_for_target_users())
        {
            return false;
        }

        return $this->weblcmsRights->is_allowed_in_courses_subtree(
            WeblcmsRights::VIEW_RIGHT,
            $publication->getId(),
            WeblcmsRights::TYPE_PUBLICATION,
            $course->getId(),
            $user->getId()
        );
    }

    /**
     * Determines if a user can view a publication category in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    protected function calculateCanUserViewPublicationCategory(
        User $user,
        ContentObjectPublicationCategory $publicationCategory, Course $course
    )
    {
        if ($this->courseService->isUserTeacherInCourse($user, $course) || $user->is_platform_admin())
        {
            return true;
        }

        if (!$this->canUserViewPublicationCategoryRegardlessOfRightSystem($user, $publicationCategory, $course))
        {
            return false;
        }

        return $this->weblcmsRights->is_allowed_in_courses_subtree(
            WeblcmsRights::VIEW_RIGHT,
            $publicationCategory->getId(),
            WeblcmsRights::TYPE_COURSE_CATEGORY,
            $course->getId(),
            $user->getId()
        );
    }

    /**
     * Checks if a user can view a publication category regardless of the right system checks
     *
     * @param User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param Course $course
     *
     * @return bool
     */
    protected function canUserViewPublicationCategoryRegardlessOfRightSystem(
        User $user,
        ContentObjectPublicationCategory $publicationCategory, Course $course
    )
    {
        return $this->cacheFunctionCall(
            'calculateCanUserViewPublicationCategoryRegardlessOfRightSystem',
            array($user, $publicationCategory, $course),
            array($user->getId(), $publicationCategory->getId(), $course->getId())
        );
    }

    /**
     * Determines if a user can view a publication category regardless of the right system checks
     *
     * @param User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param Course $course
     *
     * @return bool
     */
    protected function calculateCanUserViewPublicationCategoryRegardlessOfRightSystem(
        User $user,
        ContentObjectPublicationCategory $publicationCategory, Course $course
    )
    {
        if (!$this->canUserViewToolRegardlessOfRightSystem($user, $publicationCategory->get_tool(), $course))
        {
            return false;
        }

        if (!$publicationCategory->is_recursive_visible())
        {
            return false;
        }

        return true;
    }

    /**
     * Determines if a user can view a tool in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course * @return bool
     */
    protected function calculateCanUserViewTool(User $user, $tool, Course $course)
    {
        if ($user->is_platform_admin())
        {
            return true;
        }

        $toolRegistration = $this->courseService->getToolRegistration($tool);

        if (!$toolRegistration)
        {
            return false;
        }

        if ($this->courseService->isUserTeacherInCourse($user, $course) &&
            $this->courseSettingsService->isToolActive($course, $toolRegistration->getId()))
        {
            return true;
        }

        if (!$this->canUserViewToolRegardlessOfRightSystem($user, $tool, $course))
        {
            return false;
        }

        if ($tool == 'home')
        {
            return $this->weblcmsRights->is_allowed_in_courses_subtree(
                WeblcmsRights::VIEW_RIGHT,
                0,
                WeblcmsRights::TYPE_ROOT,
                $course->getId(),
                $user->getId()
            );
        }

        return $this->weblcmsRights->is_allowed_in_courses_subtree(
            WeblcmsRights::VIEW_RIGHT,
            $toolRegistration->getId(),
            WeblcmsRights::TYPE_COURSE_MODULE,
            $course->getId(),
            $user->getId()
        );
    }

    /**
     * Checks if a user can view a tool regardless of the right system checks
     *
     * @param User $user
     * @param string $tool
     * @param Course $course
     *
     * @return bool
     */
    protected function canUserViewToolRegardlessOfRightSystem(User $user, $tool, Course $course)
    {
        return $this->cacheFunctionCall(
            'calculateCanUserViewToolRegardlessOfRightSystem',
            array($user, $tool, $course),
            array($user->getId(), $tool, $course->getId())
        );
    }

    /**
     * Determines if a user can view a tool regardless of the right system checks
     *
     * @param User $user
     * @param string $tool
     * @param Course $course
     *
     * @return bool
     */
    protected function calculateCanUserViewToolRegardlessOfRightSystem(User $user, $tool, Course $course)
    {
        if (!$this->canUserViewCourse($user, $course))
        {
            return false;
        }

        $adminTools = array(
            'course_copier',
            'course_deleter',
            'course_truncater',
            'course_settings',
            'course_sections',
            'reporting',
            'rights'
        );

        if (!$this->courseService->isUserTeacherInCourse($user, $course) && in_array($tool, $adminTools))
        {
            return false;
        }

        $toolRegistration = $this->courseService->getToolRegistration($tool);

        if (!$toolRegistration)
        {
            return false;
        }

        if (!$this->courseSettingsService->isToolActive($course, $toolRegistration->getId()))
        {
            return false;
        }

        if (!$this->courseSettingsService->isToolVisible($course, $toolRegistration->getId()))
        {
            return false;
        }

        return true;
    }

    /**
     * Determines if a user can view a course
     *
     * @param User $user
     * @param Course $course
     *
     * @return bool
     */
    protected function calculateCanUserViewCourse(User $user, Course $course)
    {
        if ($this->viewAsUserMode)
        {
            return true;
        }

        if ($this->courseService->isUserTeacherInCourse($user, $course) || $user->is_platform_admin())
        {
            return true;
        }

        if (!$this->courseSettingsService->isCourseOpen($course))
        {
            return false;
        }

        if ($this->courseSettingsService->isCourseOpenForWorld($course))
        {
            return true;
        }

        if ($this->courseSettingsService->isCourseOpenForPlatform($course) && !$user->is_anonymous_user())
        {
            return true;
        }

        if ($this->courseService->isUserSubscribedToCourse($user, $course))
        {
            return true;
        }

        return false;
    }

    /**
     * **************************************************************************************************************
     * Cache Functionality *
     * **************************************************************************************************************
     */

    /**
     * Uses the cache for the results of a function call
     *
     * @param string $function
     * @param array $parameters
     * @param array $cacheParameters
     *
     * @return bool
     */
    protected function cacheFunctionCall($function, $parameters, $cacheParameters)
    {
        $value = $this->getFromCache($function, $cacheParameters);

        if (is_null($value))
        {
            $value = call_user_func_array(array($this, $function), $parameters);
            $this->saveToCache($function, $cacheParameters, $value);
        }

        return $value;
    }

    /**
     * Returns a value from the cache
     *
     * @param string $function
     * @param array $cacheParameters
     *
     * @return bool
     */
    protected function getFromCache($function, $cacheParameters = [])
    {
        $cacheKey = $this->getCacheKey($function, $cacheParameters);
        if (array_key_exists($cacheKey, $this->rightsCache))
        {
            return $this->rightsCache[$cacheKey];
        }
    }

    /**
     * Saves a value to the cache
     *
     * @param string $function
     * @param array $cacheParameters
     * @param string $value
     */
    protected function saveToCache($function, $cacheParameters = [], $value)
    {
        $cacheKey = $this->getCacheKey($function, $cacheParameters);
        $this->rightsCache[$cacheKey] = $value;
    }

    /**
     * Calculates the cache key for the given parameters
     *
     * @param $function
     * @param array $cacheParameters
     *
     * @return string
     */
    protected function getCacheKey($function, $cacheParameters = [])
    {
        return sha1($function . ':' . serialize($cacheParameters));
    }
}