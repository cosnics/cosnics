<?php
namespace Chamilo\Core\Metadata\ControlledVocabulary;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'vocabulary_action';
    const PARAM_CONTROLLED_VOCABULARY_ID = 'controlled_vocabulary_id';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_CREATE = 'creator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

}
