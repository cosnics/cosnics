<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Interfaces;

/**
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface ConfirmRubricScoreInterface
{
    /**
     * @param int $score
     */
    public function registerRubricScore(int $score): void;
}
