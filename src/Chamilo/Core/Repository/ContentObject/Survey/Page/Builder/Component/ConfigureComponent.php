<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Configurer\ConfigTable;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\QuestionBrowser\QuestionTable;
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

class ConfigureComponent extends Manager
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

        return implode("\n", $html);
    }

    private function get_tables()
    {
        $parameters = $this->get_parameters();
        $table = new QuestionTable($this, $parameters, $this->get_condition());

        $renderer_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        $parameters = $this->get_parameters();

        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();

        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: PAGE_CONFIGS_TAB;
        $table = new ConfigTable($this, $parameters, $this->get_page_config_condition());
        $tabs->add_tab(
            new DynamicContentTab(
                self :: PAGE_CONFIGS_TAB,
                Translation :: get('PageConfigs'),
                Theme :: getInstance()->getImagePath() . 'logo/16.png',
                $table->as_html()));

        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = self :: PAGE_QUESTIONS_TAB;
        $table = new QuestionTable($this, $parameters, $this->get_condition());
        $tabs->add_tab(
            new DynamicContentTab(
                self :: PAGE_QUESTIONS_TAB,
                Translation :: get('PageQuestions'),
                Theme :: getInstance()->getImagePath() . 'logo/16.png',
                $table->as_html()));

        return $tabs->render();
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