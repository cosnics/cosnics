<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResultTargetUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
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
     * @param RubricResult $rubricResult
     * @param bool $flush
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeRubricResult(RubricResult $rubricResult, bool $flush = true)
    {
        $this->removeEntity($rubricResult, $flush);
    }

    /**
     * @param RubricResultTargetUser $rubricResultTargetUser
     * @param bool $flush
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubricResultTargetUser(RubricResultTargetUser $rubricResultTargetUser, bool $flush = true)
    {
        return $this->saveEntity($rubricResultTargetUser, $flush);
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

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData $rubricData
     * @param \Chamilo\Libraries\Architecture\ContextIdentifier $contextIdentifier
     *
     * @return RubricResult[]
     */
    public function getRubricResultsForContext(
        RubricData $rubricData, ContextIdentifier $contextIdentifier
    )
    {
        $conditions = [
            'rubricData' => $rubricData, 'contextClass' => $contextIdentifier->getContextClass(),
            'contextId' => $contextIdentifier->getContextId()
        ];

        return $this->findBy($conditions, ['time' => 'ASC', 'resultId' => 'ASC']);
    }

    /**
     * @param RubricData $rubricData
     *
     * @return int
     */
    public function countRubricResultsForRubric(RubricData $rubricData)
    {
        return $this->count(['rubricData' => $rubricData]);
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     * @return int
     */
    public function countRubricResultsForContextIdentifier(ContextIdentifier $contextIdentifier)
    {
        return $this->count(['contextClass' => $contextIdentifier->getContextClass(), 'contextId' => $contextIdentifier->getContextId()]);
    }
}
