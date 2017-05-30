<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionDetail;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionViewerComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Contains all the general info from a submission and can render this on another page.
 *
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmissionDetailGeneralInfoSection
{

    /**
     * The main page where this section will be rendered.
     *
     * @var SubmissionViewerComponent
     */
    private $main_page;

    /**
     * Assigns the given submission details main page to the caching variable.
     *
     * @param $main_page SubmissionViewerComponent The main page
     */
    public function __construct($main_page)
    {
        $this->main_page = $main_page;
    }

    /**
     * Renders the section.
     */
    public function render_section()
    {
        return $this->main_page->set_section_content('General', $this->get_section_content());
    }

    /**
     * Returns the contents of this section as an array of strings.
     *
     * @return array The content as html code
     */
    private function get_section_content()
    {
        $html = array();

        // Date
        $html[] = $this->get_date_submitted_html();

        // Description
        $html[] = $this->get_description_html();

        // Submitter
        if ($this->main_page->get_assignment()->get_allow_group_submissions())
        {
            $html[] = $this->get_submitter_html();
        }

        // Group members
        if ($this->main_page->get_submitter_type() !=
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER
        )
        {
            $html[] = $this->get_group_members_html();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the date the submission was submitted as an array of strings of html code.
     *
     * @return array The date
     */
    private function get_date_submitted_html()
    {
        $date_submitted = $this->main_page->get_submission_tracker()->get_date_submitted();
        $html = array();

        $html[] = '<div style="font-weight:bold; float:left">';
        $html[] = Translation::get('DateSubmitted') . ':&nbsp;<br />';
        $html[] = '</div>';

        // Content
        $html[] = '<div style="float:left">';
        $html[] = $this->format_date($date_submitted) . '<br />';
        $html[] = '</div><br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the description as an array of strings.
     *
     * @return array The description as html code
     */
    private function get_description_html()
    {
        $html = array();

        // Title
        $html[] = '<div>';
        $html[] = '<h4>' . Translation::get('Description') . ':&nbsp;</h4>';
        $html[] = '</div>';

        // Content
        $html[] = '<div>';

        $submission = $this->main_page->get_submission();

        if ($submission)
        {
            $html[] = '<div class="description" style="overflow: auto;">';

            $rendition_implementation = ContentObjectRenditionImplementation::factory(
                $submission,
                ContentObjectRendition::FORMAT_HTML,
                ContentObjectRendition::VIEW_DESCRIPTION,
                $this
            );

            $html[] = $rendition_implementation->render();
        }
        else
        {
            $html[] = '<div class="warning-message">' . Translation::get('ContentObjectUnknownMessage') . '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the submitter info as an array of strings.
     *
     * @return array The submitter info as html code
     */
    private function get_submitter_html()
    {
        $html = array();

        // Title
        $html[] = '<div style="font-weight:bold; float:left">';
        $html[] = Translation::get('Submitter') . ':&nbsp;<br />';
        $html[] = '</div>';

        // Content
        $html[] = '<div style="float:left">';
        $html[] = $this->get_submitter() . '<br />';
        $html[] = '</div><br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the group members as an array of strings.
     *
     * @return array The group members as html code
     */
    private function get_group_members_html()
    {
        $html = array();

        // Title
        $html[] = '<div style="font-weight:bold; float:left">';
        $html[] = Translation::get('GroupMembers') . ':&nbsp;<br />';
        $html[] = '</div>';

        // Content
        $html[] = '<div style="float:left">';
        $html[] = $this->get_group_members() . '<br />';
        $html[] = '</div><br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the name of the submitter of the submission, followed by the group.
     *
     * @return string The submitter name and group
     */
    private function get_submitter()
    {
        $user_name = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            $this->main_page->get_submission_tracker()->get_user_id()
        );

        switch ($this->main_page->get_submitter_type())
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                return $this->main_page->get_submitter_name();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return $user_name->get_fullname() . ' - ' . $this->main_page->get_submitter_name();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return $user_name->get_fullname() . ' - ' . $this->main_page->get_submitter_name();
        }
    }

    /**
     * Returns a string of all the group members separated by commas.
     *
     * @return string The group members
     */
    private function get_group_members()
    {
        if ($this->main_page->get_submitter_type() ==
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP
        )
        {
            $group_members =
                CourseGroupDataManager::retrieve_course_group_users($this->main_page->get_target_id())->as_array();
        }
        else
        {
            $group_members = \Chamilo\Core\User\Storage\DataManager::retrieves(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                new DataClassRetrievesParameters(
                    new InCondition(
                        new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                        \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                            Group::class_name(),
                            $this->main_page->get_target_id()
                        )->get_users()
                    )
                )
            )->as_array();
        }

        $group_member_names = array();

        foreach ($group_members as $member)
        {
            $group_member_names[$member->get_lastname()] = $member->get_fullname();
        }

        ksort($group_member_names);

        return implode(", ", $group_member_names);
    }

    /**
     * Formats a date.
     *
     * @param $date type the date to be formatted.
     *
     * @return the formatted representation of the date.
     */
    private function format_date($date_submitted)
    {
        $formatted_date = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $date_submitted
        );

        if ($date_submitted > $this->main_page->get_assignment()->get_end_time())
        {
            return '<span style="color:red">' . $formatted_date . ' (' . $this->get_time_late($date_submitted) .
            ')</span>';
        }

        return $formatted_date;
    }

    /**
     * Returns the time the submission was too late.
     *
     * @param $date_submitted time
     *
     * @return array The time late
     */
    private function get_time_late($date_submitted)
    {
        $time_late = $date_submitted - $this->main_page->get_assignment()->get_end_time();

        $seconds_late = $time_late % 60;
        $time_late /= 60;
        $minutes_late = $time_late % 60;
        $time_late /= 60;
        $hours_late = $time_late % 24;
        $time_late /= 24;
        $days_late = (int) $time_late;

        $output = array();

        if ($days_late != 0)
        {
            $output[] = $days_late . ' ' . Translation::get('Days');
        }
        if ($hours_late != 0 || count($output) > 0)
        {
            $output[] = $hours_late . ' ' . Translation::get('Hours');
        }
        if ($minutes_late != 0 || count($output) > 0)
        {
            $output[] = $minutes_late . ' ' . Translation::get('Minutes');
        }
        if ($days_late == 0 && $hours_late == 0 && $minutes_late == 0)
        {
            $output[] = $seconds_late . ' ' . Translation::get('Seconds');
        }
        $output[] = ' ' . Translation::get('Late');

        return implode(" ", $output);
    }

    /**
     * @param ContentObject $attachment
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        $parameters = array(
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW_ATTACHMENT,
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_OBJECT_ID => $attachment->get_id()
        );

        return $this->main_page->get_url($parameters);
    }
}
