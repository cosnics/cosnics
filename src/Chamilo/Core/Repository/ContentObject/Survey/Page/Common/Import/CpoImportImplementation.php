<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Common\Export\CpoExportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Common\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass\Rating;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\Select;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class CpoImportImplementation extends ImportImplementation
{

    function import()
    {
        $content_object = ContentObjectImport::launch($this);
        return $content_object;
    }

    function post_import($content_object)
    {
        $dom_xpath = $this->get_controller()->get_dom_xpath();
        $content_object_node = $this->get_content_object_import_parameters()->get_content_object_node();
        
        $export_node = $dom_xpath->query(CpoExportImplementation::PAGE_CONFIGURATIONS, $content_object_node)->item(0);
        
        $configuration_node_list = $dom_xpath->query(CpoExportImplementation::CONFIGURATIONS_NODE, $export_node)->item(
            0);
        
        foreach ($dom_xpath->query(CpoExportImplementation::CONFIGURATION_NODE, $configuration_node_list) as $configuration_node)
        {
            
            $configuration = new Configuration();
            
            foreach (Configuration::get_default_property_names() as $property)
            {
                $configuration->set_default_property($property, $configuration_node->getAttribute($property));
            }
            
            $configuration->setPageId($content_object->get_id());
            
            $complex_question_id = $configuration->getComplexQuestionId();
            $new_complex_question_id = $this->get_controller()->get_complex_content_object_item_id_cache_id(
                $complex_question_id);
            $configuration->setComplexQuestionId($new_complex_question_id);
            
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class_name(), 
                    ComplexContentObjectItem::PROPERTY_ID), 
                new StaticConditionVariable($new_complex_question_id));
            
            $complex_question = \Chamilo\Core\Repository\Storage\DataManager::retrieve(
                ComplexContentObjectItem::class_name(), 
                new DataClassRetrieveParameters($condition));
            $question = $complex_question->get_ref_object();
            $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($question);
            $class = ClassnameUtilities::getInstance()->getClassnameFromObject($question, true);
            
            $to_visible_question_ids = $configuration->getToVisibleQuestionIds();
            $new_to_vivible_question_ids = array();
            foreach ($to_visible_question_ids as $to_visible_question_id)
            {
                $new_to_vivible_question_ids[] = $this->get_controller()->get_complex_content_object_item_id_cache_id(
                    $to_visible_question_id);
            }
            $configuration->setToVisibleQuestionIds($new_to_vivible_question_ids);
            
            if ($question instanceof MultipleChoice)
            {
                
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                $class = $class . 'Option';
                foreach ($answer_matches as $key => $answer_match)
                {
                    $ids = explode('_', $key);
                    $new_keys = array();
                    $new_keys[] = $new_complex_question_id;
                    $class = (string) StringUtilities::getInstance()->createString($class)->upperCamelize();
                    $option_id = $this->get_controller()->get_cache_id(
                        (string) StringUtilities::getInstance()->createString($class)->underscored(), 
                        'id', 
                        $ids[1]);
                    $new_keys[] = $option_id;
                    $new_key = implode('_', $new_keys);
                    $new_answer_matches[$new_key] = $option_id;
                }
                $configuration->setAnswerMatches($new_answer_matches);
            }
            elseif ($question instanceof Select)
            {
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                $class = $class . 'Option';
                foreach ($answer_matches as $key => $answer_match)
                {
                    $option_id = $this->get_controller()->get_cache_id(
                        (string) StringUtilities::getInstance()->createString($class)->underscored(), 
                        'id', 
                        $answer_match);
                    $new_answer_matches[$new_complex_question_id] = $option_id;
                }
                $configuration->setAnswerMatches($new_answer_matches);
            }
            elseif ($question instanceof Matrix || $question instanceof Matching)
            {
                
                $class = (string) StringUtilities::getInstance()->createString($class)->upperCamelize();
                $optionClass = $question->class_name() . 'Option';
                $matchClass = $question->class_name() . 'Match';
                $answer_matches = $configuration->getAnswerMatches();
                
                $new_answer_matches = array();
                foreach ($answer_matches as $key => $answer_match)
                {
                    $ids = explode('_', $key);
                    $new_keys = array();
                    $new_keys[] = $new_complex_question_id;
                    
                    $option_id = $this->get_controller()->get_cache_id($optionClass, 'id', $ids[1]);
                    $new_keys[] = $option_id;
                    $new_key = implode('_', $new_keys);
                    
                    $match_id = $this->get_controller()->get_cache_id($matchClass, 'id', $answer_match);
                    $new_answer_matches[$new_key] = $match_id;
                }
                
                $configuration->setAnswerMatches($new_answer_matches);
            }
            elseif ($question instanceof Rating)
            {
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                foreach ($answer_matches as $key => $answer_match)
                {
                    $new_answer_matches[$new_complex_question_id] = $answer_match;
                }
                $configuration->setAnswerMatches($new_answer_matches);
            }
            
            $configuration->setCreated(time());
            $configuration->setUpdated(time());
            $configuration->create();
        }
    }
}
?>