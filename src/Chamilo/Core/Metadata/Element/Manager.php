<?php
namespace Chamilo\Core\Metadata\Element;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'element_action';
    const PARAM_ELEMENT_ID = 'element_id';
    const PARAM_MOVE = 'move';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const ACTION_MOVE = 'Mover';
    const ACTION_ASSOCIATE = 'Associator';
    const ACTION_VOCABULATE = 'Vocabulator';
    const PROPERTY_ASSOCIATIONS = 'associations';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
