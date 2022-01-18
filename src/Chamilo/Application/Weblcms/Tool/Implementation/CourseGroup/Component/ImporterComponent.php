<?php namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler\ImportGroupsFormHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler\QuickUsersSubscribeFormHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type\ImportGroupsFormType;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ImporterComponent extends Manager
{
    /**
     * Runs this component and displays its output.
     *
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws UserException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $courseGroup = $this->getCourseGroupFromRequest();

        $form = $this->getForm()->create(ImportGroupsFormType::class);

        $handler = $this->getFormHandler();
        $handler->setParentCourseGroup($courseGroup);
        $handler->setCourse($this->get_course());

        try
        {
            if ($handler->handle($form, $this->getRequest()))
            {
                $statuses = $handler->getImportGroupStatuses();

                return $this->getTwig()->render(
                    Manager::context() . ':GroupsImportStatus.html.twig',
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
                    Manager::context() . ':ImportGroups.html.twig',
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
     * @return ImportGroupsFormHandler
     */
    protected function getFormHandler()
    {
        return $this->getService(ImportGroupsFormHandler::class);
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters()
    {
        return [self::PARAM_COURSE_GROUP];
    }
}
