<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;

/**
 * Default viewer component that handles the visualization of the portfolio item or folder
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends TabComponent implements FeedbackSupport, FeedbackNotificationSupport
{

    /**
     * Executes this component
     */
    public function build()
    {
        if (! $this->get_parent()->is_allowed_to_view_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        $content = array();
        $content[] = ContentObjectRenditionImplementation :: launch(
            $this->get_current_content_object(),
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);

        if ($this->get_current_node()->is_root())
        {
            $content[] = $this->render_last_actions();
            $content[] = $this->renderFeedback();
        }
        else
        {
            $content[] = $this->renderFeedback();
            $content[] = $this->render_statistics($this->get_current_content_object());
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = implode(PHP_EOL, $content);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function renderFeedback()
    {
        if ($this->get_parent()->is_allowed_to_view_feedback($this->get_current_node()) ||
             $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node()))
        {

            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Feedback\Manager :: context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
    }

    /**
     * Render the basic statistics for the portfolio item / folder
     *
     * @param \core\repository\ContentObject $content_object
     * @return string
     */
    public function render_statistics($content_object)
    {
        $html = array();

        $html[] = '<div class="portfolio-statistics">';

        if ($this->get_user_id() == $content_object->get_owner_id())
        {
            $html[] = Translation :: get(
                'CreatedOn',
                array('DATE' => $this->format_date($content_object->get_creation_date())));
        }
        else
        {
            $html[] = Translation :: get(
                'CreatedOnBy',
                array(
                    'DATE' => $this->format_date($content_object->get_creation_date()),
                    'USER' => $content_object->get_owner()->get_fullname()));
        }

        if ($content_object->get_creation_date() != $content_object->get_modification_date())
        {
            $html[] = '<br />';
            $html[] = Translation :: get(
                'LastModifiedOn',
                array('DATE' => $this->format_date($content_object->get_modification_date())));
        }

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Formats the given date in a human-readable format.
     *
     * @param $date int A UNIX timestamp.
     * @return string The formatted date.
     */
    public function format_date($date)
    {
        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        return DatetimeUtilities :: format_locale_date($date_format, $date);
    }

    /**
     * Render the last actions undertaken by the user in the portfolio
     *
     * @return string
     */
    public function render_last_actions()
    {
        $html = array();

        $last_activities = \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager :: retrieve_activities(
            $this->get_current_content_object(),
            null,
            0,
            1,
            array(new OrderBy(new PropertyConditionVariable(Activity :: class_name(), Activity :: PROPERTY_DATE))));

        $last_activity = $last_activities->next_result();

        if ($last_activity)
        {
            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-body">';

            $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);

            $html[] = Translation :: get(
                'LastEditedOn',
                array('DATE' => DatetimeUtilities :: format_locale_date($date_format, $last_activity->get_date())));

            $html[] = '<br />';

            $html[] = Translation :: get(
                'LastAction',
                array('ACTION' => $last_activity->get_type_string(), 'CONTENT' => $last_activity->get_content()));

            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return $this->get_parent()->count_portfolio_feedbacks($this->get_current_node());
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks()
    {
        return $this->get_parent()->retrieve_portfolio_feedbacks($this->get_current_node());
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedback_id)
    {
        return $this->get_parent()->retrieve_portfolio_feedback($feedback_id);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        $feedback = $this->get_parent()->get_portfolio_feedback();
        $feedback->set_complex_content_object_id($this->get_current_complex_content_object_item()->get_id());
        return $feedback;
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return $this->get_parent()->is_allowed_to_view_feedback($this->get_current_node());
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node());
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $this->get_parent()->is_allowed_to_update_feedback($feedback);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $this->get_parent()->is_allowed_to_delete_feedback($feedback);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::retrieve_notification()
     */
    public function retrieve_notification()
    {
        return $this->get_parent()->retrieve_portfolio_notification($this->get_current_node());
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::get_notification()
     */
    public function get_notification()
    {
        $notification = $this->get_parent()->get_portfolio_notification();
        $notification->set_complex_content_object_id($this->get_current_complex_content_object_item()->get_id());
        return $notification;
    }
}
