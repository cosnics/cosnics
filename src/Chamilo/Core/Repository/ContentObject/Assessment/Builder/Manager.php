<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder;

/**
 * @package repository.lib.complex_builder.assessment
 */
abstract class Manager extends \Chamilo\Core\Repository\Builder\Manager
{
    // Actions
    public const ACTION_ANSWER_FEEDBACK_TYPE = 'AnswerFeedbackType';
    public const ACTION_MERGE_ASSESSMENT = 'AssessmentMerger';
    public const ACTION_RANDOMIZE = 'Randomizer';
    public const ACTION_SELECT_QUESTIONS = 'QuestionSelecter';

    public const CONTEXT = __NAMESPACE__;

    // Parameters
    public const PARAM_ADD_SELECTED_QUESTIONS = 'add_selected_questions';
    public const PARAM_ANSWER_FEEDBACK_TYPE = 'answer_feedback_type';
    public const PARAM_ASSESSMENT_ID = 'assessment';
    public const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';
    public const PARAM_QUESTION_ID = 'question';
}
