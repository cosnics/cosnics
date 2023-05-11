<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class EntityTableRenderer
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\EntityTableRenderer
{
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    protected function isEntity($entityId, $userId): bool
    {
        $user = new User();
        $user->setId($userId);

        return $this->getEntityService()->isUserPartOfEntity(
            $user, $this->application->getContentObjectPublication(), $entityId
        );
    }
}
