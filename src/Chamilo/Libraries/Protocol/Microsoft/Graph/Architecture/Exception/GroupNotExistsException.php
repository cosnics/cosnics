<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception;

use Exception;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GroupNotExistsException extends Exception
{
    public function __construct(string $groupId)
    {
        parent::__construct(
            'Group does not exist in office365: ' . $groupId
        );
    }
}