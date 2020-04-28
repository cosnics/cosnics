<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Handler\SubscribePlatformGroupUsersFormHandler;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type\SubscribePlatformGroupUsersFormType;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Core\Group\Ajax\Component\GetGroupChildrenJSONComponent;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Class SubscribePlatformGroupUsersComponent
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class SubscribePlatformGroupUsersComponent extends Manager
{
    /**
     * @return string|void
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $courseGroup = $this->getCourseGroupFromRequest();

        $form = $this->getForm()->create(SubscribePlatformGroupUsersFormType::class);

        $handler = $this->getFormHandler();
        $handler->setCourseGroup($courseGroup);

        try
        {
            if ($handler->handle($form, $this->getRequest()))
            {
                $message = 'PlatformGroupUsersSubscribed';
                $success = true;
            }
            else
            {
                return $this->getTwig()->render(
                    Manager::context() . ':SubscribePlatformGroupUsers.html.twig',
                    [
                        'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                        'PLATFORM_GROUPS_JSON' => $this->getSerializer()->serialize(
                            $this->getDirectlySubscribedPlatformGroups(), 'json'
                        ),
                        'GET_GROUP_CHILDREN_URL' => GetGroupChildrenJSONComponent::getAjaxUrl(),
                        'FORM' => $form->createView(),
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
            $message = 'PlatformGroupUsersNotSubscribed';
            $success = false;
            $this->getExceptionLogger()->logException($ex);
        }

        $this->redirect(
            $this->getTranslator()->trans(
                $message, [], \Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager::context()
            ), !$success,
            [self::PARAM_ACTION => self::ACTION_GROUP_DETAILS]
        );

        return null;
    }

    /**
     * @return SubscribePlatformGroupUsersFormHandler
     */
    protected function getFormHandler()
    {
        return $this->getService(SubscribePlatformGroupUsersFormHandler::class);
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
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [self::PARAM_ACTION => self::ACTION_BROWSE],
                    [self::PARAM_COURSE_GROUP]
                ),
                $this->getTranslator()->trans('DetailsComponent', [], Manager::context())
            )
        );

        $currentGroup = $this->getCourseGroupFromRequest();
        $availableGroups = [];
        while(!$currentGroup->is_root())
        {
            array_unshift($availableGroups, $currentGroup);
            $currentGroup = $currentGroup->get_parent();
        }

        foreach($availableGroups as $currentGroup)
        {
            $breadcrumbtrail->add(
                new Breadcrumb(
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_GROUP_DETAILS,
                            self::PARAM_COURSE_GROUP => $currentGroup->getId()
                        ]
                    ),
                    $currentGroup->get_name()
                )
            );
        }
    }
}
