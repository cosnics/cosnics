<?php
namespace Chamilo\Core\Metadata\Attribute;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'attribute_action';
    const PARAM_ATTRIBUTE_ID = 'attribute_id';
    const PARAM_MOVE = 'move';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_CREATE = 'creator';
    const ACTION_MOVE = 'mover';
    const ACTION_VOCABULATE = 'vocabulator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
