<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Feedback management of the portfolio item or folder
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FeedbackComponent extends ItemComponent implements FeedbackSupport, FeedbackNotificationSupport
{

    /**
     * Executes this component
     */
    public function build()
    {
        if (! $this->get_parent()->is_allowed_to_view_feedback($this->get_current_node()) &&
             ! $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        $result = $this->getApplicationFactory()->getApplication(
            Manager::context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $result;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_STEP);
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
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        return $this->get_parent()->retrieve_portfolio_feedbacks($this->get_current_node(), $count, $offset);
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
     * Retrieves all the notifications
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<Notification>
     */
    public function retrieve_notifications()
    {
        return $this->get_application()->retrievePortfolioNotifications($this->get_current_node());
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