<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\CommonEntityRepository;

/**
 * Class RubricResultRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricResultRepository extends CommonEntityRepository
{

    /**
     * @param RubricResult $rubricResult
     * @param bool $flush
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubricResult(RubricResult $rubricResult, bool $flush = true)
    {
        return $this->saveEntity($rubricResult, $flush);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData $rubricData
     *
     * @return RubricResult[]
     */
    public function getRubricResultsForRubricData(
        \Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData $rubricData
    )
    {
        return $this->findBy(['rubricData' => $rubricData]);
    }
}
