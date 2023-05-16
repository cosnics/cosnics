<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\AssignmentRepository;

/**
 * Abstract service that can be used as a base for the AssignmentService
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\AssignmentRepository
     */
    protected $assignmentRepository;

    /**
     * AssignmentService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\AssignmentRepository $assignmentRepository
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service\FeedbackService $feedbackService
     */
    public function __construct(AssignmentRepository $assignmentRepository, FeedbackService $feedbackService)
    {
        parent::__construct($assignmentRepository, $feedbackService);
    }



    /**
     * @return string
     */
    abstract public function getEntryClassName(): string;

    /**
     * @return string
     */
    abstract public function getScoreClassName(): string;
}