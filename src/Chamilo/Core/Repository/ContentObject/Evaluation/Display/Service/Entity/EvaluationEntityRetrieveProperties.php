<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity;

use MyCLabs\Enum\Enum;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
final class EvaluationEntityRetrieveProperties extends Enum
{
    private const NONE = 0;
    private const SCORES = 1;
    private const FEEDBACK = 2;
    private const RUBRIC = 3;
    private const SCORES_FEEDBACK = 4;
    private const SCORES_RUBRIC = 5;
    private const FEEDBACK_RUBRIC = 6;
    private const ALL = 7;

    /**
     * @return bool
     */
    public function retrieveScores(): bool
    {
        switch ($this->getValue())
        {
            case self::ALL:
            case self::SCORES:
            case self::SCORES_FEEDBACK:
            case self::SCORES_RUBRIC:
                return true;
            case self::NONE:
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function retrieveFeedback(): bool
    {
        switch ($this->getValue())
        {
            case self::ALL:
            case self::FEEDBACK:
            case self::SCORES_FEEDBACK:
            case self::FEEDBACK_RUBRIC:
                return true;
            case self::NONE:
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function retrieveRubric(): bool
    {
        switch ($this->getValue())
        {
            case self::ALL:
            case self::RUBRIC:
            case self::SCORES_RUBRIC:
            case self::FEEDBACK_RUBRIC:
                return true;
            case self::NONE:
            default:
                return false;
        }
    }
}