<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain\QuickUserSubscriberStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type\QuickUsersSubscribeFormType;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\PlatformGroupUsersSubscriber;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\QuickUsersSubscriber;
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
class QuickUsersSubscribeFormHandler extends FormHandler
{
    /**
     * @var QuickUsersSubscriber
     */
    protected $quickUsersSubscriber;

    /**
     * @var CourseGroup
     */
    protected $courseGroup;

    /**
     * @var Course
     */
    protected $course;

    /**
     * @var QuickUserSubscriberStatus[]
     */
    protected $statuses;

    /**
     * QuickSubscribeUsersFormHandler constructor.
     *
     * @param QuickUsersSubscriber $quickUsersSubscriber
     */
    public function __construct(QuickUsersSubscriber $quickUsersSubscriber)
    {
        $this->quickUsersSubscriber = $quickUsersSubscriber;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return self
     */
    public function setCourseGroup(CourseGroup $courseGroup): self
    {
        $this->courseGroup = $courseGroup;

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return QuickUsersSubscribeFormHandler
     */
    public function setCourse(Course $course): QuickUsersSubscribeFormHandler
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return QuickUserSubscriberStatus[]
     */
    public function getStatuses(): ?array
    {
        return $this->statuses;
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

        $userIdentifiersCSV = $data[QuickUsersSubscribeFormType::ELEMENT_USER_IDENTIFIERS];

        $this->statuses = $this->quickUsersSubscriber->subscribeUsersFromCSVFormat(
            $this->course, $this->courseGroup, $userIdentifiersCSV
        );

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
    }
}
