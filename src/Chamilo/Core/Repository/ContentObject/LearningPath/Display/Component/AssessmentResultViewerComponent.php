<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentResultViewerComponent extends BaseReportingComponent
{

    /**
     *
     * @return string
     */
    function build()
    {
        $this->addBreadcrumbs(Translation::getInstance());

        $this->getRequest()->query->set(
            \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION,
            Manager::ACTION_VIEW_ASSESSMENT_RESULT);

        return $this->getApplicationFactory()->getApplication(
            'Chamilo\Core\Repository\ContentObject\Assessment\Display',
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    /**
     *
     * @return string
     */
    public function render_header()
    {
        $html = [parent::render_header()];
        $html[] = $this->renderCommonFunctionality();

        return implode(PHP_EOL, $html);
    }

    /**
     * Adds the breadcrumbs for this component
     *
     * @param Translation $translator
     */
    protected function addBreadcrumbs(Translation $translator)
    {
        $trail = BreadcrumbTrail::getInstance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS),
                    array(self::PARAM_REPORTING_USER_ID)),
                $translator->getTranslation('UserProgressComponent')));

        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_REPORTING)),
                $translator->getTranslation(
                    'ReportingComponent',
                    array('USER' => $this->getReportingUser()->get_fullname()))));

        $trail->add(
            new Breadcrumb(
                $this->get_url(),
                $translator->getTranslation(
                    'AssessmentResultViewerComponent',
                    array('USER' => $this->getReportingUser()->get_fullname()))));
    }

    /**
     * Retrieves the results for the assessment attempt.
     *
     * @return array The assessment attempt results
     */
    public function retrieve_assessment_results()
    {
        $trackingService = $this->getTrackingService();
        $questionAttempts = $trackingService->getQuestionAttempts(
            parent::get_root_content_object(),
            $this->getReportingUser(),
            $this->getCurrentTreeNode(),
            $this->getTreeNodeAttemptId());

        $results = [];

        foreach ($questionAttempts as $questionAttempt)
        {
            $results[$questionAttempt->get_question_complex_id()] = array(
                'answer' => $questionAttempt->get_answer(),
                'feedback' => $questionAttempt->get_feedback(),
                'score' => $questionAttempt->get_score(),
                'hint' => $questionAttempt->get_hint());
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
        $this->trackingService->changeQuestionScoreAndFeedback(
            parent::get_root_content_object(),
            $this->getReportingUser(),
            $this->getCurrentTreeNode(),
            $this->getTreeNodeAttemptId(),
            $question_cid,
            $score,
            $feedback);
    }

    /**
     * Updates the score of the assessment attempt in this learning path.
     */
    public function change_total_score($score)
    {
        $this->trackingService->changeAssessmentScore(
            parent::get_root_content_object(),
            $this->getReportingUser(),
            $this->getCurrentTreeNode(),
            $this->getTreeNodeAttemptId(),
            $score);
    }

    /**
     *
     * @return Configuration
     */
    public function get_assessment_configuration()
    {
        return $this->getCurrentTreeNode()->getTreeNodeData()->getAssessmentConfiguration();
    }

    /**
     *
     * @return bool
     */
    public function can_change_answer_data()
    {
        return $this->is_allowed_to_edit_attempt_data();
    }

    /**
     *
     * @return array
     */
    public function get_assessment_parameters()
    {
        return [];
    }

    public function get_additional_parameters()
    {
        $parameters = parent::get_additional_parameters();
        $parameters[] = self::PARAM_ITEM_ATTEMPT_ID;
        return $parameters;
    }

    public function get_root_content_object()
    {
        return $this->getCurrentTreeNode()->getContentObject();
    }

    /**
     *
     * @return int
     */
    protected function getTreeNodeAttemptId()
    {
        return (int) $this->getRequest()->get(self::PARAM_ITEM_ATTEMPT_ID);
    }
}