<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an open question
 */
class Rating extends ContentObject implements Versionable
{
    const PROPERTY_LOW = 'low';
    const PROPERTY_HIGH = 'high';
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';

    static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    function get_low()
    {
        return $this->get_additional_property(self::PROPERTY_LOW);
    }

    function get_high()
    {
        return $this->get_additional_property(self::PROPERTY_HIGH);
    }

    function set_low($value)
    {
        $this->set_additional_property(self::PROPERTY_LOW, $value);
    }

    function set_high($value)
    {
        $this->set_additional_property(self::PROPERTY_HIGH, $value);
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
        return array(self::PROPERTY_LOW, self::PROPERTY_HIGH, self::PROPERTY_QUESTION, self::PROPERTY_INSTRUCTION);
    }
}

?>