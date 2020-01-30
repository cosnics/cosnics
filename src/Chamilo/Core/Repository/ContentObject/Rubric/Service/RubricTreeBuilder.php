<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Service;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricDataRepository;
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

        /** @var PersistentCollection $treeNodes */
        $treeNodes = $rubricData->getTreeNodes();
        var_dump($rubricData->getRootNode()->getChildren()->isInitialized());



        return $rubricData;
    }
}
