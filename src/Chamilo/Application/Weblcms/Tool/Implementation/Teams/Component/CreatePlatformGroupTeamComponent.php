<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Service\CourseSubscriptionService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Handler\CreatePlatformGroupTeamFormHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type\CreatePlatformGroupTeamType;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Core\Group\Ajax\Component\GetGroupChildrenJSONComponent;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatePlatformGroupTeamComponent extends Manager
{
    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        if (!$this->get_course()->is_course_admin($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $form = $this->getForm()->create(CreatePlatformGroupTeamType::class);

        $handler = $this->getFormHandler();
        $handler->setUser($this->getUser());
        $handler->setCourse($this->get_course());

        try
        {
            if ($handler->handle($form, $this->getRequest()))
            {
                $message = 'PlatformGroupTeamCreated';
                $success = true;
            }
            else
            {
                return $this->getTwig()->render(
                    Manager::context() . ':CreatePlatformGroupTeam.html.twig', [
                        'HEADER' => $this->render_header(),
                        'FOOTER' => $this->render_footer(),
                        'FORM' => $form->createView(),
                        'PLATFORM_GROUPS_JSON' => $this->getSerializer()->serialize(
                            $this->getDirectlySubscribedPlatformGroups(), 'json'
                        ),
                        'GET_GROUP_CHILDREN_URL' => GetGroupChildrenJSONComponent::getAjaxUrl()
                    ]
                );
            }
        }
        catch (UserException $ex)
        {
            throw $ex;
        }
        catch (\Exception $ex)
        {
            $message = 'PlatformGroupTeamNotCreated';
            $success = false;
            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()), !$success,
            [self::PARAM_ACTION => self::ACTION_BROWSE]
        );

        return null;
    }

    /**
     * @return array
     */
    protected function getDirectlySubscribedPlatformGroups()
    {
        $groups = $this->getCourseSubscriptionService()->findGroupsDirectlySubscribedToCourse($this->get_course())
            ->getArrayCopy();

        foreach($groups as $index => $group)
        {
            $leftValue = $group[Group::PROPERTY_LEFT_VALUE];
            $rightValue = $group[Group::PROPERTY_RIGHT_VALUE];

            $hasChildren = $leftValue != ($rightValue - 1);
            $groups[$index]['has_children'] = $hasChildren;
        }

        return $groups;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Service\CourseSubscriptionService
     */
    protected function getCourseSubscriptionService()
    {
        return $this->getService(CourseSubscriptionService::class);
    }

    /**
     * @return CreatePlatformGroupTeamFormHandler
     */
    protected function getFormHandler()
    {
        return $this->getService(CreatePlatformGroupTeamFormHandler::class);
    }
}