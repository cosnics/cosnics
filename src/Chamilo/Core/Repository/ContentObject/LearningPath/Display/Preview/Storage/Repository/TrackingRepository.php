<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepositoryInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingRepository implements TrackingRepositoryInterface
{
    /**
     * TrackingRepository constructor.
     */
    public function __construct()
    {
        $storage = $this->getStorage();

        if (empty($storage))
        {
            $this->setStorage([]);
        }
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeAttemptCache()
    {
    }

    /**
     * Clears the cache for the LearningPathAttempt data class
     */
    public function clearTreeNodeQuestionAttemptCache()
    {
    }

    /**
     * Counts the learning path attempts joined with users for searching
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     * @param Condition $condition
     *
     * @return int
     */
    public function countLearningPathAttemptsWithUser(
        LearningPath $learningPath, $treeNodeDataIds = [], Condition $condition = null
    )
    {
        return 0;
    }

    /**
     * Counts the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param Condition $condition
     *
     * @return int
     */
    public function countTargetUsersForLearningPath(LearningPath $learningPath, Condition $condition = null)
    {
        return 0;
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
            case DummyTreeNodeAttempt::class:
                return $this->createDummyTreeNodeAttempt($dataClass);
            case DummyQuestionAttempt::class:
                return $this->createDummyQuestionAttempt($dataClass);
        }

        return false;
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
        if (!$dummyQuestionAttempt->isIdentified())
        {
            $dummyQuestionAttempt->setId(Uuid::v4()->__toString());
        }

        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);
        $questionAttempts[$dummyQuestionAttempt->getTreeNodeAttemptId()][$dummyQuestionAttempt->getId()] =
            $dummyQuestionAttempt;

        $this->setInStorage(DummyQuestionAttempt::class, $questionAttempts);

        return true;
    }

    /**
     * Creates a DummyTreeNodeAttempt
     *
     * @param DummyTreeNodeAttempt $dummyTreeNodeAttempt
     *
     * @return bool
     */
    protected function createDummyTreeNodeAttempt(DummyTreeNodeAttempt $dummyTreeNodeAttempt)
    {
        if (!$dummyTreeNodeAttempt->isIdentified())
        {
            $dummyTreeNodeAttempt->setId(Uuid::v4()->__toString());
        }

        $treeNodeAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);
        $treeNodeAttempts[$dummyTreeNodeAttempt->getLearningPathId()][$dummyTreeNodeAttempt->getUserId()]
        [$dummyTreeNodeAttempt->getId()] = $dummyTreeNodeAttempt;

        $this->setInStorage(DummyTreeNodeAttempt::class, $treeNodeAttempts);

        return true;
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
            case DummyTreeNodeAttempt::class:
                return $this->deleteDummyTreeNodeAttempt($dataClass);
            case DummyQuestionAttempt::class:
                return $this->deleteDummyQuestionAttempt($dataClass);
        }

        return false;
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
        unset($questionAttempts[$dummyQuestionAttempt->getTreeNodeAttemptId()][$dummyQuestionAttempt->getId()]);

        $this->setInStorage(DummyQuestionAttempt::class, $questionAttempts);

        return true;
    }

    /**
     * Deletes a DummyTreeNodeAttempt
     *
     * @param DummyTreeNodeAttempt $dummyTreeNodeAttempt
     *
     * @return bool
     */
    protected function deleteDummyTreeNodeAttempt(DummyTreeNodeAttempt $dummyTreeNodeAttempt)
    {
        $itemAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);
        unset(
            $itemAttempts[$dummyTreeNodeAttempt->getLearningPathId()]
            [$dummyTreeNodeAttempt->getUserId()][$dummyTreeNodeAttempt->getId()]
        );

        $this->setInStorage(DummyTreeNodeAttempt::class, $itemAttempts);

        return true;
    }

    /**
     * Finds a TreeNodeAttempt by a given LearningPathAttempt and TreeNode
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     * @param User $user
     *
     * @return TreeNodeAttempt|DataClass
     */
    public function findActiveTreeNodeAttempt(
        LearningPath $learningPath, TreeNode $treeNode, User $user
    )
    {
        /** @var DummyTreeNodeAttempt[][][] $treeNodeAttempts */
        $treeNodeAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);
        $treeNodeAttemptsForUser = $treeNodeAttempts[$learningPath->getId()][$user->getId()];

        foreach ($treeNodeAttemptsForUser as $treeNodeAttempt)
        {
            if ($treeNodeAttempt->getTreeNodeDataId() == $treeNode->getId() && !$treeNodeAttempt->isCompleted())
            {
                return $treeNodeAttempt;
            }
        }

        return null;
    }

    /**
     * Retrieves all the LearningPathAttempt objects with the TreeNodeAttempt objects and
     * TreeNodeQuestionAttempt objects for a given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return ArrayCollection
     */
    public function findLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts(
        LearningPath $learningPath
    )
    {
        /** @var TreeNodeAttempt[][][] $treeNodeAttempts */
        $treeNodeAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);

        /** @var TreeNodeQuestionAttempt[][] $questionAttempts */
        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);

        $allData = [];

        foreach ($treeNodeAttempts as $learningPathId => $learningPathTreeNodeAttempts)
        {
            foreach ($learningPathTreeNodeAttempts as $userTreeNodeAttempts)
            {
                foreach ($userTreeNodeAttempts as $treeNodeAttempt)
                {
                    $childQuestionAttempts = $questionAttempts[$treeNodeAttempt->getId()];
                    foreach ($childQuestionAttempts as $questionAttempt)
                    {
                        $allData[] = [
                            TreeNodeAttempt::PROPERTY_USER_ID => $treeNodeAttempt->getUserId(),
                            TreeNodeAttempt::PROPERTY_LEARNING_PATH_ID => $treeNodeAttempt->getLearningPathId(),
                            'tree_node_attempt_id' => $treeNodeAttempt->getId(),
                            TreeNodeAttempt::PROPERTY_TREE_NODE_DATA_ID => $treeNodeAttempt->getTreeNodeDataId(),
                            TreeNodeAttempt::PROPERTY_START_TIME => $treeNodeAttempt->get_start_time(),
                            TreeNodeAttempt::PROPERTY_TOTAL_TIME => $treeNodeAttempt->get_total_time(),
                            TreeNodeAttempt::PROPERTY_SCORE => $treeNodeAttempt->get_score(),
                            TreeNodeAttempt::PROPERTY_COMPLETED => $treeNodeAttempt->isCompleted(),
                            'tree_node_question_attempt_id' => $questionAttempt->getId(),
                            TreeNodeQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID => $questionAttempt->get_question_complex_id(
                            ),
                            TreeNodeQuestionAttempt::PROPERTY_ANSWER => $questionAttempt->get_answer(),
                            TreeNodeQuestionAttempt::PROPERTY_FEEDBACK => $questionAttempt->get_feedback(),
                            TreeNodeQuestionAttempt::PROPERTY_SCORE => $questionAttempt->get_score(),
                            TreeNodeQuestionAttempt::PROPERTY_HINT => $questionAttempt->get_hint()
                        ];
                    }
                }
            }
        }

        return new ArrayCollection($allData);
    }

    /**
     * Finds the LearningPathAttempt objects for a given LearningPath with a given condition, offset, count and orderBy
     * Joined with users for searching and sorting
     *
     * @param LearningPath $learningPath
     * @param int[] $treeNodeDataIds
     * @param Condition|null $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return ArrayCollection
     */
    public function findLearningPathAttemptsWithUser(
        LearningPath $learningPath, $treeNodeDataIds = [], Condition $condition = null, $offset = 0, $count = 0,
        $orderBy = null
    )
    {
        return new ArrayCollection([]);
    }

    /**
     * Finds the targeted users (left) joined with the learning path attempts
     *
     * @param LearningPath $learningPath
     * @param array $treeNodeDataIds
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return ArrayCollection
     */
    public function findTargetUsersWithLearningPathAttempts(
        LearningPath $learningPath, $treeNodeDataIds = [], Condition $condition = null, $offset = 0, $count = 0,
        $orderBy = null
    )
    {
        return new ArrayCollection([]);
    }

    /**
     * Finds a TreeNodeAttempt by a given ID
     *
     * @param int $treeNodeAttemptId
     *
     * @return DataClass | TreeNodeAttempt
     */
    public function findTreeNodeAttemptById($treeNodeAttemptId)
    {
        /** @var DummyTreeNodeAttempt[][][] $treeNodeAttempts */
        $treeNodeAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);

        foreach ($treeNodeAttempts as $learningPathId => $learningPathTreeNodeAttempts)
        {
            foreach ($learningPathTreeNodeAttempts as $userId => $userTreeNodeAttempts)
            {
                if (array_key_exists($treeNodeAttemptId, $userTreeNodeAttempts))
                {
                    return $userTreeNodeAttempts[$treeNodeAttemptId];
                }
            }
        }

        return null;
    }

    /**
     * Finds the learning path child attempts for a given learning path attempt
     *
     * @param LearningPath $learningPath
     * @param User $user
     *
     * @return TreeNodeAttempt[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findTreeNodeAttempts(LearningPath $learningPath, User $user)
    {
        $treeNodeAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);

        return $treeNodeAttempts[$learningPath->getId()][$user->getId()];
    }

    /**
     * Finds all the TreeNodeAttempt objects for a given LearningPath
     *
     * @param LearningPath $learningPath
     *
     * @return \Doctrine\Common\Collections\ArrayCollection | TreeNodeAttempt[]
     */
    public function findTreeNodeAttemptsForLearningPath(LearningPath $learningPath)
    {
        $treeNodeAttempts = $this->getFromStorage(DummyTreeNodeAttempt::class);
        $allTreeNodeAttempts = [];

        $treeNodeAttemptsForLearningPath = $treeNodeAttempts[$learningPath->getId()];

        foreach ($treeNodeAttemptsForLearningPath as $treeNodeAttempts)
        {
            if (is_array($treeNodeAttempts))
            {
                $allTreeNodeAttempts += $treeNodeAttempts;
            }
        }

        return $allTreeNodeAttempts;
    }

    /**
     * Finds the TreeNodeQuestionAttempt objects for a given TreeNodeAttempt
     *
     * @param TreeNodeAttempt $treeNodeAttempt
     *
     * @return TreeNodeQuestionAttempt[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function findTreeNodeQuestionAttempts(TreeNodeAttempt $treeNodeAttempt)
    {
        /** @var DummyQuestionAttempt[][] $questionAttempts */
        $questionAttempts = $this->getFromStorage(DummyQuestionAttempt::class);

        return $questionAttempts[$treeNodeAttempt->getId()];
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

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    /**
     * Returns the stored data from the session
     *
     * @return array
     */
    protected function getStorage()
    {
        return unserialize($this->getSession()->get(__NAMESPACE__));
    }

    /**
     * Empties the storage
     */
    public function resetStorage()
    {
        $this->setStorage([]);
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
     * Sets the stored data
     *
     * @param array $data
     */
    protected function setStorage($data)
    {
        $this->getSession()->set(__NAMESPACE__, serialize($data));
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
            case DummyTreeNodeAttempt::class:
                return $this->createDummyTreeNodeAttempt($dataClass);
            case DummyQuestionAttempt::class:
                return $this->createDummyQuestionAttempt($dataClass);
        }

        return false;
    }

}