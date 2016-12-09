<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Preview\Storage\DataClass;

class Answer implements
    \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Reporting\Answer
{
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_OPTION_ID = 'option_id';
    const PROPERTY_MATCH_ID = 'match_id';

    private $properties = array();

    function get_question_id()
    {
        return $this->properties[self :: PROPERTY_QUESTION_ID];
    }

    function set_question_id($question_id)
    {
        $this->properties[self :: PROPERTY_QUESTION_ID] = $question_id;
    }

    function get_option_id()
    {
        return $this->properties[self :: PROPERTY_OPTION_ID];
    }

    function set_option_id($option_id)
    {
        $this->properties[self :: PROPERTY_OPTION_ID] = $option_id;
    }

    function get_match_id()
    {
        return $this->properties[self :: PROPERTY_MATCH_ID];
    }

    function set_match_id($match_id)
    {
        $this->properties[self :: PROPERTY_MATCH_ID] = $match_id;
    }
}
?>