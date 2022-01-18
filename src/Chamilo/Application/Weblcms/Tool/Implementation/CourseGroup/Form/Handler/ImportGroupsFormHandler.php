<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain\ImportGroupStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain\QuickUserSubscriberStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type\ImportGroupsFormType;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type\QuickUsersSubscribeFormType;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\Importer\Importer;
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
class ImportGroupsFormHandler extends FormHandler
{
    protected ?Course $course;
    protected ?CourseGroup $parentCourseGroup;
    protected array $importGroupStatuses;

    protected Importer $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @param CourseGroup $parentCourseGroup
     *
     * @return ImportGroupsFormHandler
     */
    public function setParentCourseGroup(CourseGroup $parentCourseGroup): ImportGroupsFormHandler
    {
        $this->parentCourseGroup = $parentCourseGroup;

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return self
     */
    public function setCourse(Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return ImportGroupStatus[]
     */
    public function getImportGroupStatuses(): ?array
    {
        return $this->importGroupStatuses;
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

        if (!$this->course instanceof Course)
        {
            throw new \RuntimeException('The form handler can not be executed without a valid course object');
        }

        $data = $form->getData();

        $uploadedFile = $data[ImportGroupsFormType::ELEMENT_CSV_FILE];

        $this->importGroupStatuses =
            $this->importer->importGroups($uploadedFile, $this->course, $this->parentCourseGroup);

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
    }
}
