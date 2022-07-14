<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
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
    public function getContextIdentifier(): ContextIdentifier;

    /**
     * @return boolean
     */
    public function canEditGradeBook(): bool;

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array;

    public function getContextTitle(): string;

    /**
     * @return GradeBookItem[]
     */
    public function findPublicationGradeBookItems();
}
