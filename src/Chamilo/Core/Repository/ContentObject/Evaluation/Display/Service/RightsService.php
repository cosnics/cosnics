<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\User\Storage\DataClass\User;

/**
* @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
*
* @author Stefan Gabriëls - Hogeschool Gent
*/
class RightsService
{
    /**
     * @var EvaluationServiceBridgeInterface
     */
    protected $evaluationServiceBridge;

    /**
     * @param EvaluationServiceBridgeInterface $evaluationServiceBridge
     */
    public function setEvaluationServiceBridge(EvaluationServiceBridgeInterface $evaluationServiceBridge)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
    }

    /**
     * @return bool
     */
    public function canUserEditEvaluation()
    {
        return $this->evaluationServiceBridge->canEditEvaluation();
    }

    /**
     * @param User $user
     * @param EvaluationEntry $entry
     *
     * @return bool
     */
    public function canUserViewEntry(User $user, EvaluationEntry $entry)
    {
        return $this->canUserViewEntity($user, $entry->getEntityType(), $entry->getEntityId());
    }

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function canUserViewEntity(User $user, int $entityType, int $entityId)
    {
        if ($this->canUserEditEvaluation())
        {
            return true;
        }

        return $this->evaluationServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
    }
}