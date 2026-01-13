<?php
namespace Chamilo\Application\Calendar\Extension\Google\Architecture\Exception;

use Exception;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotConfiguredException extends Exception
{
    public function __construct(string $settingName)
    {
        parent::__construct(
            'The system could not find a valid configuration fot the following setting: ' . $settingName
        );
    }
}