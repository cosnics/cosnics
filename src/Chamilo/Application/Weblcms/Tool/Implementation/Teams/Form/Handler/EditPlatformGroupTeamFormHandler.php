<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Handler;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type\PlatformGroupTeamType;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Storage\DataClass\PlatformGroupTeam;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Handler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EditPlatformGroupTeamFormHandler extends FormHandler
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\PlatformGroupTeamService
     */
    protected $platformGroupTeamService;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected $course;

    /**
     * @var PlatformGroupTeam
     */
    protected $platformGroupTeam;

    /**
     * CreatePlatformGroupTeamFormHandler constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\PlatformGroupTeamService $platformGroupTeamService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\PlatformGroupTeamService $platformGroupTeamService
    )
    {
        $this->platformGroupTeamService = $platformGroupTeamService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(\Chamilo\Core\User\Storage\DataClass\User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function setCourse(\Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course): void
    {
        $this->course = $course;
    }

    public function setPlatformGroupTeam(PlatformGroupTeam $platformGroupTeam)
    {
        $this->platformGroupTeam = $platformGroupTeam;
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

        if (!$this->user instanceof User)
        {
            throw new \RuntimeException('The form handler can not be executed without a valid user object');
        }

        if (!$this->course instanceof Course)
        {
            throw new \RuntimeException('The form handler can not be executed without a valid course object');
        }

        if(!$this->platformGroupTeam instanceof PlatformGroupTeam)
        {
            throw new \RuntimeException('The form handler can not be executed without a valid platform group team object');
        }

        $data = $form->getData();

        $teamName = $data[PlatformGroupTeamType::ELEMENT_NAME];
        $platformGroupIds = json_decode($data[PlatformGroupTeamType::ELEMENT_PLATFORM_GROUPS]);

        $this->platformGroupTeamService->createTeamForSelectedGroups(
            $this->user, $this->course, $teamName, $platformGroupIds
        );

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
        // TODO: Implement rollBackModel() method.
    }
}
