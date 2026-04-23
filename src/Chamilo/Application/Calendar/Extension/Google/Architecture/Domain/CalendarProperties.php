<?php
namespace Chamilo\Application\Calendar\Extension\Google\Architecture\Domain;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarProperties
{
    public function __construct(public string $summary, public string $description, public string $timeZone)
    {
    }
}