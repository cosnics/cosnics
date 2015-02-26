<?php
namespace Chamilo\Core\Metadata\Value;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'value_action';
    const ACTION_ATTRIBUTE = 'attribute';
    const ACTION_ELEMENT = 'element';
    const ACTION_EDITOR = 'editor';
    const DEFAULT_ACTION = self :: ACTION_EDITOR;

}
