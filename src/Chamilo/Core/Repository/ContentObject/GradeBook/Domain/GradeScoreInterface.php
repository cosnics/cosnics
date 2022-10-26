<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Domain;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Domain
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
interface GradeScoreInterface
{
    /**
     * @return float|null
     */
    public function getValue(): ?float;

    /**
     * @return bool
     */
    public function hasValue(): bool;

    /**
     * @return bool
     */
    public function isNull(): bool;

    /**
     * @return bool
     */
    public function isAuthAbsent(): bool;

    /**
     * @param GradeScoreInterface $gradeScore
     *
     * @return bool
     */
    public function hasPresedenceOver(GradeScoreInterface $gradeScore): bool;
}