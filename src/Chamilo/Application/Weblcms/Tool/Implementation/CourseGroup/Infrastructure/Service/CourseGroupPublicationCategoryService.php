<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupPublicationCategoryRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroupPublicationCategory;

/**
 * Manages the CourseGroupPublicationCategories
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupPublicationCategoryService
{
    /**
     * @var CourseGroupPublicationCategoryRepository
     */
    protected $courseGroupPublicationCategoryRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository
     */
    protected $publicationRepository;

    /**
     * The Weblcms Rights Service
     *
     * @var WeblcmsRights
     */
    protected $weblcmsRights;

    /**
     * CourseGroupPublicationCategoryService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupPublicationCategoryRepository $courseGroupPublicationCategoryRepository
     * @param \Chamilo\Application\Weblcms\Storage\Repository\PublicationRepository $publicationRepository
     * @param \Chamilo\Application\Weblcms\Rights\WeblcmsRights $weblcmsRights
     */
    public function __construct(
        CourseGroupPublicationCategoryRepository $courseGroupPublicationCategoryRepository,
        PublicationRepository $publicationRepository,
        WeblcmsRights $weblcmsRights
    )
    {
        $this->courseGroupPublicationCategoryRepository = $courseGroupPublicationCategoryRepository;
        $this->publicationRepository = $publicationRepository;
        $this->weblcmsRights = $weblcmsRights;
    }

    /**
     * Helper function to create a new publication category
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory
     *
     * @throws \Exception
     */
    public function createPublicationCategoryForCourseGroup(CourseGroup $courseGroup, $toolName)
    {
        $publicationCategory = new ContentObjectPublicationCategory();

        $publicationCategory->set_parent(0);
        $publicationCategory->set_tool($toolName);
        $publicationCategory->set_course($courseGroup->get_course_code());
        $publicationCategory->set_name($courseGroup->get_name());
        $publicationCategory->set_allow_change(0);
        $publicationCategory->set_display_order(1);

        if (!$this->courseGroupPublicationCategoryRepository->create($publicationCategory))
        {
            throw new \Exception(
                'Could not create a new category in tool ' . $toolName . ' for group ' . $courseGroup->get_name()
            );
        }

        $this->setRightsOnCategoryForCourseGroup($publicationCategory, $courseGroup);
        $this->createCourseGroupPublicationCategory($publicationCategory, $courseGroup);

        return $publicationCategory;
    }

    /**
     * Removes the publication categories from a given course group
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     */
    public function disconnectPublicationCategoryFromCourseGroup(CourseGroup $courseGroup, $toolName = null)
    {
        $publicationCategories =
            $this->courseGroupPublicationCategoryRepository->findPublicationCategoriesForCourseGroup(
                $courseGroup, $toolName
            );

        foreach ($publicationCategories as $publicationCategory)
        {
            $publicationCategory->set_allow_change(1);
            $this->courseGroupPublicationCategoryRepository->update($publicationCategory);
        }

        $courseGroupPublicationCategories =
            $this->courseGroupPublicationCategoryRepository->findCourseGroupPublicationCategoriesForCourseGroup(
                $courseGroup, $toolName
            );

        foreach ($courseGroupPublicationCategories as $courseGroupPublicationCategory)
        {
            $this->courseGroupPublicationCategoryRepository->delete($courseGroupPublicationCategory);
        }
    }

    /**
     * Returns the publication categories for a given course group, optionally limiting them by a tool
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return bool
     */
    public function courseGroupHasPublicationCategories(CourseGroup $courseGroup, $toolName = null)
    {
        return count($this->getPublicationCategoriesForCourseGroup($courseGroup, $toolName)) > 0;
    }

    /**
     * Returns the publication categories for a given course group, optionally limiting them by a tool
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory[]
     */
    public function getPublicationCategoriesForCourseGroup(CourseGroup $courseGroup, $toolName = null)
    {
        return $this->courseGroupPublicationCategoryRepository->findPublicationCategoriesForCourseGroup(
            $courseGroup, $toolName
        );
    }

    /**
     * Sets the rights for a given course group on a given category
     *
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param CourseGroup $courseGroup
     * @param array $rights
     *
     * @throws \Exception
     */
    protected function setRightsOnCategoryForCourseGroup(
        ContentObjectPublicationCategory $publicationCategory,
        CourseGroup $courseGroup,
        $rights = array(WeblcmsRights::VIEW_RIGHT, WeblcmsRights::ADD_RIGHT, WeblcmsRights::MANAGE_CATEGORIES_RIGHT)
    )
    {
        /** @var RightsLocation $location */
        $location = $this->weblcmsRights->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_COURSE_CATEGORY,
            $publicationCategory->getId(),
            $courseGroup->get_course_code()
        );

        if (!$location)
        {
            throw new \Exception(
                'No location found for the publication category with id ' . $publicationCategory->getId()
            );
        }

        $location->disinherit();

        if (!$location->update())
        {
            throw new \Exception(
                'Could not update the location for the publication category with id ' . $publicationCategory->getId()
            );
        }

        foreach ($rights as $right)
        {
            if (!$this->weblcmsRights->set_location_entity_right(
                \Chamilo\Application\Weblcms\Manager::context(),
                $right,
                $courseGroup->getId(),
                CourseGroupEntity::ENTITY_TYPE,
                $location->getId()
            ))
            {
                throw new \Exception(
                    'Could not set right ' . $right . ' on publication category with id ' .
                    $publicationCategory->getId()
                );
            }
        }
    }

    /**
     * Creates a CourseGroupPublicationCategory for a given PublicationCategory and CourseGroup
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @throws \Exception
     */
    protected function createCourseGroupPublicationCategory(
        ContentObjectPublicationCategory $publicationCategory, CourseGroup $courseGroup
    )
    {
        $courseGroupPublicationCategory = new CourseGroupPublicationCategory();
        $courseGroupPublicationCategory->setCourseGroupId($courseGroup->getId());
        $courseGroupPublicationCategory->setPublicationCategoryId($publicationCategory->getId());

        if (!$this->courseGroupPublicationCategoryRepository->create($courseGroupPublicationCategory))
        {
            throw new \Exception(
                sprintf(
                    'Could not create the course group publication category for group %s and publication category %s',
                    $courseGroup->getId(), $publicationCategory->getId()
                )
            );
        }
    }
}