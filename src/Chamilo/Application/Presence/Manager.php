<?php
namespace Chamilo\Application\Presence;

use Chamilo\Application\ExamAssignment\Service\ExamAssignmentService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Authentication\AuthenticationValidator;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const ACTION_PRESENCE_REGISTRATION = 'PresenceRegistration';
    const DEFAULT_ACTION = self::ACTION_PRESENCE_REGISTRATION;

    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_TREE_NODE_ID = 'tree_node_id';
    const PARAM_PRESENCE_PERIOD_ID = 'presence_period_id';
    const PARAM_SECURITY_KEY = 'sk';
}
