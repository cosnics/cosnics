<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Common\Export\CpoExportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\MatrixMatch;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\MatrixOption;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class CpoImportImplementation extends ImportImplementation
{

    function import()
    {
        $content_object = ContentObjectImport::launch($this);
        
        $dom_xpath = $this->get_controller()->get_dom_xpath();
        $content_object_node = $this->get_content_object_import_parameters()->get_content_object_node();
        
        $export_node = $dom_xpath->query(CpoExportImplementation::SURVEY_MATRIX_QUESTION_EXPORT, $content_object_node)->item(
            0);
        
        $option_node_list = $dom_xpath->query(CpoExportImplementation::OPTIONS_NODE, $export_node)->item(0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::OPTION_NODE, $option_node_list) as $option_node)
        {
            
            $this->get_controller()->set_cache_id(
                MatrixOption::class_name(), 
                MatrixOption::PROPERTY_ID, 
                $option_node->getAttribute('id'), 
                $option_node->getAttribute('display_order'));
            $option = new MatrixOption();
            $option->set_value($option_node->getAttribute('value'));
            $option->set_display_order($option_node->getAttribute('display_order'));
            $content_object->add_option($option);
        }
        
        $match_node_list = $dom_xpath->query(CpoExportImplementation::MATCHES_NODE, $export_node)->item(0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::MATCH_NODE, $match_node_list) as $match_node)
        {
            $this->get_controller()->set_cache_id(
                MatrixMatch::class_name(), 
                MatrixMatch::PROPERTY_ID, 
                $match_node->getAttribute('id'), 
                $match_node->getAttribute('display_order'));
            $match = new MatrixMatch();
            $match->set_value($match_node->getAttribute('value'));
            $match->set_display_order($match_node->getAttribute('display_order'));
            $content_object->add_match($match);
        }
        
        return $content_object;
    }

    function post_import($content_object)
    {
        $dom_xpath = $this->get_controller()->get_dom_xpath();
        
        $content_object_node = $this->get_content_object_import_parameters()->get_content_object_node();
        
        $export_node = $dom_xpath->query(CpoExportImplementation::SURVEY_MATRIX_QUESTION_EXPORT, $content_object_node)->item(
            0);
        
        $option_node_list = $dom_xpath->query(CpoExportImplementation::OPTIONS_NODE, $export_node)->item(0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::OPTION_NODE, $option_node_list) as $option_node)
        {
            $display_order = $this->get_controller()->get_cache_id(
                MatrixOption::class_name(), 
                MatrixOption::PROPERTY_ID, 
                $option_node->getAttribute('id'));
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatrixOption::class_name(), MatrixOption::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($content_object->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatrixOption::class_name(), MatrixOption::PROPERTY_DISPLAY_ORDER), 
                new StaticConditionVariable($display_order));
            $condition = new AndCondition($conditions);
            
            $option = DataManager::retrieve(MatrixOption::class_name(), new DataClassRetrieveParameters($condition));
            
            if ($option instanceof MatrixOption)
            {
                $this->get_controller()->set_cache_id(
                    MatrixOption::class_name(), 
                    MatrixOption::PROPERTY_ID, 
                    $option_node->getAttribute('id'), 
                    $option->get_id());
            }
            else
            {
                $this->get_controller()->set_cache_id(
                    MatrixOption::class_name(), 
                    MatrixOption::PROPERTY_ID, 
                    $option_node->getAttribute('id'), 
                    null);
            }
        }
        
        $match_node_list = $dom_xpath->query(CpoExportImplementation::MATCHES_NODE, $export_node)->item(0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::MATCH_NODE, $match_node_list) as $match_node)
        {
            $display_order = $this->get_controller()->get_cache_id(
                MatrixMatch::class_name(), 
                MatrixMatch::PROPERTY_ID, 
                $match_node->getAttribute('id'));
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatrixMatch::class_name(), MatrixMatch::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($content_object->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatrixMatch::class_name(), MatrixMatch::PROPERTY_DISPLAY_ORDER), 
                new StaticConditionVariable($display_order));
            $condition = new AndCondition($conditions);
            
            $match = DataManager::retrieve(MatrixMatch::class_name(), new DataClassRetrieveParameters($condition));
            
            if ($match)
            {
                $this->get_controller()->set_cache_id(
                    MatrixMatch::class_name(), 
                    MatrixMatch::PROPERTY_ID, 
                    $match_node->getAttribute('id'), 
                    $match->get_id());
            }
            else
            {
                $this->get_controller()->set_cache_id(
                    MatrixMatch::class_name(), 
                    MatrixMatch::PROPERTY_ID, 
                    $match_node->getAttribute('id'), 
                    null);
            }
        }
    }
}