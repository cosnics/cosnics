<?php
namespace Chamilo\Application\Survey\Mail\Component;

use Chamilo\Application\Survey\Mail\Manager;
use Chamilo\Application\Survey\Mail\Storage\DataClass\Mail;
use Chamilo\Application\Survey\Mail\Table\MailTable\MailTable;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class BrowserComponent extends Manager implements DelegateComponent, TableSupport
{
    const TAB_MAILS_TO_PARTICIPANTS = 1;
    const TAB_MAILS_TO_EXPORTERS = 2;
    const TAB_MAILS_TO_REPORTERS = 3;
    const PARAM_TABLE_TYPE = 'table_type';

    private $table_type;

    private $action_bar;

    private $publication_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->table_type = Request :: get(self :: PARAM_TABLE_TYPE, self :: TAB_MAILS_TO_PARTICIPANTS);
        $this->publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        
//         if (! Rights :: get_instance()->is_right_granted(Rights :: MAIL_RIGHT, $this->publication_id))
//         {
//             throw new NotAllowedException();
//         }
        
        $this->action_bar = $this->get_action_bar();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $this->get_tabs_html();
        $html[] = $this->render_footer();
       
        return implode(PHP_EOL, $html);
    }

    function get_tabs_html()
    {
        $html = array();
       
        $tabs = new DynamicVisualTabsRenderer(self :: class_name());
        
        $params = $this->get_parameters();
        $params[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        $params[self :: PARAM_TABLE_TYPE] = self :: TAB_MAILS_TO_PARTICIPANTS;
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_MAILS_TO_PARTICIPANTS, 
                Translation :: get('MailsToParticipants'), 
                Theme :: getInstance()->getImagePath('Chamilo\Application\Survey', 'Logo/16'), 
                $this->get_url($params), 
                $this->get_table_type() == self :: TAB_MAILS_TO_PARTICIPANTS));
        
        $params[self :: PARAM_TABLE_TYPE] = self :: TAB_MAILS_TO_EXPORTERS;
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_MAILS_TO_EXPORTERS, 
                Translation :: get('MailsToExporters'), 
                Theme :: getInstance()->getCommonImagePath('Action/Export'), 
                $this->get_url($params), 
                $this->get_table_type() == self :: TAB_MAILS_TO_EXPORTERS));
        
        $params[self :: PARAM_TABLE_TYPE] = self :: TAB_MAILS_TO_REPORTERS;
        $tabs->add_tab(
            new DynamicVisualTab(
                self :: TAB_MAILS_TO_REPORTERS, 
                Translation :: get('MailsToReporters'), 
                Theme :: getInstance()->getCommonImagePath('Action/ViewResults'), 
                $this->get_url($params), 
                $this->get_table_type() == self :: TAB_MAILS_TO_REPORTERS));
        
        $table = new MailTable($this);
        $tabs->set_content($table->as_html());
        
        $html[] = $tabs->render();
        
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Mail :: class_name(), Mail :: PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->publication_id));
        
        switch ($this->get_table_type())
        {
            case self :: TAB_MAILS_TO_EXPORTERS :
                $type = Mail :: EXPORT_TYPE;
                break;
            
            case self :: TAB_MAILS_TO_PARTICIPANTS :
                $type = Mail :: PARTICIPANT_TYPE;
                break;
            
            case self :: TAB_MAILS_TO_REPORTERS :
                $type = Mail :: REPORTING_TYPE;
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Mail :: class_name(), Mail :: PROPERTY_TYPE), 
            new StaticConditionVariable($type));
        $condition = new AndCondition($conditions);
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url(array(self :: PARAM_TABLE_TYPE => $this->get_table_type())));
        
//         if (Rights :: get_instance()->is_right_granted(Rights :: MAIL_RIGHT, $this->publication_id))
//         {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('SendMailToParticipants'), 
                    Theme :: getInstance()->getCommonImagePath('Action/InviteUsers'), 
                    $this->get_send_mail_url($this->publication_id, Mail :: PARTICIPANT_TYPE), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('SendMailToExporters'), 
                    Theme :: getInstance()->getCommonImagePath('Action/InviteUsers'), 
                    $this->get_send_mail_url($this->publication_id, Mail :: EXPORT_TYPE), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('SendMailToReporters'), 
                    Theme :: getInstance()->getCommonImagePath('Action/InviteUsers'), 
                    $this->get_send_mail_url($this->publication_id, Mail :: REPORTING_TYPE), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//         }
        
        if ($this->get_user()->is_platform_admin())
        {
            $action_bar->add_tool_action(
                new ToolbarItem(
                    Translation :: get('SendTestMail'), 
                    Theme :: getInstance()->getCommonImagePath('Action/InviteUsers'), 
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => Manager :: ACTION_TEST_MAIL, 
                            Manager :: PARAM_PUBLICATION_ID => $this->publication_id), 
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL)));
        }
        return $action_bar;
    }

//     function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
//     {
//         $breadcrumbtrail->add(
//             new Breadcrumb(
//                 $this->get_url(
//                     array(
//                         \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE)), 
//                 Translation :: get('BrowserComponent')));
//         $breadcrumbtrail->add(
//             new Breadcrumb(
//                 $this->get_url(
//                     array(
//                         \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE_PARTICIPANTS, 
//                         Manager :: PARAM_PUBLICATION_ID => Request :: get(Manager :: PARAM_PUBLICATION_ID))), 
//                 Translation :: get('ParticipantBrowserComponent')));
//     }

    public function get_table_condition($object_table_class_name)
    {
        return $this->get_condition();
    }

    public function get_table_type()
    {
        return $this->table_type;
    }
}
?>