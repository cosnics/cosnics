<?php
namespace Chamilo\Application\Calendar\Storage\DataClass;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;

/**
 * @package Chamilo\Application\Calendar\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Visibility extends \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility implements UuidDataClassInterface
{
    public const CONTEXT = Manager::CONTEXT;

    public static function getStorageUnitName(): string
    {
        return 'calendar_visibility';
    }
}
