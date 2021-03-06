<?php
namespace Chamilo\Core\User\Email;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package application.common.email_manager
 */
abstract class Manager extends Application
{
    const ACTION_EMAIL = 'Emailer';
    const DEFAULT_ACTION = self::ACTION_EMAIL;
    const PARAM_ACTION = 'email_action';

    private $target_users;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->target_users = array();

        $email_action = Request::get(self::PARAM_ACTION);
        if ($email_action)
        {
            $this->set_parameter(self::PARAM_ACTION, $email_action);
        }
    }

    public function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    public function get_target_users()
    {
        return $this->target_users;
    }
}
