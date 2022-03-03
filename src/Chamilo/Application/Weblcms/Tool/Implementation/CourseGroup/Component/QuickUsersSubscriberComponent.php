<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain\QuickUserSubscriberStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler\QuickUsersSubscribeFormHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type\QuickUsersSubscribeFormType;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class QuickUsersSubscriberComponent extends Manager
{
    /**
     * @return string|null
     * @throws NotAllowedException
     * @throws UserException
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $courseGroup = $this->getCourseGroupFromRequest();

        $form = $this->getForm()->create(QuickUsersSubscribeFormType::class);

        $handler = $this->getFormHandler();
        $handler->setCourseGroup($courseGroup);
        $handler->setCourse($this->get_course());

        try
        {
            if ($handler->handle($form, $this->getRequest()))
            {
                $statuses = $handler->getStatuses();

                return $this->getTwig()->render(
                    Manager::context() . ':UserSubscriberStatus.html.twig',
                    [
                        'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                        'SUBSCRIBE_STATUSES' => $statuses,
                        'DETAILS_URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_GROUP_DETAILS])
                    ]
                );
            }
            else
            {
                return $this->getTwig()->render(
                    Manager::context() . ':QuickUsersSubscriber.html.twig',
                    [
                        'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                        'FORM' => $form->createView(), 'COURSE_GROUP_NAME' => $courseGroup->get_name()
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
            $this->getExceptionLogger()->logException($ex);
        }

        return null;
    }

    /**
     * @return QuickUsersSubscribeFormHandler
     */
    protected function getFormHandler()
    {
        return $this->getService(QuickUsersSubscribeFormHandler::class);
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters()
    {
        return [self::PARAM_COURSE_GROUP];
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addGroupDetailsBreadcrumbs($breadcrumbtrail);
    }
}
