<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Integration\Chamilo\Core\Reporting\Preview\Storage\DataClass;

class Answer implements \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Integration\Chamilo\Core\Reporting\Interfaces\Answer
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_GENDER_ID = 'gender_id';

    private $properties = array();

    function get_question_id()
    {
        return $this->properties[self :: PROPERTY_QUESTION_ID];
    }

    function set_question_id($question_id)
    {
        $this->properties[self :: PROPERTY_QUESTION_ID] = $question_id;
    }

    function get_gender_id()
    {
        return $this->properties[self :: PROPERTY_GENDER_ID];
    }

    function set_gender_id($gender_id)
    {
        $this->properties[self :: PROPERTY_GENDER_ID] = $gender_id;
    }
}
?>