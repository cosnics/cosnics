<?php
namespace Chamilo\Core\MetadataOld\Value;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'value_action';
    const ACTION_ATTRIBUTE = 'Attribute';
    const ACTION_ELEMENT = 'Element';
    const ACTION_EDITOR = 'Editor';
    const DEFAULT_ACTION = self :: ACTION_EDITOR;
}
