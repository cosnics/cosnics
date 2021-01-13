<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class QuickUserSubscriberStatus
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class QuickUserSubscriberStatus
{
    const STATUS_SUCCESS = 1;
    const STATUS_USER_NOT_FOUND = 2;
    const STATUS_USER_NOT_SUBSCRIBED_IN_COURSE = 3;
    const STATUS_COURSE_GROUP_NOT_FOUND = 4;
    const STATUS_UNKNOWN_ERROR = 10;

    /**
     * @var string
     */
    protected $userIdentifier;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var CourseGroup
     */
    protected $courseGroup;

    /**
     * QuickUserSubscriberStatus constructor.
     *
     * @param string $userIdentifier
     * @param int $status
     * @param User $user
     * @param CourseGroup $courseGroup
     */
    public function __construct(string $userIdentifier, int $status, User $user = null, CourseGroup $courseGroup = null)
    {
        $this->userIdentifier = $userIdentifier;
        $this->status = $status;
        $this->user = $user;
        $this->courseGroup = $courseGroup;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return CourseGroup
     */
    public function getCourseGroup(): ?CourseGroup
    {
        return $this->courseGroup;
    }
}
