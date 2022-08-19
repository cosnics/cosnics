<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Interfaces
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
interface GradeBookServiceBridgeInterface
{
    /**
     * @return ContextIdentifier
     */
//    public function getContextIdentifier(): ContextIdentifier;

    /**
     * @return boolean
     */
    public function canEditGradeBook(): bool;

    /**
     * @param FilterParameters|null $filterParameters
     *
     * @return User[]
     */
    public function getTargetUsers(FilterParameters $filterParameters = null): array;

    /**
     * @param FilterParameters|null $filterParameters
     *
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array;

    public function getContextTitle(): string;

    /**
     * @return GradeBookItem[]
     */
    public function findPublicationGradeBookItems();

    /**
     * @param GradeBookItem $gradeBookItem
     *
     * @return array
     */
    public function findScores(GradeBookItem $gradeBookItem);
}
