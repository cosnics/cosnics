<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Datamanager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * Enter description here .
 * ..
 * 
 * @author admin
 */
class UserStatusViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->publication_id = Request::get(self::PARAM_PUBLICATION);
        $this->attempt_id = Request::get(self::PARAM_ATTEMPT);
        $this->user_id = Request::get(self::PARAM_USER);
        
        // only edit right is allowed
        if (! $this->is_allowed(self::EDIT_RIGHT))
        {
            $this->redirect(
                Translation::get('Notallowed'), 
                true, 
                array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS));
        }
        
        if ($this->user_id != $this->get_user()->get_id())
        {
            $subject_user = Datamanager::retrieve_by_id(User::class_name(), $this->user_id);
        }
        else
        {
            $subject_user = $this->get_user();
        }
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<h3>' . $subject_user->get_firstname() . ' ' . $subject_user->get_lastname() . '</h3>';
        $html[] = $this->render_action_bar();
        $html[] = $this->render();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function render()
    {
        $assesment_type = $this->get_root_content_object()->get_assessment_type();
        // TODO check for scores/feedback/both
        // TODO display images on tabs
        
        $tabs = new DynamicTabsRenderer('', $this);
        
        if ($assesment_type == PeerAssessment::TYPE_BOTH || $assesment_type == PeerAssessment::TYPE_SCORES)
        {
            // // render the scores tab
            $tabs->add_tab(new DynamicContentTab('scores', Translation::get('Scores'), null, $this->render_scores()));
        }
        
        if ($assesment_type == PeerAssessment::TYPE_BOTH || $assesment_type == PeerAssessment::TYPE_FEEDBACK)
        {
            // render the feedback tab
            
            $tabs->add_tab(
                new DynamicContentTab('feedback', Translation::get('Feedback'), null, $this->render_feedback()));
        }
        return $tabs->render();
    }

    private function render_scores()
    {
        $group_id = $this->get_user_group($this->user_id)->get_id();
        $users = $this->get_group_users($group_id);
        $indicators = $this->get_indicators();
        $scores = $this->get_user_scores_given($this->user_id, $this->attempt_id);
        
        $processor = $this->get_root_content_object()->get_result_processor();
        $processor->retrieve_scores($this, $this->user_id, $this->attempt_id);
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data" style="width: auto">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation::get('User') . '</th>';
        
        foreach ($indicators as $i)
        {
            $html[] = '<th>' . $i->get_title() . '</th>';
        }
        
        // $html[] = '<th>' . Translation :: get('Average') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $r = 0;
        
        foreach ($users as $u)
        {
            $class = ($r ++ % 2) ? 'odd' : 'even';
            
            $html[] = '<tr class="row_' . $class . '">';
            $html[] = '<td>' . $u->get_firstname() . ' ' . $u->get_lastname() . '</td>';
            
            foreach ($indicators as $i)
            {
                $html[] = '<td style="text-align: center">' . $scores[$u->get_id()][$i->get_id()] . '</td>';
            }
            
            // $html[] = '<td style="text-align: center">' . round($processor->row_avg($u->get_id()), 2) . '</td>';
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }

    private function render_feedback()
    {
        $group_id = $this->get_user_group($this->user_id)->get_id();
        $users = $this->get_group_users($group_id);
        $feedback = $this->get_user_feedback_given($this->user_id, $this->attempt_id);
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data" style="width: auto">';
        $html[] = '<thead>';
        $html[] = '<tr>';
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
            $html[] = '<td>' . $u->get_firstname() . ' ' . $u->get_lastname() . '</td>';
            $html[] = '<td>' . $feedback[$u->get_id()] . '</td>';
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        parent::add_additional_breadcrumbs($breadcrumbtrail);
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_OVERVIEW_STATUS)), 
                Translation::get('StatusOverview')));
    }
}
