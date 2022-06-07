<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package group.lib.group_manager.component
 */
class UnsubscriberComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user = $this->get_user();

        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->get(self::PARAM_GROUP_REL_USER_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $ids);

        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $groupreluser = DataManager::retrieve_by_id(GroupRelUser::class, $id);

                if (! $groupreluser)
                {
                    continue;
                }

                if (! $groupreluser->delete())
                {
                    $failures ++;
                }
                else
                {
                    Event::trigger(
                        'UnsubscribeUser',
                        Manager::context(),
                        array(
                            Change::PROPERTY_REFERENCE_ID => $groupreluser->get_group_id(),
                            Change::PROPERTY_TARGET_USER_ID => $groupreluser->get_user_id(),
                            Change::PROPERTY_USER_ID => $user->get_id()));
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedGroupRelUsersNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupRelUserDeleted';
                }
                else
                {
                    $message = 'SelectedGroupRelUsersDeleted';
                }
            }

            $this->redirect(
                Translation::get($message), (bool) $failures,
                array(
                    Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                    self::PARAM_GROUP_ID => $this->getRequest()->get(self::PARAM_GROUP_ID)));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectSelected', null, StringUtilities::LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS)),
                Translation::get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => Request::get(self::PARAM_GROUP_ID))),
                Translation::get('ViewerComponent')));
        $breadcrumbtrail->add_help('group general');
    }
}
