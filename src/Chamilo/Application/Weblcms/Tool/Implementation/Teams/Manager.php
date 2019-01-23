<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams;

/**
 * @inheritdoc
 */
class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE_TEAM = 'CreateTeam';
    const ACTION_GO_TO_TEAM = 'GoToTeam';
}