<?php
namespace Chamilo\Core\Metadata\Value\Attribute;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'attribute_value_action';
    const PARAM_ATTRIBUTE_VALUE_ID = 'attribute_value_id';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_CREATE = 'creator';
    const ACTION_IMPORT = 'importer';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
