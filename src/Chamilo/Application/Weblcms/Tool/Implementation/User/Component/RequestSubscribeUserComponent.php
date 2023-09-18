<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Form\CourseRequestForm;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
class RequestSubscribeUserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $course = $this->get_course();
        $request = new CourseRequest();

        $course_management_rights = CourseManagementRights::getInstance();

        if (!$this->get_user()->isPlatformAdmin() && !$course_management_rights->is_allowed_management(
                CourseManagementRights::TEACHER_REQUEST_SUBSCRIBE_RIGHT, $this->get_course_id(),
                CourseManagementRights::TYPE_COURSE, $this->getRequest()->query->get(self::PARAM_OBJECTS)
            ))
        {
            throw new NotAllowedException();
        }

        $form = new CourseRequestForm(
            CourseRequestForm::TYPE_CREATE, $this->get_url(), $course, $this, $request, false,
            $this->getRequest()->query->get(self::PARAM_OBJECTS)
        );

        if ($form->validate())
        {
            $success_request = $form->create_request();

            $this->redirectWithMessage(
                Translation::get($success_request ? 'RequestSent' : 'RequestNotSent'), !$success_request,
                [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER]
            );
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER,
                        self::PARAM_TAB => $this->getRequest()->query->get(self::PARAM_TAB)
                    ]
                ), Translation::get('UserToolUnsubscribeBrowserComponent')
            )
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER_BROWSER
                    ]
                ), Translation::get('UserToolSubscribeBrowserComponent')
            )
        );
    }

    /**
     * Returns the available parameters for registration
     *
     * @return string[]
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_OBJECTS;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
