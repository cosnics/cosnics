<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse;

use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;

/**
 * Subapplication for the management of open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browse';
    public const ACTION_CREATE = 'Create';
    public const ACTION_DELETE = 'Delete';
    public const ACTION_UPDATE = 'Update';

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'open_course_action';
    public const PARAM_COURSE_ID = 'course_id';

    /**
     * Returns the course ids from the request
     *
     * @return int[]
     *
     * @throws NoObjectSelectedException
     */
    public function getCourseIdsFromRequest()
    {
        $courseIds = $this->getRequest()->get(self::PARAM_COURSE_ID);
        if (empty($courseIds))
        {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation('Course', null, self::context())
            );
        }

        if (!is_array($courseIds))
        {
            $courseIds = array($courseIds);
        }

        return $courseIds;
    }

    /**
     * Returns the url to the create component
     *
     * @return string
     */
    public function getCreateOpenCourseUrl()
    {
        $parameters = array(self::PARAM_ACTION => self::ACTION_CREATE);

        return $this->get_url($parameters);
    }

    /**
     * Returns the url to the delete component
     *
     * @param int $courseId
     *
     * @return string
     */
    public function getDeleteOpenCourseUrl($courseId)
    {
        $parameters = array(self::PARAM_ACTION => self::ACTION_DELETE, self::PARAM_COURSE_ID => $courseId);

        return $this->get_url($parameters);
    }

    /**
     *
     * @return OpenCourseService
     */
    public function getOpenCourseService()
    {
        return $this->getService(OpenCourseService::class);
    }

    /**
     * Returns the url to the update component
     *
     * @param int $courseId
     *
     * @return string
     */
    public function getUpdateOpenCourseUrl($courseId)
    {
        $parameters = array(self::PARAM_ACTION => self::ACTION_UPDATE, self::PARAM_COURSE_ID => $courseId);

        return $this->get_url($parameters);
    }

    /**
     * Returns the url to the homepage of the course
     *
     * @param int $courseId
     *
     * @return string
     */
    public function getViewCourseUrl($courseId)
    {
        $parameters = array(
            Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $courseId
        );

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }
}
