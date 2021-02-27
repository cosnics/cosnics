<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax;

use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service\UserOvertimeService;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component\AjaxComponent;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const PARAM_ACTION = 'AjaxAction';
    const ACTION_LIST_USERS = 'ListUsers';
    const ACTION_ADD_USER_OVERTIME = 'AddUserOvertime';
    const ACTION_UPDATE_USER_OVERTIME = 'UpdateUserOvertime';
    const ACTION_DELETE_USER_OVERTIME = 'DeleteUserOvertime';
    const ACTION_SET_MULTIPLE_USERS_OVERTIME = 'SetMultipleUsersOvertime';

    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_EXTRA_TIME = 'extra_time';
    const PARAM_USER_OVERTIME_ID = 'user_overtime_id';
    const PARAM_DB_ACTIONS = 'db_actions';
    const PARAM_DB_ACTION_TYPE = 'db_action_type';

    const PARAM_RESULTS = 'results';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if (!$this->get_application() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'This ajax call can only be called through the weblcms context. The parent of this component should therefore be the' .
                ' AjaxComponent from the exam assignment tool.'
            );
        }
    }

    /**
     * @return UserOvertimeService
     */
    public function getUserOvertimeService()
    {
        return $this->getService(UserOvertimeService::class);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application|AjaxComponent
     */
    public function getAjaxComponent()
    {
        return $this->get_application();
    }
}
