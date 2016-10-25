<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 *
 * @package group.lib.group_manager.component
 */
class SubscriberComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user = $this->get_user();
        $group_id = Request :: get(self :: PARAM_GROUP_ID);
        $this->set_parameter(self :: PARAM_GROUP_ID, $group_id);

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $users = $this->getRequest()->get(self :: PARAM_USER_ID);

        $failures = 0;

        if (! empty($users))
        {
            if (! is_array($users))
            {
                $users = array($users);
            }

            foreach ($users as $user)
            {
                $existing_groupreluser = $this->retrieve_group_rel_user($user, $group_id);

                if (! is_null($existing_groupreluser))
                {
                    $groupreluser = new GroupRelUser();
                    $groupreluser->set_group_id($group_id);
                    $groupreluser->set_user_id($user);

                    if (! $groupreluser->create())
                    {
                        $failures ++;
                    }
                    else
                    {
                        Event :: trigger(
                            'SubscribeUser',
                            Manager :: context(),
                            array(
                                \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change :: PROPERTY_REFERENCE_ID => $groupreluser->get_group_id(),
                                \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change :: PROPERTY_TARGET_USER_ID => $groupreluser->get_user_id(),
                                \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change :: PROPERTY_USER_ID => $this->get_user()->get_id()));
                    }
                }
                else
                {
                    $contains_dupes = true;
                }
            }

            if ($failures)
            {
                if (count($users) == 1)
                {
                    $message = 'SelectedUserNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedUsersNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            else
            {
                if (count($users) == 1)
                {
                    $message = 'SelectedUserAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedUsersAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
            }

            $this->redirect(
                Translation :: get($message),
                ($failures ? true : false),
                array(Application :: PARAM_ACTION => self :: ACTION_VIEW_GROUP, self :: PARAM_GROUP_ID => $group_id));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS)),
                Translation :: get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_VIEW_GROUP,
                        self :: PARAM_GROUP_ID => Request :: get(self :: PARAM_GROUP_ID))),
                Translation :: get('ViewerComponent')));
        $breadcrumbtrail->add_help('group general');
    }
}
