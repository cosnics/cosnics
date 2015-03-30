<?php
namespace Chamilo\Core\Metadata\Value\Element;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'element_value_action';
    const PARAM_ELEMENT_VALUE_ID = 'element_value_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const ACTION_IMPORT = 'Importer';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
