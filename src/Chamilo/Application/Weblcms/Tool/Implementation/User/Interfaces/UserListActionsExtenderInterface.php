<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Interfaces;

use Chamilo\Libraries\Format\Structure\Toolbar;

/**
 * Interface for user list actions extender
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserListActionsExtenderInterface
{

    public function getActions(Toolbar $toolbar, string $currentUserId);
}