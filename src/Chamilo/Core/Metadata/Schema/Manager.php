<?php
namespace Chamilo\Core\Metadata\Schema;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'schema_action';
    const PARAM_SCHEMA_ID = 'schema_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
