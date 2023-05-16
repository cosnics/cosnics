<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\DataClass;

use Chamilo\Core\Repository\Quota\Rights\Manager;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRight extends \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_quota_rights_location_entity_right';
    }
}