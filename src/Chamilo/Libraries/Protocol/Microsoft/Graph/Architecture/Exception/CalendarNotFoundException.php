<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception;

use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarNotFoundException extends Exception
{
    public function __construct(string $userIdentifier, string $calendarIdentifier)
    {
        parent::__construct(
            'The system could not find a valid calendar for the given userIdentifier (' . $userIdentifier .
            ') and calendarIdentifier (' . $calendarIdentifier . ')'
        );
    }
}