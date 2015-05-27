<?php
namespace Chamilo\Core\MetadataOld\Attribute;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'attribute_action';
    const PARAM_ATTRIBUTE_ID = 'attribute_id';
    const PARAM_MOVE = 'move';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const ACTION_MOVE = 'Mover';
    const ACTION_VOCABULATE = 'Vocabulator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
