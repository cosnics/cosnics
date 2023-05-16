<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service to track attempts on assessments within a LearningPath
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentTrackingService
{
    /**
     * @var AttemptService
     */
    protected $attemptService;

    /**
     * @var AttemptTrackingService
     */
    protected $attemptTrackingService;

    /**
     * @var TrackingRepositoryInterface
     */
    protected $trackingRepository;

    /**
     * AssessmentTrackingService constructor.
     *
     * @param AttemptService $attemptService
     * @param AttemptTrackingService $attemptTrackingService
     * @param TrackingRepositoryInterface $trackingRepository
     */
    public function __construct(
        AttemptService $attemptService, AttemptTrackingService $attemptTrackingService,
        TrackingRepositoryInterface $trackingRepository
    )
    {
        $this->attemptService = $attemptService;
        $this->attemptTrackingService = $attemptTrackingService;
        $this->trackingRepository = $trackingRepository;
    }

    /**
     * Returns whether or not the maximum number of attempts is reached for the given LearningPath, User
     * and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     *
     * @return bool
     */
    public function isMaximumAttemptsReachedForAssessment(
        LearningPath $learningPath, User $user, TreeNode $treeNode
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $treeNodeAttempts =
            $this->attemptService->getTreeNodeAttemptsForTreeNode($learningPath, $user, $treeNode);

        /** @var Assessment $assessment */
        $assessment = $treeNode->getContentObject();

        return $assessment->get_maximum_attempts() > 0 &&
            count($treeNodeAttempts) > $assessment->get_maximum_attempts();
    }

    /**
     * Saves the answer, score and hint for a question for the given LearningPath, User, TreeNode and
     * Question identifier
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $questionIdentifier
     * @param string $answer
     * @param int $score
     * @param string $hint
     */
    public function saveAnswerForQuestion(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $questionIdentifier,
        $answer = '', $score = 0, $hint = ''
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $treeNodeQuestionAttempts = $this->getQuestionAttempts($learningPath, $user, $treeNode);
        $treeNodeQuestionAttempt = $treeNodeQuestionAttempts[$questionIdentifier];

        if (!$treeNodeQuestionAttempt instanceof TreeNodeQuestionAttempt)
        {
            throw new RuntimeException(
                sprintf('The given TreeNodeQuestionAttempt for the question %s is not found', $questionIdentifier)
            );
        }

        $treeNodeQuestionAttempt->set_answer($answer);
        $treeNodeQuestionAttempt->set_score($score);
        $treeNodeQuestionAttempt->set_hint($hint);

        $this->trackingRepository->update($treeNodeQuestionAttempt);
        $this->trackingRepository->clearTreeNodeQuestionAttemptCache();
    }

    /**
     * Saves the assessment score for the given LearningPath, User and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $assessmentScore
     */
    public function saveAssessmentScore(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $assessmentScore = 0
    )
    {
        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        $activeAttempt->set_score($assessmentScore);
        $activeAttempt->calculateAndSetTotalTime();
        $activeAttempt->setCompleted(true);

        $this->trackingRepository->update($activeAttempt);
    }

    /**
     * Changes the assessment score for the given LearningPath, User, TreeNode and
     * TreeNodeAttemptId
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param null $treeNodeAttemptId
     * @param int $newScore
     */
    public function changeAssessmentScore(
        LearningPath $learningPath, User $user,
        TreeNode $treeNode, $treeNodeAttemptId, $newScore = 0
    )
    {
        $treeNodeAttempt = $this->attemptTrackingService->getTreeNodeAttemptById(
            $learningPath, $user, $treeNode, $treeNodeAttemptId
        );

        $treeNodeAttempt->set_score($newScore);

        $this->trackingRepository->update($treeNodeAttempt);
    }

    /**
     * Changes the score and feedback for a given question in a given TreeNodeAttempt identifier by ID
     * for a given LearningPath, User and TreeNode
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $treeNodeAttemptId
     * @param int $questionIdentifier
     * @param int $score
     * @param string $feedback
     */
    public function changeQuestionScoreAndFeedback(
        LearningPath $learningPath, User $user, TreeNode $treeNode, $treeNodeAttemptId,
        $questionIdentifier, $score = 0, $feedback = ''
    )
    {
        $treeNodeQuestionAttempts = $this->getQuestionAttempts(
            $learningPath, $user, $treeNode, $treeNodeAttemptId
        );

        $treeNodeQuestionAttempt = $treeNodeQuestionAttempts[$questionIdentifier];

        if (!$treeNodeQuestionAttempt instanceof TreeNodeQuestionAttempt)
        {
            throw new RuntimeException(
                sprintf('The given TreeNodeQuestionAttempt for the question %s is not found', $questionIdentifier)
            );
        }

        $treeNodeQuestionAttempt->set_score($score);
        $treeNodeQuestionAttempt->set_feedback($feedback);

        $this->trackingRepository->update($treeNodeQuestionAttempt);
    }

    /**
     * Returns the question attempts for a given LearningPath, User and TreeNode
     * using the given attempt (by id) or the active attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int $treeNodeAttemptId
     *
     * @return TreeNodeQuestionAttempt[]
     */
    public function getQuestionAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode,
        $treeNodeAttemptId = null
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        if (is_null($treeNodeAttemptId))
        {
            $treeNodeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);
        }
        else
        {
            $treeNodeAttempt = $this->attemptTrackingService->getTreeNodeAttemptById(
                $learningPath, $user, $treeNode, $treeNodeAttemptId
            );
        }

        $questionAttempts = $this->attemptService->getTreeNodeQuestionAttempts(
            $treeNodeAttempt
        );

        $questionAttemptPerQuestion = [];

        foreach ($questionAttempts as $questionAttempt)
        {
            $questionAttemptPerQuestion[$questionAttempt->get_question_complex_id()] = $questionAttempt;
        }

        return $questionAttemptPerQuestion;
    }

    /**
     * Registers the question attempts for the given question identifiers
     *
     * @param LearningPath $learningPath
     * @param User $user
     * @param TreeNode $treeNode
     * @param int[] $questionIdentifiers
     *
     * @return TreeNodeQuestionAttempt[]
     */
    public function registerQuestionAttempts(
        LearningPath $learningPath, User $user, TreeNode $treeNode,
        $questionIdentifiers = []
    )
    {
        $this->validateTreeNodeIsAssessment($treeNode);

        $activeAttempt = $this->attemptService->getOrCreateActiveTreeNodeAttempt($learningPath, $treeNode, $user);

        $questionAttemptPerQuestion = [];
        foreach ($questionIdentifiers as $questionIdentifier)
        {
            $questionAttemptPerQuestion[$questionIdentifier] =
                $this->attemptService->createTreeNodeQuestionAttempt(
                    $activeAttempt, $questionIdentifier
                );
        }
        $this->trackingRepository->clearTreeNodeQuestionAttemptCache();

        return $questionAttemptPerQuestion;
    }

    /**
     * Validates that the given TreeNode contains an assessment content object
     *
     * @param TreeNode $treeNode
     */
    protected function validateTreeNodeIsAssessment(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assessment)
        {
            throw new InvalidArgumentException(
                'The given TreeNode is not connected to an assessment'
            );
        }
    }
}