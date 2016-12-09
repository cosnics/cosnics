<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * A Open
 */
class DateTime extends ContentObject implements Versionable
{
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';
    const PROPERTY_QUESTION_TYPE = 'question_type';
    const TYPE_DATE = 1;
    const TYPE_TIME = 2;

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

    static function get_additional_property_names()
    {
        return array(self::PROPERTY_QUESTION, self::PROPERTY_INSTRUCTION, self::PROPERTY_QUESTION_TYPE);
    }
}

?>