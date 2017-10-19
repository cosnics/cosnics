<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\PublicationCategoryCourseGroupServiceDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Decorates the service for course groups. Adding additional functionality for the common course group functionality
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Forum\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecorator extends PublicationCategoryCourseGroupServiceDecorator implements CourseGroupServiceDecoratorInterface
{
    /**
     * Returns the name of the tool to be used in the category
     *
     * @return string
     */
    function getToolName()
    {
//        $publicationCategory = $this->createPublicationCategoryForCourseGroup($courseGroup, 'Forum');
//        $this->setRightsOnCategoryForCourseGroup($publicationCategory, $courseGroup);
//
//        $forum = $this->createForumForCourseGroup($courseGroup, $user);
//        $this->publishForumInCategory($courseGroup, $forum, $publicationCategory);
//
//        $courseGroup->set_forum_category_id($publicationCategory->getId());
//        if (! $courseGroup->update())
//        {
//            throw new \Exception(
//                'Could not set the newly created category id to the course group with id ' . $courseGroup->getId());
//        }

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