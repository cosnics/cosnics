<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentAttempt as WeblcmsPeerAssessmentAttemptTracker;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\UserAttemptStatusViewer\UserAttemptStatusViewerTable;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

class UserAttemptStatusViewerComponent extends Manager implements TableSupport
{
    const PARAM_BROWSER = 'browser';
    const BROWSER_MODE_TABLE = 'table';
    const BROWSER_MODE_LIST = 'list';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->render_action_bar();
        
        // if the user is not enrolled in any group,
        // display a message and a link to the group subscription page
        $enrolled = $this->user_is_enrolled_in_group($this->get_user_id());
        $settings = $this->get_settings($this->get_publication_id());
        
        if (! $enrolled && $settings->get_direct_subscribe_available() && $settings->get_subscription_deadline() > time())
        {
            $url = $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_USER));
            $html[] = Display::error_message(
                Translation::get('NoGroupSubscription') . ' <a href="' . $url . '">' .
                     Translation::get('SubscribeToGroup') . '</a>');
        }
        
        // if no attempts are defined, display a message
        $attempts = $this->get_attempts($this->get_publication_id());
        
        if (count($attempts) === 0)
        {
            $html[] = Display::error_message(Translation::get('NoAttemptsDefined'));
        }
        else
        {
            $html[] = $this->render_attempts($attempts);
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function render_attempts(array $attempts)
    {
        // TODO display attempt status for user
        // TODO date locale doesn't work
        switch (Request::get(self::PARAM_BROWSER))
        {
            
            case self::BROWSER_MODE_TABLE :
                $table = new UserAttemptStatusViewerTable($this);
                return $table->as_html();
            default :
                return $this->render_attempts_list($attempts);
        }
    }

    private function render_attempts_list(array $attempts)
    {
        $html = array();

        $level = 0;

        // loop through all the attempts and render them
        foreach ($attempts as $a)
        {
            if ($a->get_hidden() && ! $this->is_allowed(self::EDIT_RIGHT))
            {
                continue;
            }
            
            // do not render this attempt if it is hidden and the user has no edit rights
            $status = $this->get_user_attempt_status($this->get_user_id(), $a->get_id());
            
            // check if we are between the attempt's start and end date
            $active = ($a->get_start_date() < time()) && ($a->get_end_date() > time());
            
            // determine if the attempt should be disabled
            $disabled = ($status->get_closed() !== null) || ! $active;
            
            if ($a->get_start_date() < time())
            {
                $url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_TAKE_PEER_ASSESSMENT, 
                        self::PARAM_ATTEMPT => $a->get_id()));
            }
            else
            {
                $url = '#';
            }
            
            if (! $disabled)
            {
                $title = '<a href="' . $url . '">' . $a->get_title() . '</a>';
            }
            else
            {
                $title = $a->get_title();
            }
            
            $description = $a->get_description() . $this->render_status($status, $a);
            
            $info = sprintf(
                Translation::get('AttemptInfoDate'), 
                date('d/m/Y', $a->get_start_date()), 
                date('d/m/Y', $a->get_end_date()));
            
            if ($a->get_start_date() <= time())
            {
                $actions = $this->render_toolbar($status, $a);
            }
            
            $level = $level == 1 ? 2 : 1;
            
            $html[] = $this->render_list_item($title, $description, $info, $actions, $level, $disabled);
        }
        
        return implode(PHP_EOL, $html);
    }

    public function render_status($status, $attempt)
    {
        $html = array();
        
        if ($attempt->get_start_date() > time())
        {
            $html[] = Translation::get('AttemptNotStarted');
        }
        
        if ($status->get_progress() === 100)
        {
            $html[] = Translation::get('AttemptFinished');
        }
        elseif ($status->get_progress() !== null &&
             $this->get_root_content_object()->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
        {
            $html[] = Translation::get('Progress') . ': <b>' . round($status->get_progress()) . '%</b>';
        }
        
        if ($status->get_closed())
        {
            $html[] = '<span style="color:red">' . Translation::get('AttemptClosedOn') . ': ' .
                 date('d/m/Y', $status->get_closed()) . '<span/>';
        }
        
        return implode("<br />", $html);
    }

    public function render_toolbar($status, $attempt)
    {
        $toolbar = new Toolbar();
        
        // no specific actions for now...
        
        return $toolbar->as_html();
    }

    public function render_action_bar()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        $buttonToolbar = $this->buttonToolbarRenderer->getButtonToolBar();
        $toolActions = new ButtonGroup();
        $toolActions->addButton(
            new Button(
                
                Translation::get('List'), 
                Theme::getInstance()->getCommonImagePath('Action/List'), 
                $this->get_url(array(self::PARAM_BROWSER => self::BROWSER_MODE_LIST))));
        $toolActions->addButton(
            new Button(
                Translation::get('Table'), 
                Theme::getInstance()->getCommonImagePath('Action/Table'), 
                $this->get_url(array(self::PARAM_BROWSER => self::BROWSER_MODE_TABLE))));
        
        $buttonToolbar->addButtonGroup($toolActions);
        
        return $this->buttonToolbarRenderer->render();
    }

    public function get_condition()
    {
        return new EqualityCondition(WeblcmsPeerAssessmentAttemptTracker::PROPERTY_PUBLICATION_ID, $this->publication_id);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $peer_assessment = $this->get_root_content_object();
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_USER_ATTEMPT_STATUS)), 
                $peer_assessment->get_title()));
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
