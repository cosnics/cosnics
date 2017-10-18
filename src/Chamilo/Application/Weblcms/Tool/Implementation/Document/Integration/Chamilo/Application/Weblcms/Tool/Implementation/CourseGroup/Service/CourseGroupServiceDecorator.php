<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Decorates the service for course groups. Adding additional functionality for the common course group functionality
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecorator implements CourseGroupServiceDecoratorInterface
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
     * @param array $formValues
     */
    public function createGroup(CourseGroup $courseGroup, $formValues = [])
    {
        if ($formValues[CourseGroupFormDecorator::PROPERTY_DOCUMENT_CATEGORY_ID][0] == 1)
        {
            $this->courseGroupPublicationCategoryService->createPublicationCategoryForCourseGroup(
                $courseGroup, 'Document'
            );
        }
    }

    /**
     * Decorates the update functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param array $formValues
     */
    public function updateGroup(CourseGroup $courseGroup, $formValues = [])
    {
        // TODO: Implement updateGroup() method.
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function deleteGroup(CourseGroup $courseGroup)
    {
        // TODO: Implement deleteGroup() method.
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        // TODO: Implement subscribeUser() method.
    }

    /**
     * Decorates the unsubscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        // TODO: Implement unsubscribeUser() method.
    }
}