<?php
namespace Chamilo\Core\Lynx\Source;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'source_action';
    const PARAM_SOURCE_ID = 'source_id';
    const ACTION_BROWSE = 'Browser';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
