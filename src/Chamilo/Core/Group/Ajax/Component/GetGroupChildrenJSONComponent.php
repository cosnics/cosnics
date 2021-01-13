<?php

namespace Chamilo\Core\Group\Ajax\Component;

use Chamilo\Core\Group\Ajax\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Group\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetGroupChildrenJSONComponent extends Manager
{
    const PARAM_GROUP_ID = 'groupId';

    /**
     *
     * @return string
     */
    function run()
    {
        try
        {
            $groupId = $this->getRequest()->getFromPost(self::PARAM_GROUP_ID);
            $group = $this->getGroupService()->getGroupByIdentifier($groupId);
            if (empty($group))
            {
                throw new \RuntimeException('Could not find the group with id %s' . $groupId);
            }

            $groupData = [];

            $children = $this->getGroupService()->findDirectChildrenFromGroup($group);
            foreach($children as $child)
            {
                $groupData[] = [
                    'id' => $child->getId(),
                    'name' => $child->get_name(),
                    'code' => $child->get_code(),
                    'has_children' => $child->has_children()
                ];
            }

            return new JsonResponse($groupData);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            return new JsonResponse(['error' => $ex->getMessage(), 500]);
        }
    }

    /**
     * @return GroupService
     */
    protected function getGroupService()
    {
        return $this->getService(GroupService::class);
    }

    /**
     * @return string
     */
    public static function getAjaxUrl()
    {
        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::context(),
                self::PARAM_ACTION => self::ACTION_GET_GROUP_CHILDREN_JSON
            ]
        );

        return $redirect->getUrl();
    }
}