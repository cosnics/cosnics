<?php
namespace Chamilo\Core\Metadata\Value\Attribute;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'attribute_value_action';
    const PARAM_ATTRIBUTE_VALUE_ID = 'attribute_value_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const ACTION_IMPORT = 'Importer';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
