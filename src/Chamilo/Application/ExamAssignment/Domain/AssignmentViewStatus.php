<?php

namespace Chamilo\Application\ExamAssignment\Domain;

use Chamilo\Libraries\Architecture\Exceptions\ValueNotInArrayException;

/**
 * Class AssignmentViewStatus
 * @package Chamilo\Application\ExamAssignment\Domain
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentViewStatus
{
    const STATUS_ALLOWED = 1;
    const STATUS_NO_RIGHTS = 2;
    const STATUS_WRONG_CODE = 3;
    const STATUS_ASSIGNMENT_NOT_IN_PROGRESS = 4;
    const STATUS_CORRUPT_DATA = 5;

    /**
     * @var string
     */
    protected $status;

    /**
     * AssignmentViewStatus constructor.
     *
     * @param string $status
     */
    public function __construct(string $status)
    {
        $statuses = [
            self::STATUS_ALLOWED, self::STATUS_NO_RIGHTS, self::STATUS_WRONG_CODE,
            self::STATUS_ASSIGNMENT_NOT_IN_PROGRESS, self::STATUS_CORRUPT_DATA
        ];

        if (!in_array($status, $statuses))
        {
            throw new ValueNotInArrayException('status', $status, $statuses);
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return $this->status == self::STATUS_ALLOWED;
    }

    /**
     * @return bool
     */
    public function isNotAllowed()
    {
        return !$this->isAllowed();
    }

}

