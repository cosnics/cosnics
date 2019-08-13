<?php

namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package group.lib.group_manager.component
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->getRequest()->getFromPostOrUrl(self::PARAM_GROUP_ID);

        $this->set_parameter(self::PARAM_GROUP_ID, $ids);

        $user = $this->getUser();

        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $trail = BreadcrumbTrail::getInstance();

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
            )
        );
        $trail->add(
            new Breadcrumb($redirect->getUrl(), $this->getTranslator()->trans('Administration', [], Manager::context()))
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::context(),
                \Chamilo\Core\Admin\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER,
                DynamicTabsRenderer::PARAM_SELECTED_TAB => ClassnameUtilities::getInstance()->getNamespaceId(
                    self::package()
                )
            )
        );
        $trail->add(
            new Breadcrumb($redirect->getUrl(), $this->getTranslator()->trans('Group', [], Manager::context()))
        );

        $trail->add(
            new Breadcrumb(
                $this->get_url(array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS)),
                $this->getTranslator()->trans('GroupList', [], Manager::context())
            )
        );

        $trail->add(
            new Breadcrumb($this->get_url(), $this->getTranslator()->trans('DeleteGroup', [], Manager::context()))
        );
        $trail->add_help('group general');

        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $group = $this->retrieve_group($id);

                try
                {
                    $this->getGroupService()->deleteGroup($group);

                    Event::trigger(
                        'Delete',
                        Manager::context(),
                        array(
                            \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_REFERENCE_ID => $group->getId(
                            ),
                            \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_USER_ID => $user->getId(
                            )
                        )
                    );
                }
                catch (\Exception $ex)
                {
                    $failures ++;
                    $this->getExceptionLogger()->logException($ex);
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = $this->getTranslator()->trans(
                        'ObjectNotDeleted',
                        array('OBJECT' => $this->getTranslator()->trans('SelectedGroup', [], Manager::context())),
                        Utilities::COMMON_LIBRARIES
                    );
                }
                else
                {
                    $message = $this->getTranslator()->trans(
                        'ObjectsNotDeleted',
                        array('OBJECT' => $this->getTranslator()->trans('SelectedGroups', [], Manager::context())),
                        Utilities::COMMON_LIBRARIES
                    );
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = $this->getTranslator()->trans(
                        'ObjectDeleted',
                        array('{OBJECT}' => $this->getTranslator()->trans('SelectedGroup', [], Manager::context())),
                        Utilities::COMMON_LIBRARIES
                    );
                }
                else
                {
                    $message = $this->getTranslator()->trans(
                        'ObjectsDeleted',
                        array('{OBJECT}' => $this->getTranslator()->trans('SelectedGroups', [], Manager::context())),
                        Utilities::COMMON_LIBRARIES
                    );
                }
            }

            $this->redirect(
                $message,
                ($failures ? true : false),
                array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS),
                array(self::PARAM_GROUP_ID)
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities($this->getTranslator()->trans('NoObjectsSelected', null, Utilities::COMMON_LIBRARIES))
            );
        }

        return null;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS)),
                $this->getTranslator()->trans('BrowserComponent', [], Manager::context())
            )
        );
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->getFromUrl(self::PARAM_GROUP_ID)
                    )
                ),
                $this->getTranslator()->trans('ViewerComponent', [], Manager::context())
            )
        );
        $breadcrumbtrail->add_help('group general');
    }
}
