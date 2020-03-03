<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\CriteriumResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Choice;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
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
     * @var RubricDataRepository
     */
    protected $rubricDataRepository;

    /**
     * RubricResultService constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     */
    public function __construct(RubricDataRepository $rubricDataRepository)
    {
        $this->rubricDataRepository = $rubricDataRepository;
    }

    /**
     * @param User $user
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param CriteriumResultJSONModel[] $criteriumResultJSONModels
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Exception
     */
    public function storeRubricResults(
        User $user, RubricData $rubricData, ContextIdentifier $contextIdentifier, array $criteriumResultJSONModels
    )
    {
        $uniqueAttemptId = UUID::v4();
        $criteriumScores = [];

        foreach ($criteriumResultJSONModels as $criteriumResultJSONModel)
        {
            $treeNode = $rubricData->getTreeNodeById($criteriumResultJSONModel->getCriteriumTreeNodeId());
            $choice = $rubricData->getChoiceById($criteriumResultJSONModel->getChoiceId());

            $calculatedScore = $choice->getLevel()->getScore();
            if ($treeNode instanceof CriteriumNode)
            {
                $calculatedScore *= ($treeNode->getWeight() / 100);
            }

            $this->createRubricResult(
                $user, $rubricData, $contextIdentifier, $uniqueAttemptId, $treeNode, $calculatedScore, $choice
            );

            $criteriumScores[$treeNode->getId()] = $calculatedScore;
        }

        $this->calculateAndStoreContainerScore(
            $user, $rubricData, $contextIdentifier, $rubricData->getRootNode(), $uniqueAttemptId, $criteriumScores
        );
    }

    /**
     * @param User $user
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param TreeNode $treeNode
     * @param string $uniqueAttemptId
     * @param array $criteriumScores
     *
     * @return int
     * @throws \Exception
     */
    protected function calculateAndStoreContainerScore(
        User $user, RubricData $rubricData, ContextIdentifier $contextIdentifier, TreeNode $treeNode,
        string $uniqueAttemptId, array $criteriumScores
    )
    {
        if ($treeNode instanceof CriteriumNode)
        {
            if (array_key_exists($treeNode->getId(), $criteriumScores))
            {
                return $criteriumScores[$treeNode->getId()];
            }

            return 0;
        }

        $totalScore = 0;
        foreach ($treeNode->getChildren() as $child)
        {
            $totalScore += $this->calculateAndStoreContainerScore(
                $user, $rubricData, $contextIdentifier, $child, $uniqueAttemptId, $criteriumScores
            );
        }

        $this->createRubricResult($user, $rubricData, $contextIdentifier, $uniqueAttemptId, $treeNode, $totalScore);

        return $totalScore;
    }

    /**
     * @param User $user
     * @param RubricData $rubricData
     * @param ContextIdentifier $contextIdentifier
     * @param string $uniqueAttemptId
     * @param TreeNode $treeNode
     * @param int $score
     * @param Choice|null $choice
     *
     * @return RubricResult
     *
     * @throws \Exception
     */
    protected function createRubricResult(
        User $user, RubricData $rubricData, ContextIdentifier $contextIdentifier, string $uniqueAttemptId,
        TreeNode $treeNode, int $score, Choice $choice = null
    )
    {
        $rubricResult = new RubricResult();

        $rubricResult->setRubricData($rubricData)
            ->setUserId($user->getId())
            ->setContextIdentifier($contextIdentifier)
            ->setAttemptId($uniqueAttemptId)
            ->setTreeNode($treeNode)
            ->setSelectedChoice($choice)
            ->setScore($score)
            ->setAttemptTime(new \DateTime());

        return $rubricResult;
    }

}
