<?php
namespace Chamilo\Core\Metadata\Element;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'element_action';
    const PARAM_ELEMENT_ID = 'element_id';
    const PARAM_MOVE = 'move';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_CREATE = 'creator';
    const ACTION_MOVE = 'mover';
    const ACTION_ASSOCIATE = 'associator';
    const ACTION_VOCABULATE = 'vocabulator';
    const PROPERTY_ASSOCIATIONS = 'associations';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
