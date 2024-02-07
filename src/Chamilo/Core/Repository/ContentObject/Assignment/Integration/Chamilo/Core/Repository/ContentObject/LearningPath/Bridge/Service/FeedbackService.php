<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class FeedbackService
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository
     */
    protected $feedbackRepository;

    /**
     * AssignmentService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        parent::__construct($feedbackRepository);
    }

}