<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Renderer\PublicationList\Type\ContentObjectPublicationDetailsRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component
 */
class ViewerComponent extends Manager implements DelegateComponent, FeedbackSupport
{

    private $action_bar;

    private $html;

    private $feedback_allowed;

    private $publication;

    public function run()
    {
        // check if the content object has indeed been published for the user
        $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id());

        if (! $this->publication)
        {
            throw new ObjectNotExistException(Translation :: get('Publication'), $this->get_publication_id());
        }

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication))
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }

        $object = $this->publication->get_content_object();

        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation :: get('ToolViewerComponent', array('TITLE' => $object->get_title()))));

        $this->action_bar = $this->get_action_bar();
        $renderer = new ContentObjectPublicationDetailsRenderer($this);
        $this->html = $renderer->as_html();

        $course_settings_controller = CourseSettingsController :: get_instance();
        $this->feedback_allowed = $course_settings_controller->get_course_setting(
            $this->get_course_id(),
            CourseSettingsConnector :: ALLOW_FEEDBACK);

        if ($this->feedback_allowed)
        {
            $factory = new ApplicationFactory(
                $this->getRequest(),
                \Chamilo\Core\Repository\Feedback\Manager :: context(),
                $this->get_user(),
                $this);
            $result = $factory->run();
        }
        else
        {
            $result = '';
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $result;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = '<div id="action_bar_browser">';
        $html[] = $this->html;

        if ($this->feedback_allowed)
        {
            $html[] = '<div id="publication_feedback">';
            $html[] = '<h4>' . Translation :: get('Feedbacks') . '</h4>';
        }

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html = array();

        if ($this->feedback_allowed)
        {
            $html[] = '</div>';
        }

        $html[] = '</div>';
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_publication_id()
    {
        return Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }

    public function get_publication_count()
    {
        return 1;
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action($this->get_access_details_toolbar_item($this));
        }

        if (method_exists($this->get_parent(), 'get_tool_actions'))
        {
            $action_bar->set_tool_actions($this->get_parent()->get_tool_actions());
        }

        return $action_bar;
    }

    public function add_actionbar_item($item)
    {
        $this->action_bar->add_common_action($item);
    }

    public function can_create_feedback()
    {
        return $this->feedback_allowed;
    }

    public function can_update_feedback($feedback)
    {
        return $this->feedback_allowed;
    }

    public function can_delete_feedback($feedback)
    {
        return $this->feedback_allowed;
    }

    public function set_action_bar($action_bar)
    {
        $this->action_bar = $action_bar;
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks()
    {
        $parameters = new DataClassRetrievesParameters(
            $this->get_feedback_conditions(),
            null,
            null,
            array(
                new OrderBy(
                    new PropertyConditionVariable(
                        \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: class_name(),
                        \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: PROPERTY_MODIFICATION_DATE))));

        return \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: class_name(),
            $parameters);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        $parameters = new DataClassCountParameters($this->get_feedback_conditions());
        return \Chamilo\Application\Weblcms\Storage\DataManager :: count(
            \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: class_name(),
            $parameters);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedback_id)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: class_name(),
            $feedback_id);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        $feedback = new \Chamilo\Application\Weblcms\Storage\DataClass\Feedback();
        $feedback->set_publication_id($this->publication->get_id());
        return $feedback;
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return $this->is_allowed(2, $this->publication);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return $this->is_allowed(3, $this->publication);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->get_user_id();
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->get_user_id();
    }

    private function get_feedback_conditions()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: class_name(),
                \Chamilo\Application\Weblcms\Storage\DataClass\Feedback :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->publication->get_id()));

        return new AndCondition($conditions);
    }
}
