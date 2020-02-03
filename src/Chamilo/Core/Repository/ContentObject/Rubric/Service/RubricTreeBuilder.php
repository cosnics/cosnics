<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

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
     *
     * @return RubricData
     */
    public function buildRubricTreeByRubricDataId(int $rubricDataId)
    {
        $rubricData = $this->rubricDataRepository->findEntireRubricById($rubricDataId);

        $treeNodes = $rubricData->getTreeNodes();
        foreach($treeNodes as $treeNode)
        {
            $treeNode->setChildren(new ArrayCollection());
        }

        foreach($treeNodes as $treeNode)
        {
            if($treeNode->hasParentNode())
            {
                $treeNode->getParentNode()->getChildren()->add($treeNode);
            }
        }

        return $rubricData;
    }
}
