<?php
namespace Chamilo\Core\API\Component;

use Chamilo\Core\API\Manager;

/**
 *
 * @package group.lib.group_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class TokenComponent extends Manager
{
    const PARAM_ID = 'id';

    function run()
    {
        var_dump($this->get_parameter(self::PARAM_ID));
    }
}
