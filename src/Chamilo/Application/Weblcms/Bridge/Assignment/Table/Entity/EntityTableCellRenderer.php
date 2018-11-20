<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity;

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
     * {@inheritdoc}
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity\EntityTableCellRenderer::isEntity()
     */
    protected function isEntity($entityId, $userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->getTable()->getEntityService()->isUserPartOfEntity(
            $user, $this->getTable()->getContentObjectPublication(), $entityId
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\User\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}