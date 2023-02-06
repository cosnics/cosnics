<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Domain
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class GradeScore implements GradeScoreInterface
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isAuthAbsent(): bool
    {
        return false;
    }

    /**
     * @param GradeScoreInterface $gradeScore
     *
     * @return bool
     */
    public function hasPresedenceOver(GradeScoreInterface $gradeScore): bool
    {
        if (!$gradeScore->hasValue())
        {
            return true;
        }
        return $this->value > $gradeScore->getValue();
    }
}