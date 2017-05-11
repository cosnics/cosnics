<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyChildAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Utilities\UUID;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingRepository implements LearningPathTrackingRepositoryInterface
{
    /**
     * LearningPathTrackingRepository constructor.
     */
    public function __construct()
    {
        $storage = $this->getStorage();

        if (!isset($storage))
        {
            $this->setStorage(array());
        }
    }

    /**
     * Empties the storage
     */
    public function resetStorage()
    {
        $this->setStorage(array());
    }

    /**
     * Returns the stored data from the session
     *
     * @return array
     */
    protected function getStorage()
    {
        return unserialize(Session::retrieve(__NAMESPACE__));
    }

    /**
     * Sets the stored data
     *
     * @param array $data
     */
    protected function setStorage($data)
    {
        Session::register(__NAMESPACE__, serialize($data));
    }

    /**
     * Stores data in the storage
     *
     * @param string $property
     * @param mixed $value
     */
    protected function setInStorage($property, $value)
    {
        $data = $this->getStorage();
        $data[$property] = $value;
        $this->setStorage($data);
    }

    /**
     * Returns data from the storage
     *
     * @param string $property
     *
     * @return mixed
     */
    protected function getFromStorage($property)
    {
        $data = $this->getStorage();

        return $data[$property];
    }

    /**
     * Finds a learning path attempt by a given learning path and user
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return LearningPathAttempt | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLearningPathAttemptForUser(LearningPath $learningPath, User $user)
    {
        $attempts = $this->getFromStorage(DummyAttempt::class);

        return $attempts[$learningPath->getId()][$user->getId()];
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearLearningPathAttemptCache()
    {
    }

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return LearningPathChildAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathChildAttempts(LearningPathAttempt $learningPathAttempt)
    {
        $childAttempts = $this->getFromStorage(DummyChildAttempt::class);

        return $childAttempts[$learningPathAttempt->getId()];
    }

    /**
     * Finds all the LearningPathChildAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator | LearningPathChildAttempt[]
     */
    public function findLearningPathChildAttemptsForLearningPath(LearningPath $learningPath)
    {
        $allChildAttempts = array();

        $attempts = $this->getFromStorage(DummyAttempt::class);
        $childAttempts = $this->getFromStorage(DummyChildAttempt::class);

        $learningPathAttempts = $attempts[$learningPath->getId()];

        foreach($learningPathAttempts as $learningPathAttempt)
        {
            /** @var LearningPathAttempt $learningPathAttempt */

            $learningPathAttemptChildAttempts = $childAttempts[$learningPathAttempt->getId()];

            if(is_array($learningPathAttemptChildAttempts))
            {
                $allChildAttempts += $learningPathAttemptChildAttempts;
            }
        }

        return $allChildAttempts;
    }

    /**
     * Finds a LearningPathChildAttempt by a given LearningPathAttempt and LearningPathTreeNode
     *
     * @param LearningPathAttempt $learningPathAttempt
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return LearningPathChildAttempt | DataClass
     */
    public function findActiveLearningPathChildAttempt(
        LearningPathAttempt $learningPathAttempt, LearningPathTreeNode $learningPathTreeNode
    )
    {
        /** @var DummyChildAttempt[][] $childAttempts */
        $childAttempts = $this->getFromStorage(DummyChildAttempt::class);
        $childAttemptsForLearningPathAttempt = $childAttempts[$learningPathAttempt->getId()];

        foreach ($childAttemptsForLearningPathAttempt as $childAttempt)
        {
            if ($childAttempt->get_learning_path_item_id() == $learningPathTreeNode->getId() &&
                !$childAttempt->isFinished()
            )
            {
                return $childAttempt;
            }
        }

        return null;
    }

    /**
     * Finds a LearningPathChildAttempt by a given ID
     *
     * @param int $learningPathChildAttemptId
     *
     * @return DataClass | LearningPathChildAttempt
     */
    public function findLearningPathChildAttemptById($learningPathChildAttemptId)
    {
        /** @var DummyChildAttempt[][] $childAttempts */
        $childAttempts = $this->getFromStorage(DummyChildAttempt::class);
        foreach ($childAttempts as $learningPathAttemptId => $childAttemptsForLearningPathAttempt)
        {
            if (array_key_exists($learningPathChildAttemptId, $childAttemptsForLearningPathAttempt))
            {
                return $childAttemptsForLearningPathAttempt[$learningPathChildAttemptId];
            }
        }

        return null;
    }

    /**
     * Finds the LearningPathQuestionAttempt objects for a given LearningPathChildAttempt
     *
     * @param LearningPathChildAttempt $learningPathChildAttempt
     *
     * @return LearningPathQuestionAttempt[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findLearningPathQuestionAttempts(LearningPathChildAttempt $learningPathChildAttempt)
    {
        /** @var DummyQuestionAttempt[][] $questionAttempts */
        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);

        return $questionAttempts[$learningPathChildAttempt->getId()];
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function create(DataClass $dataClass)
    {
        switch (get_class($dataClass))
        {
            case DummyAttempt::class:
                return $this->createDummyAttempt($dataClass);
            case DummyChildAttempt::class:
                return $this->createDummyChildAttempt($dataClass);
            case DummyQuestionAttempt::class:
                return $this->createDummyQuestionAttempt($dataClass);
        }

        return false;
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function update(DataClass $dataClass)
    {
        switch (get_class($dataClass))
        {
            case DummyAttempt::class:
                return $this->createDummyAttempt($dataClass);
            case DummyChildAttempt::class:
                return $this->createDummyChildAttempt($dataClass);
            case DummyQuestionAttempt::class:
                return $this->createDummyQuestionAttempt($dataClass);
        }

        return false;
    }

    /**
     * @param DataClass $dataClass
     *
     * @return bool
     */
    public function delete(DataClass $dataClass)
    {
        switch (get_class($dataClass))
        {
            case DummyAttempt::class:
                return $this->deleteDummyAttempt($dataClass);
            case DummyChildAttempt::class:
                return $this->deleteDummyChildAttempt($dataClass);
            case DummyQuestionAttempt::class:
                return $this->deleteDummyQuestionAttempt($dataClass);
        }

        return false;
    }

    /**
     * Creates a DummyAttempt
     *
     * @param DummyAttempt $dummyAttempt
     *
     * @return bool
     */
    protected function createDummyAttempt(DummyAttempt $dummyAttempt)
    {
        if(!$dummyAttempt->is_identified())
        {
            $dummyAttempt->setId(UUID::v4());
        }

        $attempts = $this->getFromStorage(DummyAttempt::class);
        $attempts[$dummyAttempt->getLearningPathId()][$dummyAttempt->get_user_id()] = $dummyAttempt;

        $this->setInStorage(DummyAttempt::class, $attempts);

        return true;
    }

    /**
     * Deletes a DummyAttempt
     *
     * @param DummyAttempt $dummyAttempt
     *
     * @return bool
     */
    protected function deleteDummyAttempt(DummyAttempt $dummyAttempt)
    {
        $attempts = $this->getFromStorage(DummyAttempt::class);
        unset($attempts[$dummyAttempt->getLearningPathId()][$dummyAttempt->get_user_id()]);

        $this->setInStorage(DummyAttempt::class, $attempts);

        return true;
    }

    /**
     * Creates a DummyChildAttempt
     *
     * @param DummyChildAttempt $dummyChildAttempt
     *
     * @return bool
     */
    protected function createDummyChildAttempt(DummyChildAttempt $dummyChildAttempt)
    {
        if(!$dummyChildAttempt->is_identified())
        {
            $dummyChildAttempt->setId(UUID::v4());
        }

        $itemAttempts = $this->getFromStorage(DummyChildAttempt::class);
        $itemAttempts[$dummyChildAttempt->get_learning_path_attempt_id()][$dummyChildAttempt->getId()] =
            $dummyChildAttempt;

        $this->setInStorage(DummyChildAttempt::class, $itemAttempts);

        return true;
    }

    /**
     * Deletes a DummyChildAttempt
     *
     * @param DummyChildAttempt $dummyChildAttempt
     *
     * @return bool
     */
    protected function deleteDummyChildAttempt(DummyChildAttempt $dummyChildAttempt)
    {
        $itemAttempts = $this->getFromStorage(DummyChildAttempt::class);
        unset($itemAttempts[$dummyChildAttempt->get_learning_path_attempt_id()][$dummyChildAttempt->getId()]);

        $this->setInStorage(DummyChildAttempt::class, $itemAttempts);

        return true;
    }

    /**
     * Creates a DummyQuestionAttempt
     *
     * @param DummyQuestionAttempt $dummyQuestionAttempt
     *
     * @return bool
     */
    protected function createDummyQuestionAttempt(DummyQuestionAttempt $dummyQuestionAttempt)
    {
        if(!$dummyQuestionAttempt->is_identified())
        {
            $dummyQuestionAttempt->setId(UUID::v4());
        }

        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);
        $questionAttempts[$dummyQuestionAttempt->get_item_attempt_id()][$dummyQuestionAttempt->getId()] =
            $dummyQuestionAttempt;

        $this->setInStorage(DummyQuestionAttempt::class, $questionAttempts);

        return true;
    }

    /**
     * Deletes a DummyQuestionAttempt
     *
     * @param DummyQuestionAttempt $dummyQuestionAttempt
     *
     * @return bool
     */
    protected function deleteDummyQuestionAttempt(DummyQuestionAttempt $dummyQuestionAttempt)
    {
        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);
        unset($questionAttempts[$dummyQuestionAttempt->get_item_attempt_id()][$dummyQuestionAttempt->getId()]);

        $this->setInStorage(DummyQuestionAttempt::class, $questionAttempts);

        return true;
    }

    /**
     * Retrieves all the LearningPathAttempt objects with the LearningPathChildAttempt objects and
     * LearningPathQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return RecordIterator
     */
    public function findLearningPathAttemptsWithLearningPathChildAttemptsAndLearningPathQuestionAttempts(
        LearningPath $learningPath
    )
    {
        /** @var LearningPathAttempt[][] $attempts */
        $attempts = $this->getFromStorage(DummyAttempt::class);

        /** @var LearningPathChildAttempt[][] $childAttempts */
        $childAttempts = $this->getFromStorage(DummyChildAttempt::class);

        /** @var LearningPathQuestionAttempt[][] $questionAttempts */
        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);

        $learningPathAttempts = $attempts[$learningPath->getId()];

        $allData = array();

        foreach($learningPathAttempts as $userId => $learningPathAttempt)
        {
            $learningPathChildAttempts = $childAttempts[$learningPathAttempt->getId()];
            foreach($learningPathChildAttempts as $childAttempt)
            {
                $childQuestionAttempts = $questionAttempts[$childAttempt->getId()];
                foreach($childQuestionAttempts as $questionAttempt)
                {
                    $allData[]= array(
                        'learning_path_attempt_id' => $learningPathAttempt->getId(),
                        LearningPathAttempt::PROPERTY_USER_ID => $learningPathAttempt->get_user_id(),
                        LearningPathAttempt::PROPERTY_LEARNING_PATH_ID => $learningPathAttempt->getLearningPathId(),
                        LearningPathAttempt::PROPERTY_PROGRESS => $learningPathAttempt->get_progress(),
                        'learning_path_child_attempt_id' => $childAttempt->getId(),
                        LearningPathChildAttempt::PROPERTY_LEARNING_PATH_ITEM_ID => $childAttempt->get_learning_path_item_id(),
                        LearningPathChildAttempt::PROPERTY_START_TIME => $childAttempt->get_start_time(),
                        LearningPathChildAttempt::PROPERTY_TOTAL_TIME => $childAttempt->get_total_time(),
                        LearningPathChildAttempt::PROPERTY_SCORE => $childAttempt->get_score(),
                        LearningPathChildAttempt::PROPERTY_STATUS => $childAttempt->get_status(),
                        'learning_path_question_attempt_id' => $questionAttempt->getId(),
                        LearningPathQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID => $questionAttempt->get_question_complex_id(),
                        LearningPathQuestionAttempt::PROPERTY_ANSWER => $questionAttempt->get_answer(),
                        LearningPathQuestionAttempt::PROPERTY_FEEDBACK => $questionAttempt->get_feedback(),
                        LearningPathQuestionAttempt::PROPERTY_SCORE => $questionAttempt->get_score(),
                        LearningPathQuestionAttempt::PROPERTY_HINT => $questionAttempt->get_hint()
                    );
                }
            }
        }

        return new RecordIterator(LearningPathAttempt::class_name(), $allData);
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearLearningPathChildAttemptCache()
    {

    }

    /**
     * Finds the LearningPathAttempt objects for a given LearningPath with a given condition, offset, count and orderBy
     * Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findLearningPathAttemptsWithUser(
        LearningPath $learningPath, Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        return new RecordIterator(DummyAttempt::class_name(), array());
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUser(LearningPath $learningPath, Condition $condition = null)
    {
        return 0;
    }

    /**
     * Finds the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, Condition $condition = null, $offset = 0, $count = 0, $orderBy = array()
    )
    {
        return new RecordIterator(User::class_name(), array());
    }

    /**
     * Counts the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersWithLearningPathAttempts(LearningPath $learningPath, Condition $condition = null)
    {
        return 0;
    }

    /**
     * Counts the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath)
    {
        return 0;
    }

    /**
     * Counts the target users with attempts on a learning path that are completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function findUsersWithCompletedNodesCount(LearningPath $learningPath)
    {
        return 0;
    }

    /**
     * Counts the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath)
    {
        return 0;
    }

    /**
     * Counts the total number of target users for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return int
     */
    public function countTargetUsers(LearningPath $learningPath)
    {
        return 0;
    }

    /**
     * Finds the target users without attempts on a learning path
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithoutLearningPathAttempts(LearningPath $learningPath)
    {
        return new RecordIterator(User::class_name(), array());
    }

    /**
     * Finds the target users with attempts on a learning path that are not completed
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithPartialLearningPathAttempts(LearningPath $learningPath)
    {
        return new RecordIterator(User::class_name(), array());
    }
}