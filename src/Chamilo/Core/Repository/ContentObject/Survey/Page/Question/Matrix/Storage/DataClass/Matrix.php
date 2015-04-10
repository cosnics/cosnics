<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataManager;
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
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class Matrix extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_MATRIX_TYPE = 'matrix_type';
    const MATRIX_TYPE_RADIO = 1;
    const MATRIX_TYPE_CHECKBOX = 2;
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';

    private $options;

    private $matches;

    static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    public function create()
    {
        $success = parent :: create();
        
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
            new PropertyConditionVariable(
                MatrixOption :: class_name(), 
                MatrixOption :: PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $order = new OrderBy(
            new PropertyConditionVariable(
                MatrixOption :: class_name(), 
                MatrixOption :: PROPERTY_DISPLAY_ORDER));
        
        return DataManager :: retrieves(
            MatrixOption :: class_name(), 
            new DataClassRetrievesParameters($condition, null, null, array($order)));
    }

    public function get_number_of_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                MatrixOption :: class_name(), 
                MatrixOption :: PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return DataManager :: count(
            MatrixOption :: class_name(), 
            new DataClassCountParameters($condition));
    }

    public function get_matches()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                MatrixMatch :: class_name(), 
                MatrixMatch :: PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $order = new OrderBy(
            new PropertyConditionVariable(
                MatrixMatch :: class_name(), 
                MatrixMatch :: PROPERTY_DISPLAY_ORDER));
        
        return DataManager :: retrieves(
            MatrixMatch :: class_name(), 
            new DataClassRetrievesParameters($condition, null, null, array($order)));
    }

    public function get_number_of_matches()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                MatrixMatch :: class_name(), 
                MatrixMatch :: PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return DataManager :: count(MatrixMatch :: class_name(), new DataClassCountParameters($condition));
    }

    public function get_matrix_type()
    {
        return $this->get_additional_property(self :: PROPERTY_MATRIX_TYPE);
    }

    public function set_matrix_type($matrix_type)
    {
        $this->set_additional_property(self :: PROPERTY_MATRIX_TYPE, $matrix_type);
    }

    public function add_option(MatrixOption $option)
    {
        $this->options[] = $option;
    }

    public function add_match(MatrixMatch $match)
    {
        $this->matches[] = $match;
    }

    public function get_question()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTION);
    }

    public function set_question($question)
    {
        return $this->set_additional_property(self :: PROPERTY_QUESTION, $question);
    }

    public function get_instruction()
    {
        return $this->get_additional_property(self :: PROPERTY_INSTRUCTION);
    }

    public function set_instruction($instruction)
    {
        return $this->set_additional_property(self :: PROPERTY_INSTRUCTION, $instruction);
    }

    public function has_instruction()
    {
        $instruction = $this->get_instruction();
        return ($instruction != '<p>&#160;</p>' && count($instruction) > 0);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_MATRIX_TYPE, self :: PROPERTY_QUESTION, self :: PROPERTY_INSTRUCTION);
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
        
        return parent :: delete($only_version);
    }
}