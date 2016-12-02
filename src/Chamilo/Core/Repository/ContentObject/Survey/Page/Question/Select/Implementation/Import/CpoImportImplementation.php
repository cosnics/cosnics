<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Common\Export\CpoExportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\SelectOption;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

class CpoImportImplementation extends ImportImplementation
{

    function import()
    {
        $content_object = ContentObjectImport::launch($this);
        
        $dom_xpath = $this->get_controller()->get_dom_xpath();
        $content_object_node = $this->get_content_object_import_parameters()->get_content_object_node();
        
        $export_node = $dom_xpath->query(CpoExportImplementation::SURVEY_SELECT_QUESTION_EXPORT, $content_object_node)->item(
            0);
        
        $option_node_list = $dom_xpath->query(CpoExportImplementation::OPTIONS_NODE, $export_node)->item(0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::OPTION_NODE, $option_node_list) as $option_node)
        {
            
            $this->get_controller()->set_cache_id(
                SelectOption::get_table_name(), 
                SelectOption::PROPERTY_ID, 
                $option_node->getAttribute('id'), 
                $option_node->getAttribute('display_order'));
            $option = new SelectOption();
            $option->set_value($option_node->getAttribute('value'));
            $option->set_display_order($option_node->getAttribute('display_order'));
            $content_object->add_option($option);
        }
        
        return $content_object;
    }

    function post_import($content_object)
    {
        $dom_xpath = $this->get_controller()->get_dom_xpath();
        $content_object_node = $this->get_content_object_import_parameters()->get_content_object_node();
        
        $export_node = $dom_xpath->query(CpoExportImplementation::SURVEY_SELECT_QUESTION_EXPORT, $content_object_node)->item(
            0);
        
        $option_node_list = $dom_xpath->query(CpoExportImplementation::OPTIONS_NODE, $export_node)->item(0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::OPTION_NODE, $option_node_list) as $option_node)
        {
            $display_order = $this->get_controller()->get_cache_id(
                SelectOption::get_table_name(), 
                SelectOption::PROPERTY_ID, 
                $option_node->getAttribute('id'));
            
            $dm = DataManager::getInstance();
            $conditions = array();
            $conditions[] = new EqualityCondition(SelectOption::PROPERTY_QUESTION_ID, $content_object->get_id());
            $conditions[] = new EqualityCondition(SelectOption::PROPERTY_DISPLAY_ORDER, $display_order);
            $condition = new AndCondition($conditions);
            $option = $dm->retrieve_survey_select_question_options($condition)->next_result();
            
            if ($option)
            {
                $this->get_controller()->set_cache_id(
                    SelectOption::get_table_name(), 
                    SelectOption::PROPERTY_ID, 
                    $option_node->getAttribute('id'), 
                    $option->get_id());
            }
            else
            {
                $this->get_controller()->set_cache_id(
                    SelectOption::get_table_name(), 
                    SelectOption::PROPERTY_ID, 
                    $option_node->getAttribute('id'), 
                    null);
            }
        }
    }
}
?>