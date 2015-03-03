<?php
namespace Chamilo\Application\Portfolio;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * Portfolio Application
 *
 * @package application\portfolio$Manager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'portfolio';

    // Parameters
    const PARAM_USER_ID = 'user_id';

    // Actions
    const ACTION_BROWSE = 'browser';
    const ACTION_HOME = 'home';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_HOME;

    /**
     * Get the "current" user id, which is either the user of whom we are viewing the portfolio or the currently
     * logged-in user
     *
     * @return int
     */
    public function get_current_user_id()
    {
        return Request :: get(self :: PARAM_USER_ID, $this->get_user_id());
    }

    /**
     * Get the "current" user object, which is either the user of whom we are viewing the portfolio or the currently
     * logged-in user
     *
     * @return \user\User
     */
    public function get_current_user()
    {
        return \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            $this->get_current_user_id());
    }
}