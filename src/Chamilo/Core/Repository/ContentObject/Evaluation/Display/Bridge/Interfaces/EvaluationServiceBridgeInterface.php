<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface EvaluationServiceBridgeInterface
{
    /**
     *
     * @return boolean
     */
    public function canEditEvaluation();

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType();

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser);

    /**
     *
     * @return ContextIdentifier
     */
    public function getContextIdentifier();


    /**
     *
     * @return int[]
     */
    public function getTargetEntityIds();

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId);

    /**
     * @param int $evaluationId
     * @param int $userId
     * @param int $entityId
     *
     * @return EvaluationEntryScore
     */
    public function saveEntityAsPresent(int $evaluationId, int $userId, int $entityId): EvaluationEntryScore;

    /**
     * @param int $evaluationId
     * @param int $userId
     * @param int $entityId
     *
     * @return EvaluationEntryScore
     */
    public function saveEntityAsAbsent(int $evaluationId, int $userId, int $entityId): EvaluationEntryScore;

    /**
     * @param int $evaluationId
     * @param int $userId
     * @param int $entityId
     * @param string $score
     *
     * @return EvaluationEntryScore
     */
    public function saveEntryScoreForEntity(int $evaluationId, int $userId, int $entityId, string $score): EvaluationEntryScore;

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, $entityType, $entityId);

}