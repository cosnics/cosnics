<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricTreeBuilder
{
    /**
     * @var RubricDataRepository
     */
    protected $rubricDataRepository;

    /**
     * RubricTreeBuilder constructor.
     *
     * @param RubricDataRepository $rubricDataRepository
     */
    public function __construct(RubricDataRepository $rubricDataRepository)
    {
        $this->rubricDataRepository = $rubricDataRepository;
    }

    /**
     * @param int $rubricDataId
     * @param int $expectedVersion
     *
     * @return RubricData
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public function buildRubricTreeByRubricDataId(int $rubricDataId, int $expectedVersion = null)
    {
        $rubricData = $this->rubricDataRepository->findEntireRubricById($rubricDataId, $expectedVersion);

        $treeNodes = $rubricData->getTreeNodes();
        foreach ($treeNodes as $treeNode)
        {
            $treeNode->setChildren(new ArrayCollection());
        }

        foreach ($treeNodes as $treeNode)
        {
            if ($treeNode->hasParentNode())
            {
                $treeNode->getParentNode()->getChildren()->add($treeNode);
            }
        }

        foreach ($rubricData->getLevels() as $level)
        {
            $level->applyCriteriumId();
        }

        return $rubricData;
    }
}
