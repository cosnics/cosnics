<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php This tool allows a user to publish assignments in his or her course
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable
{
    // Browse actions
    const ACTION_BROWSE_SUBMISSIONS = 'submissions_browser';
    const ACTION_BROWSE_SUBMITTERS = 'submitters_browser';
    const ACTION_STUDENT_BROWSE_SUBMISSIONS = 'student_submissions_browser';
    // Submission actions
    const ACTION_DELETE_SUBMISSION = 'submission_deleter';
    const ACTION_DOWNLOAD_SUBMISSIONS = 'submissions_downloader';
    const ACTION_SUBMIT_SUBMISSION = 'submission_submit';
    const ACTION_VIEW_SUBMISSION = 'submission_viewer';
    // Feedback actions
    const ACTION_DELETE_FEEDBACK = 'feedback_deleter';
    const ACTION_EDIT_FEEDBACK = 'feedback_updater';
    const ACTION_GIVE_FEEDBACK = 'submission_feedback';
    // Parameters
    const PARAM_ATTACHMENT_TYPE = 'attachment_type';
    const PARAM_FEEDBACK_ID = 'feedback_id';
    const PARAM_SUBMISSION = 'submission';
    const PARAM_SUBMITTER_TYPE = 'submitter_type';
    const PARAM_TARGET_ID = 'target_id';
    const PARAM_TYPE = 'type';
    // Properties
    const PROPERTY_DATE_SUBMITTED = 'DateSubmitted';
    const PROPERTY_DESCRIPTION = 'Description';
    const PROPERTY_FIRST_SUBMISSION = 'FirstSubmissionDate';
    const PROPERTY_GROUP_MEMBERS = 'GroupMembers';
    const PROPERTY_LAST_SUBMISSION = 'LastSubmissionDate';
    const PROPERTY_NAME = 'Name';
    const PROPERTY_NUMBER_OF_SUBMISSIONS = 'NumberOfSubmissions';
    const PROPERTY_NUMBER_OF_FEEDBACKS = 'NumberOfFeedbacks';
    const PROPERTY_SCORE = 'Score';
    const PROPERTY_TITLE = 'Title';

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(Assignment :: CLASS_NAME);
    }

    /**
     * Adds extra actions to the toolbar in different components
     *
     * @param $toolbar Toolbar
     * @param $publication Publication
     * @return Toolbar
     */
    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('BrowseSubmitters'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_BROWSE_SUBMITTERS,
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID])),
                ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('SubmissionSubmit'),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SUBMIT_SUBMISSION,
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                        self :: PARAM_TARGET_ID => $this->get_user_id(),
                        self :: PARAM_SUBMITTER_TYPE => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)),
                ToolbarItem :: DISPLAY_ICON));
        return $toolbar;
    }
}
