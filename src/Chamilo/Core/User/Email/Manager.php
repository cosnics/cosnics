<?php
namespace Chamilo\Core\User\Email;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package application.common.email_manager
 */
abstract class Manager extends Application
{
    public const ACTION_EMAIL = 'Emailer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_EMAIL;

    public const PARAM_ACTION = 'email_action';

    private $target_users;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->target_users = [];

        $email_action = $this->getRequest()->query->get(self::PARAM_ACTION);

        if ($email_action)
        {
            $this->set_parameter(self::PARAM_ACTION, $email_action);
        }
    }

    public function get_target_users()
    {
        return $this->target_users;
    }

    public function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }
}
