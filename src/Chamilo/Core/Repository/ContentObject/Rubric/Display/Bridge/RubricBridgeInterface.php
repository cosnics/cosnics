<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Interface RubricBridgeInterface
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface RubricBridgeInterface
{
    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier();

    /**
     * @return string
     */
    public function getEntityName();

    /**
     * @return User[]
     */
    public function getTargetUsers();

    /**
     * @param User $user
     * @param float $totalScore
     */
    public function saveScore(User $user, float $totalScore, float $maxScore);
}
