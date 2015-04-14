<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\table\Configurer\ConfigTable;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\table\QuestionBrowser\QuestionTable;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class ConfigureComponent extends Manager implements DelegateComponent, TableSupport
{
    const PAGE_CONFIGS_TAB = 1;
    const PAGE_QUESTIONS_TAB = 2;
    const VISIBLE_QUESTION_ID = 'visible_question_id';
    const INVISIBLE_QUESTION_ID = 'invisible_question_id';
    const ANSWERMATCH = 'answer_match';

    private $page_id;

    private $action_bar;

    function run()
    {
        $this->page_id = $this->get_root_content_object_id();
        
        $this->action_bar = $this->get_action_bar();
        
        $html = array();
        
        $html[] = $this->render_header();
        
        $html[] = $this->action_bar->as_html();
        
        $html[] = $this->get_tables();
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function get_tables()
    {
        $renderer_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        $parameters = $this->get_parameters();
        
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: PAGE_CONFIGS_TAB;
        $table = new ConfigTable($this);
        $tabs->add_tab(
            new DynamicContentTab(
                self :: PAGE_CONFIGS_TAB, 
                Translation :: get('PageConfigs'), 
                Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Builder', 
                    'Logo/16'), 
                $table->as_html()));
        
        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: PAGE_QUESTIONS_TAB;
        $table = new QuestionTable($this);
        $tabs->add_tab(
        new DynamicContentTab(
        self :: PAGE_QUESTIONS_TAB,
        Translation :: get('PageQuestions'),
        Theme :: getInstance()->getImagePath(
        'Chamilo\Core\Repository\ContentObject\Survey\Page\Builder',
        'Logo/16'),
        $table->as_html()));
        
        return $tabs->render();
    }

    public function get_table_condition($object_table_class_name)
    {
        switch ($object_table_class_name)
        {
            case QuestionTable :: class_name() :
                return $this->get_condition();
                break;
            
            case ConfigTable :: class_name() :
                return $this->get_page_config_condition();
                break;
        }
        ;
    }

    function get_condition()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: CLASS_NAME, 
                ComplexContentObjectItem :: PROPERTY_PARENT), 
            new StaticConditionVariable($this->page_id));
        return $condition;
    }

    function get_page_config_condition()
    {
        return $this->page_id;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }
}

?>