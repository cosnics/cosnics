<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * A Open
 */
class Choice extends ContentObject implements Versionable
{
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';
    const PROPERTY_QUESTION_TYPE = 'question_type';
    const PROPERTY_FIRST_CHOICE = 'first_choice';
    const PROPERTY_SECOND_CHOICE = 'second_choice';
    const TYPE_YES_NO = 0;
    const TYPE_OTHER = 1;

    static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
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

    public function get_question_type()
    {
        return $this->get_additional_property(self::PROPERTY_QUESTION_TYPE);
    }

    public function set_question_type($question_type)
    {
        return $this->set_additional_property(self::PROPERTY_QUESTION_TYPE, $question_type);
    }

    public function get_first_choice()
    {
        return $this->get_additional_property(self::PROPERTY_FIRST_CHOICE);
    }

    public function set_first_choice($first_choice)
    {
        return $this->set_additional_property(self::PROPERTY_FIRST_CHOICE, $first_choice);
    }

    public function get_second_choice()
    {
        return $this->get_additional_property(self::PROPERTY_SECOND_CHOICE);
    }

    public function set_second_choice($second_choice)
    {
        return $this->set_additional_property(self::PROPERTY_SECOND_CHOICE, $second_choice);
    }

    public function choices()
    {
        if (self::PROPERTY_FIRST_CHOICE && self::PROPERTY_SECOND_CHOICE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getOptions()
    {
        $options = array();
        
        if ($this->get_question_type() == self::TYPE_YES_NO)
        {
            $options[1] = Translation::get('AnswerYes');
            $options[2] = Translation::get('AnswerNo');
        }
        else
        {
            $options[1] = $this->get_first_choice();
            $options[2] = $this->get_second_choice();
        }
        
        return $options;
    }

    static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_QUESTION, 
            self::PROPERTY_INSTRUCTION, 
            self::PROPERTY_QUESTION_TYPE, 
            self::PROPERTY_FIRST_CHOICE, 
            self::PROPERTY_SECOND_CHOICE);
    }
}

?>