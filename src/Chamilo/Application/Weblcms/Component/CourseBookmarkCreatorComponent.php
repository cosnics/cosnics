<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Repository\ContentObject\Bookmark\Form\BookmarkForm;
use Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass\Bookmark;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * Component to create a bookmark for a course
 */
class CourseBookmarkCreatorComponent extends Manager
{

    private $items = [];

    private $parents;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManagePersonalCourses');

        $course_id = Request::get(self::PARAM_COURSE);

        $content_object = new Bookmark();

        // set title
        $course = CourseDataManager::retrieve_course($course_id);
        $title = $course->get_title();
        $content_object->set_title($title);

        // set application
        $content_object->set_application(self::package());

        // set url
        $params_bookmark = [];
        $params_bookmark[Application::PARAM_CONTEXT] = Manager::CONTEXT;
        $params_bookmark[Application::PARAM_ACTION] = self::ACTION_VIEW_COURSE;
        $params_bookmark[self::PARAM_COURSE] = $course_id;

        $url_bookmark = $this->get_url($params_bookmark);
        $content_object->set_url($url_bookmark);

        $params_form = [];
        $params_form[Application::PARAM_CONTEXT] = Manager::CONTEXT;
        $params_form[self::PARAM_ACTION] = self::ACTION_CREATE_BOOKMARK;
        $params_form[self::PARAM_COURSE] = $course_id;

        $form = new BookmarkForm(
            BookmarkForm::TYPE_CREATE, $this->getCurrentWorkspace(), $content_object, '',
            FormValidator::FORM_METHOD_POST, $this->get_url($params_form), null, null, false
        );

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        if ($form->validate())
        {
            $values = $form->exportValues();
            // create bookmark
            $bookmark = new Bookmark();
            $bookmark->set_title($values[ContentObject::PROPERTY_TITLE]);
            $bookmark->set_description($values[ContentObject::PROPERTY_DESCRIPTION]);
            $bookmark->set_application($values[Bookmark::PROPERTY_APPLICATION]);
            $bookmark->set_url($values[Bookmark::PROPERTY_URL]);
            $bookmark->set_owner_id($this->get_user_id());

            $success = $bookmark->create();

            $html = [];

            $html[] = $this->render_header();

            if ($success)
            {
                $html[] = Translation::get('CreatingBookmarkSuccess');
                $html[] = '<script>window.close()</script>';
            }
            else
            {
                $html[] = Translation::get('CreatingBookmarkFailure');
            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {

            // set title
            $content_object->set_title($title);

            // set application
            $content_object->set_application(self::package());

            // set url
            $params_bookmark = [];
            $params_bookmark[Application::PARAM_CONTEXT] = Manager::CONTEXT;
            $params_bookmark[Application::PARAM_ACTION] = self::ACTION_VIEW_COURSE;
            $params_bookmark[self::PARAM_COURSE] = $course_id;

            $url_bookmark = $this->get_url($params_bookmark);
            $content_object->set_url($url_bookmark);

            $params_form = [];
            $params_form[Application::PARAM_CONTEXT] = Manager::CONTEXT;
            $params_form[self::PARAM_ACTION] = self::ACTION_CREATE_BOOKMARK;
            $params_form[self::PARAM_COURSE] = $course_id;

            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }
}