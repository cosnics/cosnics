<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Domain
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class NullScore implements GradeScoreInterface
{
    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return null;
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAbsent(): bool
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
        return false;
    }
}