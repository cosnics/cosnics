<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User;

/**
 * Implementations of this interface provide the functionality to connect to
 * other applications and to retrieve actions from those applications.
 */
interface WeblcmsUserToolConnector
{

    public function get_toolbar_items($user_id);

    public function is_active();
}