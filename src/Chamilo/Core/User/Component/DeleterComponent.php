<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package user.lib.user_manager.component
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (! $this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $ids);

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $failures = 0;

            foreach ($ids as $id)
            {
                $user = DataManager::retrieve_by_id(
                    User::class,
                    (int) $id);

                if (! DataManager::user_deletion_allowed($user))
                {
                    $failures ++;
                    continue;
                }

                if ($user->delete())
                {
                    Event::trigger(
                        'Delete',
                        Manager::CONTEXT,
                        array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
                }
                else
                {
                    $failures ++;
                }
            }

            $message = $this->get_result(
                $failures,
                count($ids),
                'UserNotDeleted',
                'UsersNotDeleted',
                'UserDeleted',
                'UsersDeleted');

            $this->redirectWithMessage(
                $message,
                ($failures > 0),
                array(Application::PARAM_ACTION => self::ACTION_BROWSE_USERS));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('User')),
                        StringUtilities::LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_USERS)),
                Translation::get('AdminUserBrowserComponent')));
    }
}
