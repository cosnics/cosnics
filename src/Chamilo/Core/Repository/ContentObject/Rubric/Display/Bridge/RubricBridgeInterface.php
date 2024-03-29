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
     * @param float $maxScore
     */
    public function saveScore(User $user, float $totalScore, float $maxScore);

    /**
     * @return array|null
     */
    public function getPostSaveRedirectParameters();

    /**
     * @param string $url
     */
    public function setEntryURL(string $url);

    /**
     * @return string
     */
    public function getEntryURL(): string;

    /**
     * @param bool $allow
     */
    public function setAllowCreateFromExistingRubric(bool $allow);

    /**
     * @return bool
     */
    public function getAllowCreateFromExistingRubric(): bool;
}
