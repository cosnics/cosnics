<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Table;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableRenderer extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\EntityTableRenderer
{
    public const TABLE_IDENTIFIER = DataClass::PROPERTY_ID;

    protected function isEntity($entityId, $userId): bool
    {
        return true;
    }
}