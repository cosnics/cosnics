<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display;

use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * Enter description here .
 * ..
 *
 * @author Renaat De Muynck
 * @method PeerAssessmentDisplaySupport get_parent()
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_PUBLICATION = \Chamilo\Core\Repository\Manager :: PARAM_PUBLICATION_ID;
    const PARAM_ATTEMPT = 'attempt';
    const PARAM_GROUP = 'group';
    const PARAM_INDICATOR = 'indicator';
    const PARAM_USER = 'user';
    const PARAM_GROUP_USERS = 'group_users';
    const PARAM_EXPORT_TYPE = 'export_type';
    const ACTION_TAKE_PEER_ASSESSMENT = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
    const ACTION_CLOSE_USER_ATTEMPT = 'UserAttemptCloser';
    const ACTION_TOGGLE_CLOSE_USER_ATTEMPT = 'UserAttemptClosedToggler';
    const ACTION_CLOSE_ATTEMPT = 'AttemptCloser';
    const ACTION_OPEN_USER_ATTEMPT = 'UserAttemptOpener';
    const ACTION_VIEW_USER_ATTEMPT_STATUS = 'UserAttemptStatusViewer';
    const ACTION_VIEW_USER_STATUS = 'UserStatusViewer';
    const ACTION_VIEW_USER_RESULTS = 'UserResultsViewer';
    const ACTION_VIEW_USER_REPORT = 'UserReportViewer';
    const ACTION_VIEW_ATTEMPT = 'AttemptViewer';
    const ACTION_TOGGLE_ATTEMPT_VISIBILITY = 'AttemptVisibilityToggler';
    const ACTION_BROWSE_DIRECT_GROUP_SUBSCRIBE = 'DirectGroupSubscribeBrowser';
    const ACTION_SUBSCRIBE_USER = 'UserGroupSubscriber';
    const ACTION_UNSUBSCRIBE_USER = 'UserGroupUnsubscriber';
    const ACTION_OVERVIEW_STATUS = 'StatusViewer';
    const ACTION_OVERVIEW_RESULTS = 'ResultsViewer';
    const ACTION_EXPORT_USER_RESULT = 'UserResultExporter';
    const ACTION_EXPORT_RESULT = 'ResultExporter';
    const EXPORT_TYPE_EXCEL = 'excel';
    const EXPORT_TYPE_CSV = 'csv';

    /**
     *
     * @todo move to proper place
     */
    const VIEW_RIGHT = 1;
    const EDIT_RIGHT = 2;
    const DEFAULT_ACTION = self :: ACTION_VIEW_USER_ATTEMPT_STATUS;

    private $action_bar;

    // region PeerAssessmentDisplaySupport;
    public function get_settings($publication_id)
    {
        return $this->get_parent()->get_settings($publication_id);
    }

    public function get_group($id)
    {
        return $this->get_parent()->get_group($id);
    }

    public function get_groups($publication_id)
    {
        return $this->get_parent()->get_groups($publication_id);
    }

    public function add_user_to_group($user_id, $group_id)
    {
        return $this->get_parent()->add_user_to_group($user_id, $group_id);
    }

    public function remove_user_from_group($user_id, $group_id)
    {
        return $this->get_parent()->remove_user_from_group($user_id, $group_id);
    }

    /**
     * Get the groups in which the current user is subscribed
     *
     * @param integer $user_id
     * @return array The groups
     * @deprecated use get_user_group() instead
     */
    public function get_user_groups($user_id)
    {
        $this->display_warning_message('Deprecated, use get_user_group() instead');

        return array($this->get_parent()->get_user_group($user_id));
    }

    public function get_user_group($user_id = null)
    {
        return $this->get_parent()->get_user_group($user_id);
    }

    public function get_group_users($group_id)
    {
        return $this->get_parent()->get_group_users($group_id);
    }

    public function count_group_users($group_id)
    {
        return $this->get_parent()->count_group_users($group_id);
    }

    public function get_attempt($id = null)
    {
        return $this->get_parent()->get_attempt($id);
    }

    public function get_attempts($publication_id)
    {
        return $this->get_parent()->get_attempts($publication_id);
    }

    public function close_attempt($id)
    {
        return $this->get_parent()->close_attempt($id);
    }

    public function toggle_attempt_visibility($id)
    {
        return $this->get_parent()->toggle_attempt_visibility($id);
    }

    public function get_user_attempt_status($user_id, $attempt_id)
    {
        return $this->get_parent()->get_user_attempt_status($user_id, $attempt_id);
    }

    public function close_user_attempt($user_id, $attempt_id)
    {
        return $this->get_parent()->close_user_attempt($user_id, $attempt_id);
    }

    public function open_user_attempt($user_id, $attempt_id)
    {
        return $this->get_parent()->open_user_attempt($user_id, $attempt_id);
    }

    /**
     * Toggles a user's closed status
     *
     * @param int $user_id
     * @param int $attempt_id
     * @return bool Returns true if closed, false otherwise
     */
    public function toggle_attempt_status_close($user_id, $attempt_id)
    {
        $status = $this->get_user_attempt_status($user_id, $attempt_id);

        if ($status->get_closed() === null)
        {
            return $this->close_user_attempt($user_id, $attempt_id);
        }
        else
        {
            return $this->open_user_attempt($user_id, $attempt_id);
        }
    }

    public function get_user_scores_received($user_id, $attempt_id)
    {
        return $this->get_parent()->get_user_scores_received($user_id, $attempt_id);
    }

    public function get_user_scores_given($user_id, $attempt_id)
    {
        return $this->get_parent()->get_user_scores_given($user_id, $attempt_id);
    }

    public function save_scores($user_id, $attempt_id, array $scores)
    {
        return $this->get_parent()->save_scores($user_id, $attempt_id, $scores);
    }

    public function has_scores($attempt_id = null)
    {
        return $this->get_parent()->has_scores($attempt_id);
    }

    /**
     * checks if a pa group has scores
     *
     * @param int $group_id
     * @return boolean
     */
    function group_has_scores($group_id)
    {
        return $this->get_parent()->group_has_scores();
    }

    public function get_indicators()
    {
        return $this->get_parent()->get_indicators();
    }

    public function count_indicators()
    {
        return $this->get_parent()->count_indicators();
    }

    // Endregion PeerAssessmentDisplaySupport;

    /**
     *
     * @param type $title
     * @param type $description
     * @param type $info
     * @param type $actions
     * @param type $level
     * @param type $invisible
     * @param string $image
     * @return type
     */
    static public function render_list_item($title, $description, $info, $actions, $level, $invisible = false, $image = null)
    {
        if (is_null($image))
        {
            $image = Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Logo/22');
        }

        $html = array();

        $class = $invisible ? ' invisible' : '';

        $html[] = '<div class="announcements level_' . $level . '"  style="background-image: url(' . $image . ')">';
        $html[] = '<div class="title' . $class . '">';
        $html[] = $title;
        $html[] = '</div>';
        $html[] = '<div class="description' . $class . '">';
        $html[] = $description;
        $html[] = '</div>';
        $html[] = '<div class="publication_info' . $class . '">';
        $html[] = $info;
        $html[] = '</div>';
        $html[] = '<div class="publication_actions">';
        $html[] = $actions;
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets a reference to the action bar with the default actions already added
     *
     * @return ActionBarRenderer Reference to the action bar
     */
    protected function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

            $display_action = Request :: get(self :: PARAM_ACTION);

            if (! is_null($display_action))
            {
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('PeerAssessmentComplexDisplayUserAttemptStatusViewerComponent'),
                        Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                        $this->get_url(array(self :: PARAM_ACTION => null))));
            }
            if ($this->is_allowed(self :: EDIT_RIGHT))
            /**
             *
             * @todo should not be weblcmsright
             */
            {

                if ($display_action != self :: ACTION_OVERVIEW_STATUS)
                    $this->action_bar->add_tool_action(
                        new ToolbarItem(
                            Translation :: get('PeerAssessmentComplexDisplayStatusViewerComponent'),
                            Theme :: getInstance()->getCommonImagePath('Action/Reporting'),
                            $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_OVERVIEW_STATUS))));

                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('ToolComplexBuilder'),
                        Theme :: getInstance()->getCommonImagePath('Action/Build'),
                        $this->get_url($this->get_builder_params())));
            }

            if ($display_action != self :: ACTION_OVERVIEW_RESULTS)
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('PeerAssessmentComplexDisplayResultsViewerComponent'),
                        Theme :: getInstance()->getCommonImagePath('Action/ViewResults'),
                        $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_OVERVIEW_RESULTS))));
            $settings = $this->get_settings($this->get_publication_id());

            if ($display_action != self :: ACTION_BROWSE_DIRECT_GROUP_SUBSCRIBE &&
                 $settings->get_direct_subscribe_available())
                $this->action_bar->add_tool_action(
                    new ToolbarItem(
                        Translation :: get('PeerAssessmentComplexDisplayDirectGroupSubscribeBrowserComponent'),
                        Theme :: getInstance()->getCommonImagePath('Action/Group'),
                        $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_DIRECT_GROUP_SUBSCRIBE))));

            return $this->action_bar;
        }
    }

    /**
     * Renders the action bar
     *
     * @return string The html representation of the action bar
     */
    protected function render_action_bar()
    {
        return $this->get_action_bar()->as_html();
    }

    function get_application_component_path()
    {
        return __DIR__ . '/component/';
    }

    /**
     * checks if current user has correct rights
     */
    public function is_allowed($right)
    {
        return $this->get_parent()->is_allowed($right);
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->get_parent()->is_allowed_to_view_content_object();
    }

    public function is_allowed_to_edit_content_object()
    {
        return $this->get_parent()->is_allowed_to_edit_content_object();
    }

    public function is_allowed_to_add_child()
    {
        return $this->get_parent()->is_allowed_to_add_child();
    }

    public function is_allowed_to_delete_child()
    {
        return $this->get_parent()->is_allowed_to_delete_child();
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->get_parent()->is_allowed_to_delete_feedback();
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->get_parent()->is_allowed_to_edit_feedback();
    }

    /**
     * asks publication id to parent
     */
    public function get_publication_id()
    {
        return $this->get_parent()->get_publication_id();
    }

    public function user_is_enrolled_in_group($user_id)
    {
        return $this->get_parent()->user_is_enrolled_in_group($user_id);
    }

    public function get_context_group_users($context_group_id)
    {
        return $this->get_parent()->get_context_group_users($context_group_id);
    }

    public function get_context_group($context_group_id)
    {
        return $this->get_parent()->get_context_group($context_group_id);
    }

    public function save_feedback($user_id, $attempt_id, array $feedback)
    {
        return $this->get_parent()->save_feedback($user_id, $attempt_id, $feedback);
    }

    public function get_user_feedback_given($user_id, $attempt_id)
    {
        return $this->get_parent()->get_user_feedback_given($user_id, $attempt_id);
    }

    public function get_user_feedback_received($user_id, $attempt_id)
    {
        return $this->get_parent()->get_user_feedback_received($user_id, $attempt_id);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $peer_assessment = $this->get_root_content_object();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_USER_ATTEMPT_STATUS)),
                $peer_assessment->get_title()));
    }

    function get_builder_params()
    {
        return $this->get_parent()->get_builder_params();
    }
}
