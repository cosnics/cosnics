<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 */
class DeleterComponent extends Manager
{

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function run(): Response
    {
        $translator = $this->getTranslator();
        $groupService = $this->getGroupService();
        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP_ID);

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getRequest()->query->get(self::PARAM_GROUP_ID)
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );

        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = [$ids];
            }

            foreach ($ids as $id)
            {
                $group = $groupService->findGroupByIdentifier($id);

                if (!$groupService->deleteGroup($group))
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

            return $this->redirectWithMessage(
                $message, (bool) $failures, [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS
                ]
            );
        }
        else
        {
            return new Response(
                $this->display_error_page(
                    htmlentities($translator->trans('NoObjectsSelected', [], StringUtilities::LIBRARIES))
                )
            );
        }
    }
}
