<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component;

use Chamilo\Application\Weblcms\Service\CourseSubscriptionService;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Handler\CreatePlatformGroupTeamFormHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type\CreatePlatformGroupTeamType;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type\PlatformGroupTeamType;
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
                    Manager::context() . ':PlatformGroupTeamForm.html.twig', [
                        'HEADER' => $this->render_header(),
                        'FOOTER' => $this->render_footer(),
                        'FORM' => $form->createView(),
                        'PLATFORM_GROUPS_JSON' => $this->getSerializer()->serialize(
                            $this->getDirectlySubscribedPlatformGroups(), 'json'
                        ),
                        'GET_GROUP_CHILDREN_URL' => GetGroupChildrenJSONComponent::getAjaxUrl(),
                        'TEAM_NAME_COURSE_METADATA' => $this->get_course()->get_title() . ' (' .
                            $this->get_course()->get_visual_code() . ')'
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
     * @return CreatePlatformGroupTeamFormHandler
     */
    protected function getFormHandler()
    {
        return $this->getService(CreatePlatformGroupTeamFormHandler::class);
    }
}
