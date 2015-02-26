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
    const CLASS_NAME = __CLASS__;
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';
    const PROPERTY_DATE = 'date';
    const PROPERTY_TIME = 'time';

    static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
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

    public function get_date()
    {
        return $this->get_additional_property(self :: PROPERTY_DATE);
    }

    public function set_date($date)
    {
        return $this->set_additional_property(self :: PROPERTY_DATE, $date);
    }

    public function get_time()
    {
        return $this->get_additional_property(self :: PROPERTY_TIME);
    }

    public function set_time($time)
    {
        return $this->set_additional_property(self :: PROPERTY_TIME, $time);
    }

    static function get_additional_property_names()
    {
        return array(
            self :: PROPERTY_QUESTION, 
            self :: PROPERTY_INSTRUCTION, 
            self :: PROPERTY_DATE, 
            self :: PROPERTY_TIME);
    }
}

?>