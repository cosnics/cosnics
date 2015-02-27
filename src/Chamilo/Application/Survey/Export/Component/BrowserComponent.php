<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataClass\ExportRegistration;
use Chamilo\Application\Survey\Export\Storage\DataClass\ExportTemplate;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Application\Survey\Export\Table\RegistrationTable\ExportRegistrationTable;
use Chamilo\Application\Survey\Export\Table\TemplateTable\ExportTemplateTable;
use Chamilo\Application\Survey\Export\Table\TrackerTable\ExportTable;
use Chamilo\Application\Survey\Export\Storage\DataClass\Export;
use Chamilo\Application\Survey\Export\Storage\DataClass\SynchronizeAnswer;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport
{
    const TAB_EXPORT_TEMPLATES = 1;
    const TAB_EXPORT_REGISTRATIONS = 2;
    const TAB_EXPORT_TACKERS = 3;

    private $action_bar;

    private $publication_id;

    private $synchronisation_tracker;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->publication_id = Request :: get(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
        
        if (! Rights :: is_right_granted(Rights :: RIGHT_EXPORT_RESULT, $this->publication_id))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->action_bar = $this->get_action_bar();
        
        $output = $this->get_tabs_html();
        
        $this->display_header();
        echo $this->action_bar->as_html() . '<br />';
        echo $output;
        $this->display_footer();
    }

    function get_tabs_html()
    {
        $html = array();
        
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $table = new ExportTemplateTable($this);
        $tabs->add_tab(
            new DynamicContentTab(
                self :: TAB_EXPORT_TEMPLATES, 
                Translation :: get('ExportTemplates'), 
                Theme :: getInstance()->getImagePath() . 'Logo/16.png', 
                $table->as_html()));
        
        if (Rights :: is_right_granted(Rights :: RIGHT_ADD_EXPORT_TEMPLATE, $this->publication_id))
        {
            $table = new ExportRegistrationTable($this);
            $tabs->add_tab(
                new DynamicContentTab(
                    self :: TAB_EXPORT_REGISTRATIONS, 
                    Translation :: get('AddExportTemplate'), 
                    Theme :: getInstance()->getImagePath() . 'Logo/16.png', 
                    $table->as_html()));
        }
        
        $cron_enabled = PlatformSetting :: get('enable_export_cron_job', 'application\survey');
        
        if ($cron_enabled)
        {
            $table = new ExportTable($this);
            $tabs->add_tab(
                new DynamicContentTab(
                    self :: TAB_EXPORT_TACKERS, 
                    Translation :: get('ExportTrackers'), 
                    Theme :: getInstance()->getImagePath() . 'Logo/16.png', 
                    $table->as_html()));
        }
        
        $html[] = $tabs->render();
        
        $html[] = '<div class="clear"></div>';
        
        return implode($html, "\n");
    }

    function get_export_template_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ExportTemplate :: class_name(), ExportTemplate :: PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($this->publication_id));
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ExportTemplate :: class_name(), ExportTemplate :: PROPERTY_NAME), 
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ExportTemplate :: class_name(), ExportTemplate :: PROPERTY_DESCRIPTION), 
                '*' . $query . '*');
            $conditions[] = new OrCondition($or_conditions);
        }
        
        return new AndCondition($conditions);
    }

    function get_export_tracker_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Export :: class_name(), Export :: PROPERTY_USER_ID), 
            new StaticConditionVariable($this->get_user_id()));
        $job_condition = new EqualityCondition(
            new PropertyConditionVariable(Export :: class_name(), Export :: PROPERTY_EXPORT_JOB_ID), 
            new StaticConditionVariable(0));
        $conditions[] = new NotCondition($job_condition);
        
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Export :: class_name(), Export :: PROPERTY_TEMPLATE_NAME), 
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Export :: class_name(), Export :: PROPERTY_TEMPLATE_DESCRIPTION), 
                '*' . $query . '*');
            $or_condition = new OrCondition($or_conditions);
            $conditions[] = $or_condition;
        }
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_export_registration_condition()
    {
        $query = $this->action_bar->get_query();
        
        if (isset($query) && $query != '')
        {
            $or_conditions = array();
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ExportRegistration :: class_name(), ExportRegistration :: PROPERTY_NAME), 
                '*' . $query . '*');
            $or_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(
                    ExportRegistration :: class_name(), 
                    ExportRegistration :: PROPERTY_DESCRIPTION), 
                '*' . $query . '*');
            $condition = new OrCondition($or_conditions);
        }
        
        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->set_search_url($this->get_url());
        
        $publication = DataManager :: retrieve_by_id(Publication :: class_name(), $this->publication_id);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizeAnswer :: class_name(), 
                SynchronizeAnswer :: PROPERTY_SURVEY_PUBLICATION_ID), 
            new StaticConditionVariable($this->publication_id));
//         $this->synchronisation_tracker = Tracker :: get_singular_data(
//             SynchronizeAnswer :: CLASS_NAME, 
//             \Chamilo\Application\Survey\Manager :: APPLICATION_NAME, 
//             $condition);
        
//         if ($this->synchronisation_tracker)
//         {
//             $status = $this->synchronisation_tracker->get_status();
            
//             switch ($status)
//             {
//                 case SynchronizeAnswer :: STATUS_SYNCHRONIZED :
//                     if ($publication->get_to_date() > 0)
//                     {
//                         if ($this->synchronisation_tracker->get_created() > $publication->get_to_date())
//                         {
//                             $action_bar->add_tool_action(
//                                 new ToolbarItem(
//                                     Translation :: get('LastSynchronized') . ' ' .
//                                          $this->get_date($this->synchronisation_tracker->get_created()), 
//                                         Theme :: getInstance()->getCommonImagePath() . 'action_config.png', 
//                                         null, 
//                                         ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//                         }
//                         else
//                         {
//                             $action_bar->add_tool_action(
//                                 new ToolbarItem(
//                                     Translation :: get('LastSynchronized') . ' ' .
//                                          $this->get_date($this->synchronisation_tracker->get_created()), 
//                                         Theme :: getInstance()->getCommonImagePath() . 'action_config.png', 
//                                         $this->get_convert_answers_url($this->publication_id), 
//                                         ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//                         }
//                     }
//                     else
//                     {
//                         $action_bar->add_tool_action(
//                             new ToolbarItem(
//                                 Translation :: get('LastSynchronized') . ' ' .
//                                      $this->get_date($this->synchronisation_tracker->get_created()), 
//                                     Theme :: getInstance()->getCommonImagePath() . 'action_config.png', 
//                                     $this->get_convert_answers_url($this->publication_id), 
//                                     ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//                     }
//                     break;
                
//                 case SynchronizeAnswer :: STATUS_SYNCHRONISATION_IN_QUEUE :
//                     $action_bar->add_tool_action(
//                         new ToolbarItem(
//                             Translation :: get('AnswerSynchronizationInQueue') . ' ' .
//                                  $this->get_date($this->synchronisation_tracker->get_created()), 
//                                 Theme :: getInstance()->getCommonImagePath() . 'action_config_na.png', 
//                                 null, 
//                                 ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//                     break;
//             }
//         }
//         else
//         {
//             $action_bar->add_tool_action(
//                 new ToolbarItem(
//                     Translation :: get('SynchronizeAnswers'), 
//                     Theme :: getInstance()->getCommonImagePath() . 'action_config.png', 
//                     $this->get_convert_answers_url($this->publication_id), 
//                     ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//         }
        return $action_bar;
    }

    function get_tracker()
    {
        return $this->synchronisation_tracker;
    }

    private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('NoDate');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE, 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => \Chamilo\Application\Survey\Component\BrowserComponent :: TAB_EXPORT)), 
                Translation :: get('BrowserComponent')));
    }

    function get_parameters()
    {
        return array(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
    }

    public function get_table_condition($object_table_class_name)
    {
        switch ($object_table_class_name)
        {
            case ExportRegistrationTable :: class_name() :
                return $this->get_export_registration_condition();
                break;
            case ExportTemplateTable :: class_name() :
                return $this->get_export_template_condition();
                break;
            case ExportTable :: class_name() :
                return $this->get_export_tracker_condition();
                break;
        }
    }
}
?>