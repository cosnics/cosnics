<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentResultViewerComponent extends TabComponent
{
    /**
     * @return string
     */
    function build()
    {
        $this->getRequest()->query->set(
            \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION,
            \Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager::ACTION_VIEW_ASSESSMENT_RESULT
        );

        $factory = new ApplicationFactory(
            'Chamilo\Core\Repository\ContentObject\Assessment\Display',
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        );

        return $factory->run();
    }

    /**
     * Retrieves the results for the assessment attempt.
     *
     * @return array The assessment attempt results
     */
    public function retrieve_assessment_results()
    {
        $trackingService = $this->getLearningPathTrackingService();
        $questionAttempts = $trackingService->getQuestionAttempts(
            $this->get_root_content_object(), $this->getUser(), $this->getCurrentLearningPathTreeNode(),
            $this->getLearningPathChildAttemptId()
        );

        $results = [];

        foreach ($questionAttempts as $questionAttempt)
        {
            $results[$questionAttempt->get_question_complex_id()] = array(
                'answer' => $questionAttempt->get_answer(),
                'feedback' => $questionAttempt->get_feedback(),
                'score' => $questionAttempt->get_score(),
                'hint' => $questionAttempt->get_hint()
            );
        }

        return $results;
    }

    /**
     * Updates the question attempts of the assessment.
     *
     * @param int $question_cid The complex question id
     * @param int $score The score
     * @param string $feedback The feedback
     */
    public function change_answer_data($question_cid, $score, $feedback)
    {
        $this->learningPathTrackingService->changeQuestionScoreAndFeedback(
            $this->get_root_content_object(), $this->getUser(), $this->getCurrentLearningPathTreeNode(),
            $this->getLearningPathChildAttemptId(), $question_cid, $score, $feedback
        );
    }

    /**
     * Updates the score of the assessment attempt in this learning path.
     */
    public function change_total_score($score)
    {
        $this->learningPathTrackingService->changeAssessmentScore(
            $this->get_root_content_object(), $this->getUser(), $this->getCurrentLearningPathTreeNode(),
            $this->getLearningPathChildAttemptId(), $score
        );
    }

    /**
     * @return Configuration
     */
    public function get_assessment_configuration()
    {
        return $this->getCurrentLearningPathTreeNode()->getLearningPathChild()->getAssessmentConfiguration();
    }

    /**
     * @return bool
     */
    public function can_change_answer_data()
    {
        return $this->is_allowed_to_edit_attempt_data();
    }

    /**
     * @return array
     */
    public function get_assessment_parameters()
    {
        return array();
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID, self::PARAM_FULL_SCREEN, self::PARAM_ITEM_ATTEMPT_ID);
    }

    /**
     * @return int
     */
    protected function getLearningPathChildAttemptId()
    {
        return (int) $this->getRequest()->get(self::PARAM_ITEM_ATTEMPT_ID);
    }
}