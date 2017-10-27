<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * Manages the decorators for the CourseGroups
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupDecoratorsManager implements CourseGroupFormDecoratorInterface, CourseGroupServiceDecoratorInterface
{
    /**
     * @var CourseGroupFormDecoratorInterface[]
     */
    protected $formDecorators;

    /**
     * @var CourseGroupServiceDecoratorInterface[]
     */
    protected $serviceDecorators;

    /**
     * CourseGroupDecoratorsManager constructor.
     */
    public function __construct()
    {
        $this->formDecorators = [];
        $this->serviceDecorators = [];
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface[]
     */
    public function getFormDecorators()
    {
        return $this->formDecorators;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface[]
     */
    public function getServiceDecorators()
    {
        return $this->serviceDecorators;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupFormDecoratorInterface $courseGroupFormDecorator
     */
    public function addFormDecorator(CourseGroupFormDecoratorInterface $courseGroupFormDecorator)
    {
        $this->formDecorators[] = $courseGroupFormDecorator;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface $courseGroupServiceDecorator
     */
    public function addServiceDecorator(CourseGroupServiceDecoratorInterface $courseGroupServiceDecorator)
    {
        $this->serviceDecorators[] = $courseGroupServiceDecorator;
    }

    /**
     * Decorates the course group form
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $courseGroupForm
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     */
    public function decorateCourseGroupForm(FormValidator $courseGroupForm, CourseGroup $courseGroup)
    {
        foreach ($this->formDecorators as $formDecorator)
        {
            $formDecorator->decorateCourseGroupForm($courseGroupForm, $courseGroup);
        }
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
        foreach ($this->serviceDecorators as $serviceDecorator)
        {
            $serviceDecorator->createGroup($courseGroup, $user, $formValues);
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
        foreach ($this->serviceDecorators as $serviceDecorator)
        {
            $serviceDecorator->updateGroup($courseGroup, $user, $formValues);
        }
    }

    /**
     * Decorates the delete functionality of a course group. Handing over the created course group and the form
     * values for further processing of the custom form
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function deleteGroup(CourseGroup $courseGroup, User $user)
    {
        foreach ($this->serviceDecorators as $serviceDecorator)
        {
            $serviceDecorator->deleteGroup($courseGroup, $user);
        }
    }

    /**
     * Decorates the subscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function subscribeUser(CourseGroup $courseGroup, User $user)
    {
        foreach ($this->serviceDecorators as $serviceDecorator)
        {
            $serviceDecorator->subscribeUser($courseGroup, $user);
        }
    }

    /**
     * Decorates the unsubscribe user functionality
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unsubscribeUser(CourseGroup $courseGroup, User $user)
    {
        foreach ($this->serviceDecorators as $serviceDecorator)
        {
            $serviceDecorator->unsubscribeUser($courseGroup, $user);
        }
    }
}