<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\survey_page
 */
class ComplexContentObjectPathNode extends \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
{
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_QUESTION_NR = 'question_nr';
    const PROPERTY_IS_QUESTION = 'is_question';
    const PROPERTY_QUESTION_MAX_ANSWER_COUNT = 'max_question_answer_count';
    const PROPERTY_NODE_IN_MENU = 'node_in_menu';
    
    
    function set_next_page_step($step)
    {
        return $this->set_property(self :: PROPERTY_NEXT_PAGE_STEP, $step);
    }
    
    function set_question($question)
    {
        return $this->set_property(self :: PROPERTY_QUESTION, $question);
    }

    function get_question()
    {
        return $this->get_property(self :: PROPERTY_QUESTION);
    }

    function set_question_nr($question_nr)
    {
        return $this->set_property(self :: PROPERTY_QUESTION_NR, $question_nr);
    }

    function get_question_nr()
    {
        return $this->get_property(self :: PROPERTY_QUESTION_NR);
    }

    function set_is_question($question)
    {
        return $this->set_property(self :: PROPERTY_IS_QUESTION, $question);
    }

    function is_question()
    {
        return $this->get_property(self :: PROPERTY_IS_QUESTION);
    }

    function set_question_max_answer_count($max_answer_count)
    {
        return $this->set_property(self :: PROPERTY_QUESTION_MAX_ANSWER_COUNT, $max_answer_count);
    }

    function get_question_max_answer_count()
    {
        return $this->get_property(self :: PROPERTY_QUESTION_MAX_ANSWER_COUNT);
    }
}