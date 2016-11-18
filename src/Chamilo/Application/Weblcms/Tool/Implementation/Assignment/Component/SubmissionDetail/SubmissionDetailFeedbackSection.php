<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail;

use Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form allows a user to add quick feedback to a submission.
 * 
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionDetailFeedbackSection extends FormValidator
{

    /**
     * Caching variable for the submission tracker.
     * 
     * @var \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission The submission tracker
     */
    private $submission_tracker;

    /**
     * Caching variable of the main page that uses this section.
     * 
     * @var AssignmentToolSubmissionViewerComponent
     */
    private $main_page;

    /**
     * Fills the form with all the data.
     * 
     * @param $submission_tracker \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission
     * @param $url string The url of the main page
     */
    public function __construct($submission_tracker, $main_page, $url = '')
    {
        $this->main_page = $main_page;
        
        parent::__construct('submission_feedback', 'post', $url);
        $this->submission_tracker = $submission_tracker;
        
        $html_editor_options = array();
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        
        $this->add_html_editor(
            $this->submission_tracker->get_id(), 
            Translation::get('QuickFeedback'), 
            false, 
            $html_editor_options);
        
        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation::get('Reset'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the feedback of the submission.
     */
    public function set_feedback()
    {
        $this->create_tracker();
    }

    /**
     * Creates a new feedback tracker for the submission.
     * 
     * @return boolean
     */
    private function create_tracker()
    {
        $values = $this->exportValues();
        
        $success = false;
        
        $description = new Description();
        if (! empty($values[$this->submission_tracker->get_id()]) &&
             $values[$this->submission_tracker->get_id()] != "<br />")
        {
            // Sets the description data
            $description->set_title(
                Translation::get('Feedback') . ' ' . $this->submission_tracker->get_content_object()->get_title());
            $description->set_description($values[$this->submission_tracker->get_id()]);
            $description->set_owner_id($this->main_page->get_target_id());
            
            $description->create();
        }
        
        if ($description->get_id())
        {
            $arguments = array(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID => $this->submission_tracker->get_id(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_CREATED => time(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_MODIFIED => time(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_USER_ID => $this->main_page->get_user_id(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_CONTENT_OBJECT_ID => $description->get_id());
            Event::trigger('FeedbackSubmission', \Chamilo\Application\Weblcms\Manager::context(), $arguments);
            
            $success = true;
        }
        
        return $success;
    }
}
