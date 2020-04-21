<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\PublicationCategoryCourseGroupServiceDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\User\Storage\DataClass\User;
use Exception;

/**
 * Decorates the service for course groups. Adding additional functionality for the common course group functionality
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecorator extends PublicationCategoryCourseGroupServiceDecorator
    implements CourseGroupServiceDecoratorInterface
{
    /**
     * Decorates the create functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     */
    public function createGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        $hasCategory = boolval($formValues[$this->getFormProperty()][0]);

        if ($hasCategory)
        {
            $this->createCategoryAndForum($courseGroup, $user);
        }
    }

    /**
     * Decorates the update functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param array $formValues
     */
    public function updateGroup(CourseGroup $courseGroup, User $user, $formValues = [])
    {
        $hasCategory = boolval($formValues[$this->getFormProperty()][$courseGroup->getId()]);

        if ($hasCategory)
        {
            if (!$this->courseGroupPublicationCategoryService->courseGroupHasPublicationCategories(
                $courseGroup, $this->getToolName()
            ))
            {
                $this->createCategoryAndForum($courseGroup, $user);
            }
            else
            {
                $this->courseGroupPublicationCategoryService->updatePublicationCategoryForCourseGroup(
                    $courseGroup, $this->getToolName()
                );
            }
        }
        else
        {
            $this->courseGroupPublicationCategoryService->disconnectPublicationCategoryFromCourseGroup(
                $courseGroup, $this->getToolName()
            );
        }
    }

    /**
     * Creates a new ContentObjectPublicationCategory and Forum and publishes the forum in the category
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    protected function createCategoryAndForum(CourseGroup $courseGroup, User $user)
    {
        $category = $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup(
            $courseGroup, $this->getToolName()
        );

        $forum = $this->createForumForCourseGroup($courseGroup, $user);

        $this->publishForumInCategory($courseGroup, $forum, $category);
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
            throw new Exception('Could not create a new forum for course group with id ' . $courseGroup->getId());
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
            throw new Exception('Could not publish the forum for course group with id ' . $courseGroup->getId());
        }

        return $content_object_publication;
    }

    /**
     * Returns the name of the tool to be used in the category
     *
     * @return string
     */
    function getToolName()
    {
        return 'Forum';
    }

    /**
     * Returns the name of the property for the
     * @return mixed
     */
    function getFormProperty()
    {
        return CourseGroupFormDecorator::PROPERTY_FORUM_CATEGORY_ID;
    }
}