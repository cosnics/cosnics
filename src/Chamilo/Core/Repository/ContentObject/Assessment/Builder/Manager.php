<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder;

/**
 * $Id: assessment_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.assessment
 */
abstract class Manager extends \Chamilo\Core\Repository\Builder\Manager
{
    // Actions
    const ACTION_MERGE_ASSESSMENT = 'assessment_merger';
    const ACTION_SELECT_QUESTIONS = 'question_selecter';
    const ACTION_RANDOMIZE = 'randomizer';
    const ACTION_ANSWER_FEEDBACK_TYPE = 'answer_feedback_type';

    // Parameters
    const PARAM_ADD_SELECTED_QUESTIONS = 'add_selected_questions';
    const PARAM_QUESTION_ID = 'question';
    const PARAM_ASSESSMENT_ID = 'assessment';
    const PARAM_ANSWER_FEEDBACK_TYPE = 'answer_feedback_type';
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';
}
