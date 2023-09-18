<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Renderer\PublicationList\Type\ContentObjectPublicationDetailsRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\Feedback;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package repository.lib.complex_display.assessment.component
 */
class ViewerComponent extends Manager implements BreadcrumbLessComponentInterface, FeedbackSupport
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $feedback_allowed;

    private $html;

    private $publication;

    public function run()
    {
        // check if the content object has indeed been published for the user
        $this->publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->get_publication_id()
        );

        if (!$this->publication)
        {
            throw new ObjectNotExistException(Translation::get('Publication'), $this->get_publication_id());
        }

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            throw new NotAllowedException();
        }

        $this->getCategoryBreadcrumbsGenerator()->generateBreadcrumbsForContentObjectPublication(
            $this->getBreadcrumbTrail(), $this, $this->publication
        );

        $object = $this->publication->get_content_object();

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->get_url(), Translation::get('ToolViewerComponent', ['TITLE' => $object->get_title()])
            )
        );

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $renderer = new ContentObjectPublicationDetailsRenderer($this);
        $this->html = $renderer->as_html();

        $course_settings_controller = CourseSettingsController::getInstance();
        $this->feedback_allowed = $course_settings_controller->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_FEEDBACK
        );

        if ($this->feedback_allowed)
        {
            $result = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Feedback\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }
        else
        {
            $result = '';
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $result;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function count_feedbacks()
    {
        $parameters = new DataClassCountParameters($this->get_feedback_conditions());

        return DataManager::count(
            Feedback::class, $parameters
        );
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $commonActions->addButton($this->get_access_details_toolbar_item($this));
            }

            if (method_exists($this->get_parent(), 'get_tool_actions'))
            {
                $toolActions->setButtons($this->get_parent()->get_tool_actions());
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_feedback()
    {
        $feedback = new Feedback();
        $feedback->set_publication_id($this->publication->get_id());

        return $feedback;
    }

    private function get_feedback_conditions()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Feedback::class, Feedback::PROPERTY_PUBLICATION_ID
            ), new StaticConditionVariable($this->publication->get_id())
        );

        return new AndCondition($conditions);
    }

    public function get_publication_count()
    {
        return 1;
    }

    public function get_publication_id()
    {
        return $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::count_feedbacks()
     */

    /**
     * @return bool
     */
    public function hasCategories()
    {
        return false;
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::retrieve_feedback()
     */

    public function is_allowed_to_create_feedback()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::get_feedback()
     */

    public function is_allowed_to_delete_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->get_user_id();
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */

    public function is_allowed_to_update_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->get_user_id();
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */

    public function is_allowed_to_view_feedback()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */

    public function render_footer(): string
    {
        $html = [];

        if ($this->feedback_allowed)
        {
            $html[] = '</div>';
        }

        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    /*
     * (non-PHPdoc) @see \core\repository\feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */

    public function render_header(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->html;

        if ($this->feedback_allowed)
        {
            $html[] = '<div id="publication_feedback">';
            $html[] = '<h4>' . Translation::get('Feedbacks') . '</h4>';
        }

        return implode(PHP_EOL, $html);
    }

    public function retrieve_feedback($feedback_id)
    {
        return DataManager::retrieve_by_id(
            Feedback::class, $feedback_id
        );
    }

    /**
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->get_feedback_conditions(), $count, $offset, new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable(
                        Feedback::class, Feedback::PROPERTY_MODIFICATION_DATE
                    )
                )
            ])
        );

        return DataManager::retrieves(
            Feedback::class, $parameters
        );
    }
}
