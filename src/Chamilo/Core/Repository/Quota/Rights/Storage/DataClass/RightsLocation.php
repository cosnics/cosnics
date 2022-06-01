<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Libraries\Rights\Domain\RightsLocation
{

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_quota_rights_location';
    }
}
