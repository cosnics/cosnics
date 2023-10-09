<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\UserCourseGroups;
use Chamilo\Core\User\Service\UserDetails\UserDetailsRenderer;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\User\Component
 */
class DetailsComponent extends Manager
{

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();

        if ($this->getRequest()->query->has(\Chamilo\Application\Weblcms\Manager::PARAM_USERS))
        {
            $user = $this->getUserService()->findUserByIdentifier(
                $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS)
            );

            $course_groups = new UserCourseGroups($user->getId(), $this->get_course_id());

            $html[] = $this->getUserDetailsRenderer()->renderUserDetails($user, $this->getUser());
            $html[] = $this->getUserGroupsDetailsRenderer()->renderUserDetails($user, $this->getUser());
            $html[] = $course_groups->toHtml();
        }

        $requestUserIdentifiers = $this->getRequest()->request->get('user_id');

        if ($requestUserIdentifiers)
        {
            foreach ($requestUserIdentifiers as $user_id)
            {
                $user = $this->getUserService()->findUserByIdentifier($user_id);
                $course_groups = new UserCourseGroups($user->getId(), $this->get_course_id());

                $html[] = $this->getUserDetailsRenderer()->renderUserDetails($user, $this->getUser());
                $html[] = $this->getUserGroupsDetailsRenderer()->renderUserDetails($user, $this->getUser());
                $html[] = $course_groups->toHtml();
            }
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_USERS;
        $additionalParameters[] = self::PARAM_TAB;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getUserDetailsRenderer(): UserDetailsRenderer
    {
        return $this->getService(UserDetailsRenderer::class);
    }

    public function getUserGroupsDetailsRenderer(): \Chamilo\Core\Group\Service\UserDetails\UserDetailsRenderer
    {
        return $this->getService(\Chamilo\Core\Group\Service\UserDetails\UserDetailsRenderer::class);
    }
}
