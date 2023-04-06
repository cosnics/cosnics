<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * Enter description here .
 * ..
 * 
 * @author admin
 */
class StatusViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(self::EDIT_RIGHT))
            $this->redirect(null, null, array(self::PARAM_ACTION => self::ACTION_VIEW_USER_STATUS));
        
        $this->publication_id = Request::get(self::PARAM_PUBLICATION);
        $this->group_id = Request::get(self::PARAM_GROUP);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->render_action_bar();
        $html[] = $this->render_tabs();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function render_tabs()
    {
        $groups = $this->get_groups($this->publication_id);
        
        if ($groups)
        {
            $group_id = empty($this->group_id) ? $groups[0]->get_id() : $this->group_id;
            
            $tabs = new DynamicVisualTabsRenderer();
            // loop through the groups
            foreach ($groups as $g)
            {
                $url = $this->get_url(array(self::PARAM_GROUP => $g->get_id()));
                
                $tab = new DynamicVisualTab('tab_' + $g->get_id(), $g->get_name(), null, $url);
                
                if ($g->get_id() == $group_id)
                {
                    $tab->set_selected(true);
                    $tabs->set_content('tab');
                    $tabs->set_content($this->render_group($g->get_id()));
                }
                
                $tabs->add_tab($tab);
            }
            
            return $tabs->render();
        }
        else
        {
            $this->redirect(
                Translation::get('NoGroupsDefined'), 
                1, 
                array(self::PARAM_ACTION => self::ACTION_VIEW_USER_ATTEMPT_STATUS));
        }
    }

    private function render_group($group_id)
    {
        $users = $this->get_group_users($group_id);
        $attempts = $this->get_attempts($this->publication_id);
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data" style="width: auto">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation::get('User') . '</th>';
        
        foreach ($attempts as $a)
        {
            $html[] = '<th>' . $a->get_title() . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $r = 0;
        
        foreach ($users as $u)
        {
            $class = ($r ++ % 2) ? 'odd' : 'even';
            
            $html[] = '<tr class="row_' . $class . '">';
            $html[] = '<td style="min-width: 200px">' . $u->get_firstname() . ' ' . $u->get_lastname() . '</td>';
            
            foreach ($attempts as $a)
            {
                $html[] = '<td style="min-width: 100px">' . $this->render_status($u, $a) . '</td>';
            }
            
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }

    private function render_status($user, $attempt)
    {
        $status = $this->get_user_attempt_status($user->get_id(), $attempt->get_id());
        
        $html = array();
        
        $html[] = '<div>' . $this->render_progress($status) . '</div>';
        $html[] = '<div>';
        $html[] = $this->render_details_link($status);
        $html[] = $this->render_closed_status($status, $attempt);
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * renders progress depending on type of PA scores/scores and feedback => the progress of the user_attempt_status
     * feedback : ok if there is a user_attempt_status and not ok if not
     * 
     * @param type $status
     * @return string
     */
    private function render_progress($status)
    {
        $root_content_object = $this->get_root_content_object();
        $assessment_type = $root_content_object->get_assessment_type();
        if ($assessment_type == PeerAssessment::TYPE_BOTH || $assessment_type == PeerAssessment::TYPE_SCORES)
        {
            return '<div>' . Translation::get('Progress') . ': <b>' . round($status->get_progress()) . '%</b></div>';
        }
        else
        {
            if (is_null($status->get_progress()))
            {
                return Translation::get('NoFeedbackGiven');
            }
            else
            {
                return Translation::get('FeedbackGiven');
            }
        }
    }

    private function render_details_link($status)
    {
        $item = new ToolbarItem(
            Translation::get('Details'), 
            Theme::getInstance()->getCommonImagePath(
                (($status->get_progress() > 0) ? 'Action/Details' : 'Action/DetailsNa')), 
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_USER_STATUS, 
                    self::PARAM_ATTEMPT => $status->get_attempt_id(), 
                    self::PARAM_USER => $status->get_user_id())), 
            ToolbarItem::DISPLAY_ICON);
        
        return $item->as_html();
    }

    private function render_closed_status($status, $attempt)
    {
        $item = new ToolbarItem();
        $item->set_display(ToolbarItem::DISPLAY_ICON);
        $item->set_href(
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_TOGGLE_CLOSE_USER_ATTEMPT, 
                    self::PARAM_ATTEMPT => $status->get_attempt_id(), 
                    self::PARAM_USER => $status->get_user_id())));
        
        if ($status->get_closed() === null)
        {
            $item->set_label(Translation::get('StatusOpen') . ' ' . Translation::get('CloseStatus'));
            $item->set_image(Theme::getInstance()->getCommonImagePath('Action/LockNa'));
        }
        else
        {
            if ($status->get_closed() < $attempt->get_end_date())
            {
                $item->set_label(
                    Translation::get('AttemptClosed') . ': ' . date('d/m/Y', $status->get_closed()) . '. ' .
                         Translation::get('OpenStatus'));
                $item->set_image(Theme::getInstance()->getCommonImagePath('Action/SettingTrueLocked'));
            }
            else
            {
                $item->set_label(
                    Translation::get('AttemptClosedAfterDeadline') . ': ' . date('d/m/Y', $status->get_closed()));
                $item->set_image(Theme::getInstance()->getCommonImagePath('Action/SettingFalseLocked'));
                $item->set_href(null);
            }
        }
        
        return $item->as_html();
    }

    protected function render_action_bar()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        return $this->buttonToolbarRenderer->render();
    }
}
