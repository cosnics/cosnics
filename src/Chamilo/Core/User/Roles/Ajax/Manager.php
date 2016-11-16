<?php
namespace Chamilo\Core\User\Roles\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Application\Weblcms\Ajax
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_GET_ROLES_FOR_ELEMENT_FINDER = 'GetRolesForElementFinder';
}
