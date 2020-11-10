<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
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
     * @param User $targetUser
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param TreeNodeResultJSONModel[] $treeNodeResultJSONModels
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function storeRubricResults(
        User $user, User $targetUser, RubricData $rubricData, ContextIdentifier $contextIdentifier,
        array $treeNodeResultJSONModels
    )
    {
        $uniqueAttemptId = UUID::v4();

        $treeNodeResultJSONModelsById = [];
        foreach ($treeNodeResultJSONModels as $treeNodeResultJSONModel)
        {
            $treeNodeResultJSONModelsById[$treeNodeResultJSONModel->getTreeNodeId()] = $treeNodeResultJSONModel;
        }

        $totalScore = $this->calculateAndStoreScoreForTreeNode(
            $user, $targetUser, $rubricData, $contextIdentifier, $rubricData->getRootNode(), $uniqueAttemptId,
            $treeNodeResultJSONModelsById
        );

        $this->rubricResultRepository->flush();

        return $totalScore;
    }

    /**
     * @param User $user
     * @param User $targetUser
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param TreeNode $treeNode
     * @param string $uniqueAttemptId
     * @param TreeNodeResultJSONModel[] $treeNodeResultJSONModelsById
     *
     * @return int
     * @throws \Exception
     */
    protected function calculateAndStoreScoreForTreeNode(
        User $user, User $targetUser, RubricData $rubricData, ContextIdentifier $contextIdentifier, TreeNode $treeNode,
        string $uniqueAttemptId, array $treeNodeResultJSONModelsById
    )
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

            $this->createRubricResult(
                $user, $targetUser, $rubricData, $contextIdentifier, $uniqueAttemptId, $treeNode, $calculatedScore,
                $treeNodeResultJSONModel->getComment(), $choice
            );

            return $calculatedScore;
        }

        $totalScore = 0;
        foreach ($treeNode->getChildren() as $child)
        {
            $totalScore += $this->calculateAndStoreScoreForTreeNode(
                $user, $targetUser, $rubricData, $contextIdentifier, $child, $uniqueAttemptId,
                $treeNodeResultJSONModelsById
            );
        }

        $comment = $treeNodeResultJSONModel instanceof TreeNodeResultJSONModel ?
            $treeNodeResultJSONModel->getComment() : null;

        $this->createRubricResult(
            $user, $targetUser, $rubricData, $contextIdentifier, $uniqueAttemptId, $treeNode, $totalScore, $comment
        );

        return $totalScore;
    }

    /**
     * @param User $user
     * @param User $targetUser
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param string $uniqueAttemptId
     * @param TreeNode $treeNode
     * @param float $score
     * @param string|null $comment
     * @param Choice|null $choice
     *
     * @return RubricResult
     *
     * @throws \Exception
     */
    protected function createRubricResult(
        User $user, User $targetUser, RubricData $rubricData, ContextIdentifier $contextIdentifier,
        string $uniqueAttemptId, TreeNode $treeNode, float $score, string $comment = null, Choice $choice = null
    )
    {
        $rubricResult = new RubricResult();

        $rubricResult->setRubricData($rubricData)
            ->setEvaluatorUserId($user->getId())
            ->setContextIdentifier($contextIdentifier)
            ->setResultId($uniqueAttemptId)
            ->setTreeNode($treeNode)
            ->setSelectedChoice($choice)
            ->setScore($score)
            ->setTargetUserId($targetUser->getId())
            ->setComment($comment)
            ->setTime(new \DateTime());

        $this->rubricResultRepository->saveRubricResult($rubricResult, false);

        return $rubricResult;
    }

    /**
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param User $targetUser
     *
     * @return RubricResult[]
     */
    public function getRubricResultsForContext(
        RubricData $rubricData, ContextIdentifier $contextIdentifier, User $targetUser
    )
    {
        return $this->rubricResultRepository->getRubricResultsForContext($rubricData, $contextIdentifier, $targetUser);
    }

    /**
     * @param RubricData $rubricData
     */
    public function doesRubricHaveResults(RubricData $rubricData)
    {
        return $this->rubricResultRepository->countRubricResultsForRubric($rubricData) > 0;
    }

}
