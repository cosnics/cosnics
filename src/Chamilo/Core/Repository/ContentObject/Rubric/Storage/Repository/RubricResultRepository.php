<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricResult;
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
     * @param User $targetUser
     *
     * @return RubricResult[]
     */
    public function getRubricResultsForContext(
        RubricData $rubricData, ContextIdentifier $contextIdentifier, User $targetUser
    )
    {
        return $this->findBy(
            [
                'rubricData' => $rubricData, 'contextClass' => $contextIdentifier->getContextClass(),
                'contextId' => $contextIdentifier->getContextId(), 'targetUserId' => $targetUser->getId()
            ],
            ['time' => 'ASC', 'resultId' => 'ASC']
        );
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
}
