<?php
namespace Chamilo\Core\Group\Architecture\Exception;

use RuntimeException;

/**
 * @package Chamilo\Core\Group\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupNotFoundException extends RuntimeException
{
    public function __construct(?string $identifier = null, ?string $code = null, ?string $parentIdentifier = null)
    {
        $properties = [];

        if ($identifier)
        {
            $properties[] = 'identifier = ' . $identifier;
        }

        if ($code)
        {
            $properties[] = 'code = ' . $code;
        }

        if ($parentIdentifier)
        {
            $properties[] = 'parentIdentifier = ' . $parentIdentifier;
        }

        parent::__construct('Could not find the group with the following properties: ' . implode(', ', $properties));
    }
}