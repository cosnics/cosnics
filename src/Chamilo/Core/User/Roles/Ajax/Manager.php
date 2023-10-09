<?php
namespace Chamilo\Core\User\Roles\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\User\Roles\Ajax
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_GET_ROLES_FOR_ELEMENT_FINDER = 'GetRolesForElementFinder';

    public const CONTEXT = __NAMESPACE__;
}
