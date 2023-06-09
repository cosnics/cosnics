<?php
namespace Chamilo\Core\Home\Rights\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\Home\Rights\Ajax
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    public const CONTEXT = __NAMESPACE__;
}
