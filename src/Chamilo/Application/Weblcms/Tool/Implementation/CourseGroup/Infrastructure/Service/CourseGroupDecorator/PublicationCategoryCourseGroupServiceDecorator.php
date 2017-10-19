<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PublicationCategoryCourseGroupServiceDecorator implements CourseGroupServiceDecoratorInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService
     */
    protected $courseGroupPublicationCategoryService;

    /**
     * CourseGroupServiceDecorator constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupPublicationCategoryService $courseGroupPublicationCategoryService
    )
    {
        $this->courseGroupPublicationCategoryService = $courseGroupPublicationCategoryService;
    }

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
            $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup(
                $courseGroup, $this->getToolName()
            );
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
            $this->courseGroupPublicationCategoryService->createOrUpdatePublicationCategoryForCourseGroup(
                $courseGroup, $this->getToolName()
            );
        }
        else
        {
            $this->courseGroupPublicationCategoryService->disconnectPublicationCategoryFromCourseGroup(
                $courseGroup, $this->getToolName()
            );
        }
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function deleteGroup(CourseGroup $courseGroup)
    {
        $this->courseGroupPublicationCategoryService->disconnectPublicationCategoryFromCourseGroup(
            $courseGroup, $this->getToolName()
        );
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {

    }

    /**
     * Decorates the unsubscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {

    }

    /**
     * Returns the name of the tool to be used in the category
     *
     * @return string
     */
    abstract function getToolName();

    /**
     * Returns the name of the property for the
     * @return mixed
     */
    abstract function getFormProperty();
}