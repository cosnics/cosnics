<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Configuration\ConfigurationTable;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class QuestionManagerComponent extends TabComponent implements TableSupport
{
    
    const PAGE_CONFIGS_TAB = 1;
    const PAGE_QUESTIONS_TAB = 2;
    const VISIBLE_QUESTION_ID = 'visible_question_id';
    const INVISIBLE_QUESTION_ID = 'invisible_question_id';
    const ANSWERMATCH = 'answer_match';

    private $page_id;

    function build()
    {
        $this->page_id = $this->get_current_node()->get_parent()->get_content_object()->get_id();
               
        $html = array();
        
        $table = new ConfigurationTable($this);
        
        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($object_table_class_name)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Configuration :: class_name(), Configuration :: PROPERTY_PAGE_ID),
            new StaticConditionVariable($this->page_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Configuration :: class_name(), Configuration :: PROPERTY_COMPLEX_QUESTION_ID),
            new StaticConditionVariable($this->get_complex_content_object_item_id()));
        
        return new AndCondition($conditions);
    }
    
    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP);
    }

}

?>