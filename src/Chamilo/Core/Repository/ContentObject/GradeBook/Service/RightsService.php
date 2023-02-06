<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Service;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
//use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
//use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class RightsService
{
    /**
     * @var GradeBookServiceBridgeInterface
     */
    protected $gradebookServiceBridge;

    /**
     * @param GradeBookServiceBridgeInterface $gradebookServiceBridge
     */
    public function setGradeBookServiceBridge(GradeBookServiceBridgeInterface $gradebookServiceBridge)
    {
        $this->gradebookServiceBridge = $gradebookServiceBridge;
    }

    /**
     * @return bool
     */
    public function canUserEditGradeBook()
    {
        return $this->gradebookServiceBridge->canEditGradeBook();
    }

//    /**
//     * @param User $user
//     * @param EvaluationEntry $entry
//     *
//     * @return bool
//     */
//    public function canUserViewEntry(User $user, EvaluationEntry $entry)
//    {
//        return $this->canUserViewEntity($user, $entry->getEntityType(), $entry->getEntityId());
//    }

//    /**
//     * @param User $user
//     * @param int $entityType
//     * @param int $entityId
//     *
//     * @return bool
//     */
//    public function canUserViewEntity(User $user, int $entityType, int $entityId)
//    {
//        if ($this->canUserEditEvaluation())
//        {
//            return true;
//        }
//
//        return $this->evaluationServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
//    }
}