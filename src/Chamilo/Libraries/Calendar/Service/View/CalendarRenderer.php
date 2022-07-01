<?php
namespace Chamilo\Libraries\Calendar\Service\View;

use Chamilo\Libraries\Architecture\Traits\ClassContext;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class CalendarRenderer
{
    use ClassContext;

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return static::context();
    }
}
