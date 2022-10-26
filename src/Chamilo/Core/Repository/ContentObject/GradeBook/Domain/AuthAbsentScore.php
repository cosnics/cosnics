<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Domain
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class AuthAbsentScore implements GradeScoreInterface
{
    /**
     * @return float|null
     * @throws \Exception
     */
    public function getValue(): ?float
    {
        throw new \Exception('Score with authorized absence doesn\'t contain a value.');
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
        return false;
    }

    /**
     * @return bool
     */
    public function isAuthAbsent(): bool
    {
        return true;
    }

    /**
     * @param GradeScoreInterface $gradeScore
     *
     * @return bool
     */
    public function hasPresedenceOver(GradeScoreInterface $gradeScore): bool
    {
        return $gradeScore->isNull();
    }
}