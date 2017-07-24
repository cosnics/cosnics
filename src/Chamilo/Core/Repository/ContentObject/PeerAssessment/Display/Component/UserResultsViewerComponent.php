<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\PeerAssessmentGraph;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Enter description here .
 * ..
 *
 * @author admin
 */
class UserResultsViewerComponent extends Manager
{

    private $processor;

    private $attempt_id;

    private $publication_id;

    private $user_id;

    private $users;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->publication_id = Request::get(self::PARAM_PUBLICATION);
        $this->attempt_id = Request::get(self::PARAM_ATTEMPT);
        $this->user_id = Request::get(self::PARAM_USER);
        $root_content_object = $this->get_root_content_object();
        $this->processor = $root_content_object->get_result_processor();

        $status = $this->get_user_attempt_status($this->user_id, $this->attempt_id);

        // redirect if attempt status is closed
        if (is_null($status->get_closed()) && $this->get_attempt($this->attempt_id)->get_end_date() < time())
        {
            throw new NotAllowedException();
        }

        $group = $this->get_user_group($this->user_id);
        $group_id = $group->get_id();

        if ($this->is_allowed(self::EDIT_RIGHT))
        {
            $this->users = $this->get_group_users($group_id);
        }
        else
        {
            $this->users[] = $this->get_user();
        }

        // only edit right or target user is allowed
        if (! $this->is_allowed(self::EDIT_RIGHT) && $this->get_user()->get_id() != $this->user_id)
        {
            $this->redirect(
                Translation::get('Notallowed'),
                true,
                array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS));
        }

        $render_html = $this->render();

        if (! $render_html)
        {
            $params = array(self::PARAM_ACTION => self::ACTION_OVERVIEW_RESULTS);
            $this->redirect(Translation::get('NoScores'), true, $params);
        }

        if ($this->user_id != $this->get_user()->get_id())
        {
            $subject_user = \Chamilo\Core\User\Storage\Datamanager::retrieve_by_id(User::class_name(), $this->user_id);
        }
        else
        {
            $subject_user = $this->get_user();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<h3>' . $subject_user->get_firstname() . ' ' . $subject_user->get_lastname() . '</h3>';
        $html[] = $this->render_action_bar();
        $html[] = $render_html;

        if ($root_content_object->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
        {
            $html[] = '<br/>' . $this->render_graph();
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_action_bar()
    {
        $settings = $this->get_settings($this->get_publication_id());

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        if ($settings->get_enable_user_results_export())
        {
            if (! $this->is_allowed(self::EDIT_RIGHT) && $this->get_user()->get_id() != Request::get(self::PARAM_USER))
                return;

            $buttonToolbar = $this->buttonToolbarRenderer->getButtonToolBar();
            $commonActions = new ButtonGroup();
            $commonActions->addButton(
                new Button(
                    Translation::get('Export', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Export/Ods'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_EXPORT_USER_RESULT,
                            self::PARAM_EXPORT_TYPE => self::EXPORT_TYPE_EXCEL,
                            self::PARAM_USER => Request::get(self::PARAM_USER),
                            self::PARAM_ATTEMPT => Request::get(self::PARAM_ATTEMPT)))));

            $buttonToolbar->addButtonGroup($commonActions);
        }

        return $this->buttonToolbarRenderer->render();
    }

    private function render()
    {
        // TODO check for scores/feedback/both
        // TODO display images on tabs
        $type = $this->get_root_content_object()->get_assessment_type();

        $tabs = new DynamicTabsRenderer('', $this);

        if ($type == PeerAssessment::TYPE_SCORES || $type == PeerAssessment::TYPE_BOTH)
        {

            // render the scores tab
            $tabs->add_tab(new DynamicContentTab('scores', Translation::get('Scores'), null, $this->render_scores()));
        }
        if ($type == PeerAssessment::TYPE_FEEDBACK || $type == PeerAssessment::TYPE_BOTH)
        {
            // render the feedback tab
            $tabs->add_tab(
                new DynamicContentTab('feedback', Translation::get('Feedback'), null, $this->render_feedback()));
        }
        return $tabs->render();
    }

    private function render_scores()
    {
        $indicators = $this->get_indicators();

        $this->processor->retrieve_scores($this, $this->user_id, $this->attempt_id);
        // $scores = $this->processor->get_scores();

        return $this->processor->render_table($indicators, $this->users);
    }

    private function render_feedback()
    {
        $settings = $this->get_settings($this->get_publication_id());

        $group_id = $this->get_user_group($this->user_id)->get_id();
        $users = $this->get_group_users($group_id);
        $feedback = $this->get_user_feedback_received($this->user_id, $this->attempt_id);

        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data" style="width: auto">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        if ($settings->get_anonymous_feedback() == false)
            $html[] = '<th>' . Translation::get('User') . '</th>';
        $html[] = '<th>' . Translation::get('Feedback') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $r = 0;

        foreach ($users as $u)
        {
            $class = ($r ++ % 2) ? 'odd' : 'even';

            $html[] = '<tr class="row_' . $class . '">';
            if ($settings->get_anonymous_feedback() == false)
                $html[] = '<td>' . $u->get_firstname() . ' ' . $u->get_lastname() . '</td>';
            $html[] = '<td>' . $feedback[$u->get_id()] . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }

    public function render_graph()
    {
        $attempt = $this->get_attempt($this->attempt_id);

        foreach ($this->users as $curr_user)
        {
            if ($curr_user->get_id() == $this->user_id)
            {
                $user = $curr_user;
                break;
            }
        }

        $graph = new PeerAssessmentGraph(
            Translation::get('Result') . ' ' . $attempt->get_title() . ' ' . $user->get_firstname() . ' ' .
                 $user->get_lastname());

        $graph->set_offset($this->processor->get_graph_offset());
        $graph->set_range($this->processor->get_graph_range());

        $indicators = $this->get_indicators();

        if (count($indicators) < 3)
        {
            $this->display_error_message(Translation::get('NotEnoughIndicators'));
            return;
        }

        foreach ($indicators as $indicator)
        {
            $competences[] = $indicator->get_title();
            $averages[] = $this->processor->col_avg($indicator->get_id());
        }

        $graph->add_competences($competences);

        $scores = $this->processor->get_scores();

        $graph->set_personal_score($scores[$user->get_id()]);
        $graph->set_average_total_score($averages);

        return $graph->render();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        parent::add_additional_breadcrumbs($breadcrumbtrail);

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_OVERVIEW_RESULTS)),
                Translation::get('ResultsOverview')));
    }
}
