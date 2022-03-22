<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\RubricTreeNodeScore;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResultTargetUser;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Utilities\UUID;

/**
 * Class RubricResultService
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 */
class RubricResultService
{
    /**
     * @var RubricResultRepository
     */
    protected $rubricResultRepository;

    /**
     * @var float
     */
    protected $criteriumEqRestWeight;

    /**
     * RubricResultService constructor.
     *
     * @param RubricResultRepository $rubricResultRepository
     */
    public function __construct(RubricResultRepository $rubricResultRepository)
    {
        $this->rubricResultRepository = $rubricResultRepository;
    }

    /**
     * @param User $user
     * @param User[] $targetUsers
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param TreeNodeResultJSONModel[] $treeNodeResultJSONModels
     * @param \DateTime|null $resultTime
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function storeRubricResults(
        User $user, array $targetUsers, RubricData $rubricData, ContextIdentifier $contextIdentifier,
        array $treeNodeResultJSONModels, \DateTime $resultTime = null
    )
    {
        $uniqueAttemptId = UUID::v4();

        $treeNodeResultJSONModelsById = [];
        foreach ($treeNodeResultJSONModels as $treeNodeResultJSONModel)
        {
            $treeNodeResultJSONModelsById[$treeNodeResultJSONModel->getTreeNodeId()] = $treeNodeResultJSONModel;
        }

        if ($rubricData->useScores() && $rubricData->useRelativeWeights())
        {
            $this->criteriumEqRestWeight = $rubricData->getEqRestWeight();
        }

        $totalScore = $this->calculateAndStoreScoreForTreeNode(
            $user, $rubricData, $contextIdentifier, $rubricData->getRootNode(), $uniqueAttemptId,
            $treeNodeResultJSONModelsById, $resultTime
        )->getScore();

        foreach ($targetUsers as $targetUser)
        {
            $this->createRubricResultTargetUser($targetUser, $uniqueAttemptId);
        }

        $this->rubricResultRepository->flush();

        return $totalScore;
    }

    /**
     * @param User $user
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param TreeNode $treeNode
     * @param string $uniqueAttemptId
     * @param TreeNodeResultJSONModel[] $treeNodeResultJSONModelsById
     * @param \DateTime|null $resultTime
     *
     * @return RubricTreeNodeScore
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function calculateAndStoreScoreForTreeNode(
        User $user, RubricData $rubricData, ContextIdentifier $contextIdentifier, TreeNode $treeNode,
        string $uniqueAttemptId, array $treeNodeResultJSONModelsById, \DateTime $resultTime = null
    ): RubricTreeNodeScore
    {
        $treeNodeResultJSONModel = $treeNodeResultJSONModelsById[$treeNode->getId()];

        if ($treeNode instanceof CriteriumNode)
        {
            if (!$treeNodeResultJSONModel instanceof TreeNodeResultJSONModel)
            {
                throw new \InvalidArgumentException(
                    sprintf('No result found for treenode with id %s', $treeNode->getId())
                );
            }

            $choice = null;
            $level = null;

            $levelId = $treeNodeResultJSONModel->getLevelId();
            if (is_null($levelId))
            {
                $calculatedScore = null;
            }
            else if ($treeNode->hasLevels())
            {
                $level = $rubricData->getLevelById($treeNodeResultJSONModel->getLevelId());
                if ($treeNode !== $level->getCriterium())
                {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The given level %s does not belong to the given criterium %s', $level->getId(),
                            $treeNode->getId()
                        )
                    );
                }
                $calculatedScore = $level->getScore();
            }
            else
            {
                $choice = $rubricData->getChoiceByLevelAndCriteriumId(
                    $treeNodeResultJSONModel->getLevelId(), $treeNodeResultJSONModel->getTreeNodeId()
                );

                if ($treeNode !== $choice->getCriterium())
                {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The given choice %s does not belong to the given criterium %s', $choice->getId(),
                            $treeNode->getId()
                        )
                    );
                }

                $calculatedScore = $choice->calculateScore();
            }


            $this->createRubricResult(
                $user, $rubricData, $contextIdentifier, $uniqueAttemptId, $treeNode, $calculatedScore,
                $treeNodeResultJSONModel->getComment(), $choice, $level, $resultTime
            );

            if ($rubricData->useRelativeWeights())
            {
                $relativeWeight = $treeNode->getRelativeWeight();
                if (is_null($relativeWeight))
                {
                    $relativeWeight = $this->criteriumEqRestWeight;
                }
                return new RubricTreeNodeScore($treeNode, $calculatedScore, $relativeWeight);
            }

            return new RubricTreeNodeScore($treeNode, $calculatedScore);
        }

        $totalScore = 0;
        $totalWeight = null;
        if ($rubricData->useRelativeWeights())
        {
            $score = 0;
            $totalWeight = 0;
            foreach ($treeNode->getChildren() as $child)
            {
                $calculatedScore = $this->calculateAndStoreScoreForTreeNode(
                    $user, $rubricData, $contextIdentifier, $child, $uniqueAttemptId,
                    $treeNodeResultJSONModelsById
                );

                if (is_null($calculatedScore->getScore()))
                {
                    $score = null;
                }

                if (!is_null($score))
                {
                    $score += ($calculatedScore->getScore() * ($calculatedScore->getWeight() / 100));
                }

                $totalWeight += $calculatedScore->getWeight();
            }
            if (!is_null($score))
            {
                $totalScore = $totalWeight === 0 ? 0 : ($score / $totalWeight) * 100;
            }
            else
            {
                $totalScore = null;
            }
        }
        else
        {
            foreach ($treeNode->getChildren() as $child)
            {
                $calculatedScore = $this->calculateAndStoreScoreForTreeNode(
                    $user, $rubricData, $contextIdentifier, $child, $uniqueAttemptId,
                    $treeNodeResultJSONModelsById
                );
                if (is_null($calculatedScore->getScore()))
                {
                    $totalScore = null;
                }
                if (!is_null($totalScore))
                {
                    $totalScore += $calculatedScore->getScore();
                }
            }
        }

        $comment = $treeNodeResultJSONModel instanceof TreeNodeResultJSONModel ?
            $treeNodeResultJSONModel->getComment() : null;

        $this->createRubricResult(
            $user, $rubricData, $contextIdentifier, $uniqueAttemptId, $treeNode, $totalScore, $comment,
            null, null, $resultTime
        );

        return new RubricTreeNodeScore($treeNode, $totalScore, $totalWeight);
    }

    /**
     * @param User $user
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param string $uniqueAttemptId
     * @param TreeNode $treeNode
     * @param float|null $score
     * @param string|null $comment
     * @param Choice|null $choice
     * @param Level|null $level
     * @param \DateTime|null $resultTime
     *
     * @return RubricResult
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createRubricResult(
        User $user, RubricData $rubricData, ContextIdentifier $contextIdentifier,
        string $uniqueAttemptId, TreeNode $treeNode, ?float $score, string $comment = null, Choice $choice = null, Level $level = null,
        \DateTime $resultTime = null
    )
    {
        if (empty($resultTime))
        {
            $resultTime = new \DateTime();

        }

        $rubricResult = new RubricResult();

        $rubricResult->setRubricData($rubricData)
            ->setEvaluatorUserId($user->getId())
            ->setContextIdentifier($contextIdentifier)
            ->setResultId($uniqueAttemptId)
            ->setTreeNode($treeNode)
            ->setSelectedChoice($choice)
            ->setSelectedLevel($level)
            ->setScore($score)
            ->setComment($comment)
            ->setTime($resultTime);

        $this->rubricResultRepository->saveRubricResult($rubricResult, false);

        return $rubricResult;
    }

    /**
     * @param User $targetUser
     * @param string $uniqueAttemptId
     *
     * @return RubricResultTargetUser
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createRubricResultTargetUser(User $targetUser, string $uniqueAttemptId)
    {
        $rubricResultTargetUser = new RubricResultTargetUser();
        $rubricResultTargetUser->setRubricResultGUID($uniqueAttemptId);
        $rubricResultTargetUser->setTargetUserId($targetUser->getId());

        $this->rubricResultRepository->saveRubricResultTargetUser($rubricResultTargetUser, false);

        return $rubricResultTargetUser;
    }

    /**
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     *
     * @return RubricResult[]
     */
    public function getRubricResultsForContext(
        RubricData $rubricData, ContextIdentifier $contextIdentifier
    )
    {
        return $this->rubricResultRepository->getRubricResultsForContext($rubricData, $contextIdentifier);
    }

    /**
     * @param RubricData $rubricData
     *
     * @return bool
     */
    public function doesRubricHaveResults(RubricData $rubricData)
    {
        return $this->rubricResultRepository->countRubricResultsForRubric($rubricData) > 0;
    }
}
