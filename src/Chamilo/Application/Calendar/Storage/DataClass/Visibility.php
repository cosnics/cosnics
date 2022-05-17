<?php
namespace Chamilo\Application\Calendar\Storage\DataClass;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Visibility extends \Chamilo\Libraries\Calendar\Event\Visibility
{

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'calendar_visibility';
    }
}
