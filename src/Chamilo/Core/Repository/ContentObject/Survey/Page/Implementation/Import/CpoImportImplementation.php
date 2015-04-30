<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Implementation\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Implementation\ImportImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass\Rating;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\Select;
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
        $content_object = ContentObjectImport :: launch($this);
        return $content_object;
    }

    function post_import($content_object)
    {
        $configs = $content_object->getConfiguration();
        
        foreach ($configs as $key_index => $configuration)
        {
            $complex_question_id = $configuration->getComplexQuestionId();
            $new_complex_question_id = $this->get_controller()->get_complex_content_object_item_id_cache_id(
                $complex_question_id);
            $configuration->setComplexQuestionId($new_complex_question_id);
            
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_ID), 
                new StaticConditionVariable($new_complex_question_id));
            
            $complex_question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve(
                ComplexContentObjectItem :: class_name(), 
                new DataClassRetrieveParameters($condition));
            $question = $complex_question->get_ref_object();
            $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromObject($question);
            $class = ClassnameUtilities :: getInstance()->getClassnameFromObject($question, true);
            
            $to_visible_question_ids = $configuration->getToVisibleQuestionIds();
            $new_to_vivible_question_ids = array();
            foreach ($to_visible_question_ids as $to_visible_question_id)
            {
                $new_to_vivible_question_ids[] = $this->get_controller()->get_complex_content_object_item_id_cache_id(
                    $to_visible_question_id);
            }
            $configuration->setToVisibleQuestionIds($new_to_vivible_question_ids);
            
            if ($class == MultipleChoice :: class_name())
            {
                
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                $class = $class . 'Option';
                foreach ($answer_matches as $key => $answer_match)
                {
                    $ids = explode('_', $key);
                    $new_keys = array();
                    $new_keys[] = $new_complex_question_id;
                    $class = (string) StringUtilities :: getInstance()->createString($class)->upperCamelize();
                    $option_id = $this->get_controller()->get_cache_id(
                        (string) StringUtilities :: getInstance()->createString($class)->underscored(), 
                        'id', 
                        $ids[1]);
                    $new_keys[] = $option_id;
                    $new_key = implode('_', $new_keys);
                    $new_answer_matches[$new_key] = $option_id;
                }
                $configuration->setAnswerMatches($new_answer_matches);
            }
            elseif ($class == Select :: get_type_name())
            {
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                $class = $class . 'Option';
                foreach ($answer_matches as $key => $answer_match)
                {
                    $option_id = $this->get_controller()->get_cache_id(
                        (string) StringUtilities :: getInstance()->createString($class)->underscored(), 
                        'id', 
                        $answer_match);
                    $new_answer_matches[$new_complex_question_id] = $option_id;
                }
                $configuration->setAnswerMatches($new_answer_matches);
            }
            elseif ($class == Matrix :: class_name() || $class == Matching :: class_name())
            {
                
                $class = (string) StringUtilities :: getInstance()->createString($class)->upperCamelize();
                $option_class = $class . 'Option';
                $match_class = $class . 'Match';
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                foreach ($answer_matches as $key => $answer_match)
                {
                    $ids = explode('_', $key);
                    $new_keys = array();
                    $new_keys[] = $new_complex_question_id;
                    
                    $option_id = $this->get_controller()->get_cache_id(
                        (string) StringUtilities :: getInstance()->createString($option_class)->underscored(), 
                        'id', 
                        $ids[1]);
                    $new_keys[] = $option_id;
                    $new_key = implode('_', $new_keys);
                    
                    $match_id = $this->get_controller()->get_cache_id(
                        (string) StringUtilities :: getInstance()->createString($match_class)->underscored(), 
                        'id', 
                        $answer_match);
                    $new_answer_matches[$new_key] = $match_id;
                }
                $configuration->setAnswerMatches($new_answer_matches);
            }
            elseif ($class == Rating :: class_name())
            {
                $answer_matches = $configuration->getAnswerMatches();
                $new_answer_matches = array();
                foreach ($answer_matches as $key => $answer_match)
                {
                    $new_answer_matches[$new_complex_question_id] = $answer_match;
                }
                $configuration->setAnswerMatches($new_answer_matches);
                
                $configuration->setCreated(time());
                $configuration->setUpdated(time());
                $configuration->update();
            }
        }
    }
}
?>