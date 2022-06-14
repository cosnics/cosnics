<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
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
abstract class UserApproverComponent extends Manager
{
    const PARAM_CHOICE = 'choice';
    const CHOICE_APPROVE = 1;
    const CHOICE_DENY = 0;

    abstract protected function getChoice();

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        $ids = $this->getRequest()->get(self::PARAM_USER_USER_ID);
        $this->set_parameter(self::PARAM_USER_USER_ID, $ids);

        $choice = $this->getChoice();
        $this->set_parameter(self::PARAM_USER_USER_ID, $choice);

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

                if ($choice == self::CHOICE_APPROVE)
                {
                    $user->set_active(1);
                    $user->set_approved(1);

                    if ($user->update())
                    {
                        Event::trigger(
                            'Update',
                            Manager::context(),
                            array(
                                ChangesTracker::PROPERTY_REFERENCE_ID => $user->get_id(),
                                ChangesTracker::PROPERTY_USER_ID => $this->get_user()->get_id()));
                    }
                    else
                    {
                        $failures ++;
                    }
                }
                else
                {
                    if (! DataManager::user_deletion_allowed($user))
                    {
                        continue;
                    }

                    if ($user->delete())
                    {
                        Event::trigger(
                            'Delete',
                            Manager::context(),
                            array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
                    }
                    else
                    {
                        $failures ++;
                    }
                }
            }

            if ($choice == self::CHOICE_APPROVE)
            {
                $message = $this->get_result(
                    $failures,
                    count($ids),
                    'UserNotApproved',
                    'UsersNotApproved',
                    'UserApproved',
                    'UsersApproved');
            }
            else
            {
                $message = $this->get_result(
                    $failures,
                    count($ids),
                    'UserNotDenied',
                    'UsersNotDenied',
                    'UserDenied',
                    'UsersDenied');
            }

            $this->redirect(
                $message,
                ($failures > 0),
                array(Application::PARAM_ACTION => self::ACTION_USER_APPROVAL_BROWSER));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get('NoObjectSelected'),
                    array('OBJECT' => Translation::get('User')),
                    StringUtilities::LIBRARIES));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_USER_APPROVAL_BROWSER)),
                Translation::get('UserApprovalBrowserComponent')));
    }
}
