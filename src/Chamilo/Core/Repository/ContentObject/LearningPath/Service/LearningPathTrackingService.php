<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Service to manage the tracking of attempts in a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingService
{
    /**
     * @var LearningPathTrackingRepository
     */
    protected $learningPathTrackingRepository;

    /**
     * LearningPathTrackingService constructor.
     *
     * @param LearningPathTrackingRepository $learningPathTrackingRepository
     */
    public function __construct(LearningPathTrackingRepository $learningPathTrackingRepository)
    {
        $this->learningPathTrackingRepository = $learningPathTrackingRepository;
    }

    /**
     * Returns the existing learning path attempt or creates a new one for the given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    public function getOrCreateLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        $learningPathAttempt = $this->getLearningPathAttemptForUser($learningPath, $user);
        if ($learningPathAttempt instanceof LearningPathAttempt)
        {
            return $learningPathAttempt;
        }

        return $this->createLearningPathAttemptForUser($learningPath, $user);
    }

    /**
     * Returns a LearningPathAttempt for a given LearningPath and User
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    public function getLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        return $this->learningPathTrackingRepository->findLearningPathAttemptForUser($learningPath, $user);
    }

    /**
     * Creates a new LearningPathAttempt for a given LearningPath and User
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt
     */
    public function createLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        $learningPathAttempt = $this->createLearningPathAttemptInstance();

        $learningPathAttempt->setLearningPathId($learningPath->getId());
        $learningPathAttempt->set_user_id($user->getId());
        $learningPathAttempt->set_progress(0);

        $learningPathAttempt->create();

        $this->learningPathTrackingRepository->clearLearningPathAttemptCache();

        return $learningPathAttempt;
    }

    /**
     * Returns the existing and active LearningPathChildAttempt or creates a new one for the given
     * LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt
     */
    public function getOrCreateActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $activeLearningPathChildAttempt = $this->getActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );

        if($activeLearningPathChildAttempt instanceof LearningPathChildAttempt)
        {
            return $activeLearningPathChildAttempt;
        }

        return $this->createLearningPathChildAttempt($learningPathAttempt, $learningPathTreeNode);
    }

    /**
     * Returns the active LearningPathChildAttempt for a given LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt
     */
    public function getActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        return $this->learningPathTrackingRepository->findActiveLearningPathChildAttempt(
            $learningPathAttempt, $learningPathTreeNode
        );
    }

    /**
     * Creates a LearningPathChildAttempt for a given LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt
     */
    public function createLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        $learningPathChildAttempt = $this->createLearningPathChildAttemptInstance();

        $learningPathChildAttempt->set_learning_path_attempt_id($learningPathAttempt->getId());
        $learningPathChildAttempt->set_learning_path_item_id($learningPathTreeNode->getId());
        $learningPathChildAttempt->set_start_time(time());
        $learningPathChildAttempt->set_total_time(0);
        $learningPathChildAttempt->set_score(0);
        $learningPathChildAttempt->set_min_score(0);
        $learningPathChildAttempt->set_max_score(0);
        $learningPathChildAttempt->set_status(LearningPathChildAttempt::STATUS_NOT_ATTEMPTED);
        $learningPathChildAttempt->create();

        return $learningPathChildAttempt;
    }

    /**
     * Returns the learning path item attempts, sorted by the children to which they belong
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return LearningPathChildAttempt[][]
     */
    public function getLearningPathChildAttempts(LearningPathAttempt $learningPathAttempt)
    {
        $learningPathChildAttempts =
            $this->learningPathTrackingRepository->findLearningPathChildAttempts($learningPathAttempt);

        $attempt_data = array();

        foreach ($learningPathChildAttempts as $learningPathChildAttempt)
        {
            $attempt_data[$learningPathChildAttempt->get_learning_path_item_id()][] = $learningPathChildAttempt;
        }

        return $attempt_data;
    }

    /**
     * Returns the LearningPathQuestionAttempt objects for a given LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathItemAttempt
     *
     * @return array
     */
    public function getLearningPathQuestionAttempts(
        LearningPathChildAttempt $learningPathItemAttempt
    )
    {
        $learningPathQuestionAttempts =
            $this->learningPathTrackingRepository->findLearningPathQuestionAttempts($learningPathItemAttempt);

        $learningPathQuestionAttemptsPerQuestion = array();

        foreach ($learningPathQuestionAttempts as $learningPathQuestionAttempt)
        {
            $learningPathQuestionAttemptsPerQuestion[$learningPathQuestionAttempt->get_question_complex_id()] =
                $learningPathQuestionAttempt;
        }

        return $learningPathQuestionAttemptsPerQuestion;
    }

    /**
     * Creates a LearningPathQuestionAttempt for a given LearningPathChildAttempt and question identifier
     *
     * @param LearningPathChildAttempt $learningPathChildAttempt
     * @param int $questionId
     */
    public function createLearningPathQuestionAttempt(LearningPathChildAttempt $learningPathChildAttempt, $questionId)
    {
        $learningPathQuestionAttempt = $this->createLearningPathQuestionAttemptInstance();

        $learningPathQuestionAttempt->set_item_attempt_id($learningPathChildAttempt->getId());
        $learningPathQuestionAttempt->set_question_complex_id($questionId);
        $learningPathQuestionAttempt->set_answer('');
        $learningPathQuestionAttempt->set_score(0);
        $learningPathQuestionAttempt->set_feedback('');
        $learningPathQuestionAttempt->set_hint(0);

        $learningPathQuestionAttempt->create();
    }

    /**
     * Creates a new instance of the LearningPathAttempt extension
     *
     * @return LearningPathAttempt
     */
    protected function createLearningPathAttemptInstance()
    {
        $learningPathAttempt = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt();

        $learningPathAttempt->set_course_id($this->get_course_id());
        $learningPathAttempt->set_learning_path_id($this->get_publication()->get_id());

        return $learningPathAttempt;
    }

    /**
     * Creates a new instance of the LearningPathChildAttempt extension
     *
     * @return LearningPathChildAttempt
     */
    protected function createLearningPathChildAttemptInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt();
    }

    /**
     * Creates a new instance of the LearningPathQuestionAttempt extension
     *
     * @return LearningPathQuestionAttempt
     */
    protected function createLearningPathQuestionAttemptInstance()
    {
        return new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt();
    }

}