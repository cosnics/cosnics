<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.content_object.survey_matching_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class Matching extends ContentObject implements Versionable
{
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';

    private $options;

    private $matches;

    static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function create()
    {
        $success = parent::create();
        
        if ($success)
        {
            foreach ($this->options as $option)
            {
                $option->set_question_id($this->get_id());
                $option->create();
            }
            
            foreach ($this->matches as $match)
            {
                $match->set_question_id($this->get_id());
                $match->create();
            }
        }
        return $success;
    }

    public function get_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $order = new OrderBy(
            new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_DISPLAY_ORDER));
        
        return DataManager::retrieves(
            MatchingOption::class_name(), 
            new DataClassRetrievesParameters($condition, null, null, array($order)));
    }

    public function get_number_of_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return DataManager::count(MatchingOption::class_name(), new DataClassCountParameters($condition));
    }

    public function get_matches()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $order = new OrderBy(
            new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_DISPLAY_ORDER));
        
        return DataManager::retrieves(
            MatchingMatch::class_name(), 
            new DataClassRetrievesParameters($condition, null, null, array($order)));
    }

    public function get_number_of_matches()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return DataManager::count(MatchingMatch::class_name(), new DataClassCountParameters($condition));
    }

    public function add_option(MatchingOption $option)
    {
        $this->options[] = $option;
    }

    public function add_match(MatchingMatch $match)
    {
        $this->matches[] = $match;
    }

    public function get_question()
    {
        return $this->get_additional_property(self::PROPERTY_QUESTION);
    }

    public function set_question($question)
    {
        return $this->set_additional_property(self::PROPERTY_QUESTION, $question);
    }

    public function get_instruction()
    {
        return $this->get_additional_property(self::PROPERTY_INSTRUCTION);
    }

    public function set_instruction($instruction)
    {
        return $this->set_additional_property(self::PROPERTY_INSTRUCTION, $instruction);
    }

    public function has_instruction()
    {
        $instruction = $this->get_instruction();
        return ($instruction != '<p>&#160;</p>' && count($instruction) > 0);
    }

    static function get_additional_property_names()
    {
        return array(self::PROPERTY_QUESTION, self::PROPERTY_INSTRUCTION);
    }

    public function delete($only_version = false)
    {
        foreach ($this->get_options()->as_array() as $option)
        {
            if (! $option->delete())
            {
                return false;
            }
        }
        
        foreach ($this->get_matches()->as_array() as $match)
        {
            if (! $match->delete())
            {
                return false;
            }
        }
        
        return parent::delete($only_version);
    }
}