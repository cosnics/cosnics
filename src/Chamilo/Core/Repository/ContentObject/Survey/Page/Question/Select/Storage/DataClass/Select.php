<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataManager;
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
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class Select extends ContentObject implements Versionable
{
    const PROPERTY_ANSWER_TYPE = 'answer_type';
    const ANSWER_TYPE_RADIO = 1;
    const ANSWER_TYPE_CHECKBOX = 2;
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';

    static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
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
        }
        return $success;
    }

    public function get_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(SelectOption :: class_name(), SelectOption :: PROPERTY_QUESTION_ID),
            new StaticConditionVariable($this->get_id()));

        $order = new OrderBy(
            new PropertyConditionVariable(SelectOption :: class_name(), SelectOption :: PROPERTY_DISPLAY_ORDER));

        return DataManager :: retrieves(
            SelectOption :: class_name(),
            new DataClassRetrievesParameters($condition, null, null, array($order)));
    }

    public function get_number_of_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(SelectOption :: class_name(), SelectOption :: PROPERTY_QUESTION_ID),
            new StaticConditionVariable($this->get_id()));

        return DataManager :: count(SelectOption :: class_name(), new DataClassCountParameters($condition));
    }

    public function add_option(SelectOption $option)
    {
        $this->options[] = $option;
    }

    public function get_answer_type()
    {
        return $this->get_additional_property(self :: PROPERTY_ANSWER_TYPE);
    }

    public function set_answer_type($answer_type)
    {
        return $this->set_additional_property(self :: PROPERTY_ANSWER_TYPE, $answer_type);
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

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ANSWER_TYPE, self :: PROPERTY_QUESTION, self :: PROPERTY_INSTRUCTION);
    }

    public function has_instruction()
    {
        $instruction = $this->get_instruction();
        return ($instruction != '<p>&#160;</p>' && count($instruction) > 0);
    }
}
?>