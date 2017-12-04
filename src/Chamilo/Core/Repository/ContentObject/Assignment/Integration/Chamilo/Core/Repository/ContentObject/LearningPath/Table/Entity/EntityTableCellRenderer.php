<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableCellRenderer
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableCellRenderer
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity\EntityTableCellRenderer::isEntity()
     */
    protected function isEntity($entityId, $userId)
    {
        return $entityId == $userId;
    }
}