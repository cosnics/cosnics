<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRight extends \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight
{
    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_quota_rights_location_entity_right';
    }
}