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
    const ACTION_ADD_USER_OVERTIME = 'AddUserOvertime';
    const ACTION_UPDATE_USER_OVERTIME = 'UpdateUserOvertime';
    const ACTION_DELETE_USER_OVERTIME = 'DeleteUserOvertime';

    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_EXTRA_TIME = 'extra_time';
    const PARAM_USER_OVERTIME_ID = 'user_overtime_id';

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
}
