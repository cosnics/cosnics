<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\PlatformGroupUsersSubscriber;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type\PlatformGroupTeamType;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Handler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubscribePlatformGroupUsersFormHandler extends FormHandler
{
    /**
     * @var PlatformGroupUsersSubscriber
     */
    protected $platformGroupUsersSubscriber;

    /**
     * @var CourseGroup
     */
    protected $courseGroup;

    /**
     * PlatformGroupUserSubscriberFormHandler constructor.
     *
     * @param PlatformGroupUsersSubscriber $platformGroupUsersSubscriber
     */
    public function __construct(PlatformGroupUsersSubscriber $platformGroupUsersSubscriber)
    {
        $this->platformGroupUsersSubscriber = $platformGroupUsersSubscriber;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return SubscribePlatformGroupUsersFormHandler
     */
    public function setCourseGroup(CourseGroup $courseGroup): SubscribePlatformGroupUsersFormHandler
    {
        $this->courseGroup = $courseGroup;

        return $this;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request): bool
    {
        if (!parent::handle($form, $request))
        {
            return false;
        }

        if (!$this->courseGroup instanceof CourseGroup)
        {
            throw new \RuntimeException('The form handler can not be executed without a valid course group object');
        }

        $data = $form->getData();

        $platformGroupIds = json_decode($data[PlatformGroupTeamType::ELEMENT_PLATFORM_GROUPS]);

        $this->platformGroupUsersSubscriber->subscribeUsersFromPlatformGroupsInCourseGroup(
            $this->courseGroup, $platformGroupIds
        );

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
    }
}
