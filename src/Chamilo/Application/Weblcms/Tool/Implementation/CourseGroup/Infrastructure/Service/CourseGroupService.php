<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\RightsLocation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepositoryInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Course group service to help with the management of course groups
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupService implements CourseGroupServiceInterface
{
    /**
     * The Weblcms Rights Service
     *
     * @var WeblcmsRights
     */
    protected $weblcmsRights;

    /**
     * @var CourseGroupRepositoryInterface
     */
    protected $courseGroupRepository;

    /**
     * CourseGroupService constructor.
     *
     * @param WeblcmsRights $weblcmsRights
     * @param CourseGroupRepositoryInterface $courseGroupRepository
     */
    public function __construct(WeblcmsRights $weblcmsRights, CourseGroupRepositoryInterface $courseGroupRepository)
    {
        $this->courseGroupRepository = $courseGroupRepository;
        $this->weblcmsRights = $weblcmsRights;
    }

    /**
     * Creates a document category for a given course group
     *
     * @param CourseGroup $courseGroup
     *
     * @throws \Exception
     */
    public function createDocumentCategoryForCourseGroup(CourseGroup $courseGroup)
    {
        $publicationCategory = $this->createPublicationCategoryForCourseGroup($courseGroup, 'Document');
        $this->setRightsOnCategoryForCourseGroup($publicationCategory, $courseGroup);

        $courseGroup->set_document_category_id($publicationCategory->getId());
        if(!$courseGroup->update())
        {
            throw new \Exception(
                'Could not set the newly created category id to the course group with id ' . $courseGroup->getId()
            );
        }
    }

    /**
     * Creates a forum for a given course group
     *
     * @param CourseGroup $courseGroup
     * @param User $user
     *
     * @throws \Exception
     */
    public function createForumCategoryAndPublicationForCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $publicationCategory = $this->createPublicationCategoryForCourseGroup($courseGroup, 'Forum');
        $this->setRightsOnCategoryForCourseGroup($publicationCategory, $courseGroup);

        $forum = $this->createForumForCourseGroup($courseGroup, $user);
        $this->publishForumInCategory($courseGroup, $forum, $publicationCategory);

        $courseGroup->set_forum_category_id($publicationCategory->getId());
        if(!$courseGroup->update())
        {
            throw new \Exception(
                'Could not set the newly created category id to the course group with id ' . $courseGroup->getId()
            );
        }
    }

    /**
     * Counts the course groups in a given course
     *
     * @param int $courseId
     *
     * @return int
     */
    public function countCourseGroupsInCourse($courseId)
    {
        return $this->courseGroupRepository->countCourseGroupsInCourse($courseId);
    }

    /**
     * Helper function to create a new publication category
     *
     * @param CourseGroup $courseGroup
     * @param string $toolName
     *
     * @return ContentObjectPublicationCategory
     *
     * @throws \Exception
     */
    protected function createPublicationCategoryForCourseGroup(CourseGroup $courseGroup, $toolName)
    {
        $publicationCategory = new ContentObjectPublicationCategory();

        $publicationCategory->set_parent(0);
        $publicationCategory->set_tool($toolName);
        $publicationCategory->set_course($courseGroup->get_course_code());
        $publicationCategory->set_name($courseGroup->get_name());
        $publicationCategory->set_allow_change(0);
        $publicationCategory->set_display_order(1);

        if (!$publicationCategory->create())
        {
            throw new \Exception(
                'Could not create a new category in tool ' . $toolName . ' for group ' . $courseGroup->get_name()
            );
        }

        return $publicationCategory;
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
        ContentObjectPublicationCategory $publicationCategory, CourseGroup $courseGroup,
        $rights = array(WeblcmsRights::VIEW_RIGHT, WeblcmsRights::ADD_RIGHT, WeblcmsRights::MANAGE_CATEGORIES_RIGHT)
    )
    {
        /** @var RightsLocation $location */
        $location = $this->weblcmsRights->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_COURSE_CATEGORY, $publicationCategory->getId(), $courseGroup->get_course_code()
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
            if (
            !$this->weblcmsRights->set_location_entity_right(
                \Chamilo\Application\Weblcms\Manager::context(), $right,
                $courseGroup->getId(), CourseGroupEntity::ENTITY_TYPE, $location->getId()
            )
            )
            {
                throw new \Exception(
                    'Could not set right ' . $right . ' on publication category with id ' .
                    $publicationCategory->getId()
                );
            }
        }
    }

    /**
     * Creates a forum content object for a given course group
     *
     * @param CourseGroup $courseGroup
     * @param User $user
     *
     * @return Forum
     *
     * @throws \Exception
     */
    protected function createForumForCourseGroup(CourseGroup $courseGroup, User $user)
    {
        $forum = new Forum();

        $forum->set_title($courseGroup->get_name());
        $forum->set_locked(0);
        $forum->set_description($courseGroup->get_name() . ' Forum');
        $forum->set_owner_id($user->getId());

        if (!$forum->create())
        {
            throw new \Exception(
                'Could not create a new forum for course group with id ' . $courseGroup->getId()
            );
        }

        return $forum;
    }

    /**
     * Publishes a given forum in a given category
     *
     * @param CourseGroup $courseGroup
     * @param Forum $forum
     * @param ContentObjectPublicationCategory $publicationCategory
     *
     * @return ContentObjectPublication
     *
     * @throws \Exception
     */
    protected function publishForumInCategory(
        CourseGroup $courseGroup, Forum $forum, ContentObjectPublicationCategory $publicationCategory
    )
    {
        $content_object_publication = new ContentObjectPublication();

        $content_object_publication->set_category_id($publicationCategory->getId());
        $content_object_publication->set_tool('Forum');
        $content_object_publication->set_course_id($courseGroup->get_course_code());
        $content_object_publication->set_publisher_id($forum->get_owner_id());
        $content_object_publication->set_content_object_id($forum->getId());
        $content_object_publication->set_publication_date(time());
        $content_object_publication->set_modified_date(time());

        if (!$content_object_publication->create())
        {
            throw new \Exception('Could not publish the forum for course group with id ' . $courseGroup->getId());
        }

        return $content_object_publication;
    }
}