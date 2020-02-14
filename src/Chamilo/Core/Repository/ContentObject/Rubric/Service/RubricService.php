<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;

/**
 * Class RubricService
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Service
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricService
{
    /**
     * @var RubricDataRepository
     */
    protected $rubricDataRepository;

    /**
     * @var RubricValidator
     */
    protected $rubricValidator;

    /**
     * @var RubricTreeBuilder
     */
    protected $rubricTreeBuilder;

    /**
     * RubricService constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     * @param RubricValidator $rubricValidator
     * @param RubricTreeBuilder $rubricTreeBuilder
     */
    public function __construct(
        RubricDataRepository $rubricDataRepository, RubricValidator $rubricValidator,
        RubricTreeBuilder $rubricTreeBuilder
    )
    {
        $this->rubricDataRepository = $rubricDataRepository;
        $this->rubricValidator = $rubricValidator;
        $this->rubricTreeBuilder = $rubricTreeBuilder;
    }

    /**
     * Retrieves a rubric from the database
     *
     * @param int $rubricDataId
     * @param int $expectedVersion
     *
     * @return RubricData
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getRubric(int $rubricDataId, int $expectedVersion)
    {
        return $this->rubricTreeBuilder->buildRubricTreeByRubricDataId($rubricDataId, $expectedVersion);
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubric(RubricData $rubricData)
    {
        $rubricData->setLastUpdated(new \DateTime());
        $this->rubricValidator->validateRubric($rubricData);
        $this->rubricDataRepository->saveRubricData($rubricData);
    }

    /**
     * Removes the tree node from the database. If the tree node has choices they are removed from the
     * database as well
     *
     * @param TreeNode $treeNode
     *
     * @throws \Exception
     */
    public function removeTreeNodeFromDatabase(TreeNode $treeNode)
    {
        if ($treeNode->getRubricData() instanceof RubricData)
        {
            throw new \RuntimeException(
                'First disconnect the tree node from the rubric data before removing it from the database.'
            );
        }

        $this->rubricDataRepository->executeTransaction(
            function () use ($treeNode) {
                $this->rubricDataRepository->removeTreeNode($treeNode);
                if ($treeNode instanceof CriteriumNode)
                {
                    foreach ($treeNode->getChoices() as $choice)
                    {
                        $this->rubricDataRepository->removeChoice($choice);
                    }
                }

                foreach($treeNode->getChildren() as $child)
                {
                    $this->removeTreeNodeFromDatabase($child);
                }
            }
        );
    }

    /**
     * @param Level $level
     *
     * @throws \Exception
     */
    public function removeLevelFromDatabase(Level $level)
    {
        if($level->getRubricData() instanceof RubricData)
        {
            throw new \RuntimeException(
                'First disconnect the level from the rubric data before removing it from the database.'
            );
        }

        $this->rubricDataRepository->executeTransaction(
            function () use ($level) {
                $this->rubricDataRepository->removeLevel($level);
                foreach ($level->getChoices() as $choice)
                {
                    $this->rubricDataRepository->removeChoice($choice);
                }
            }
        );

    }
}
