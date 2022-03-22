<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Preview\Bridge;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Class RubricBridge
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricBridge implements \Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge\RubricBridgeInterface
{

    public function getContextIdentifier()
    {
        // TODO: Implement getContextIdentifier() method.
    }

    public function getEntityName()
    {
        // TODO: Implement getEntityName() method.
    }

    public function getTargetUsers()
    {
        // TODO: Implement getTargetUsers() method.
    }

    public function saveScore(User $user, float $totalScore, float $maxScore)
    {
        // TODO: Implement saveScore() method.
    }

    public function getPostSaveRedirectParameters()
    {
        // TODO: Implement getPostSaveRedirectParameters() method.
    }

    public function setEntryURL(string $url)
    {
        // TODO: Implement setEntryURL() method.
    }

    public function getEntryURL(): string
    {
        // TODO: Implement getEntryURL() method.
    }
}
