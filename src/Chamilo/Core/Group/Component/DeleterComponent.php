<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 */
class DeleterComponent extends Manager
{

    /**
     * @throws \Exception
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID);

        $this->set_parameter(self::PARAM_GROUP_ID, $ids);

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $trail = $this->getBreadcrumbTrail();

        $browseUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER
            ]
        );
        $trail->add(
            new Breadcrumb($browseUrl, $translator->trans('Administration', [], \Chamilo\Core\Admin\Manager::CONTEXT))
        );

        $browseTabUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_ADMIN_BROWSER,
                GenericTabsRenderer::PARAM_SELECTED_TAB => ClassnameUtilities::getInstance()->getNamespaceId(
                    Manager::CONTEXT
                )
            ]
        );
        $trail->add(new Breadcrumb($browseTabUrl, $translator->trans('Group', [], Manager::CONTEXT)));

        $trail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $translator->trans('GroupList', [], Manager::CONTEXT)
            )
        );

        $trail->add(new Breadcrumb($this->get_url(), $translator->trans('DeleteGroup', [], Manager::CONTEXT)));

        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = [$ids];
            }

            foreach ($ids as $id)
            {
                $group = $this->retrieve_group($id);

                if (!$this->getGroupService()->deleteGroup($group))
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = $translator->trans(
                        'ObjectNotDeleted', ['OBJECT' => $translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = $translator->trans(
                        'ObjectsNotDeleted', ['OBJECT' => $translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
            }
            elseif (count($ids) == 1)
            {
                $message = $translator->trans(
                    'ObjectDeleted', ['OBJECT' => $translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else
            {
                $message = $translator->trans(
                    'ObjectsDeleted', ['OBJECT' => $translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->redirectWithMessage(
                $message, (bool) $failures, [Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS],
                [self::PARAM_GROUP_ID]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities($translator->trans('NoObjectsSelected', [], StringUtilities::LIBRARIES))
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $translator = $this->getTranslator();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $translator->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );
    }
}
