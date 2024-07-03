<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.assessment.component
 */
class ResultsDeleterComponent extends Manager
{

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            throw new NotAllowedException();
        }

        if ($this->getRequest()->query->has(self::PARAM_USER_ASSESSMENT))
        {
            $uaid = $this->getRequest()->query->get(self::PARAM_USER_ASSESSMENT);

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ID
                ), new StaticConditionVariable($uaid)
            );

            $item = DataManager::retrieve(
                AssessmentAttempt::class, new StorageParameters(condition: $condition)
            );

            if ($item)
            {
                $redirect_aid = $item->get_assessment_id();
            }

            $this->delete_user_assessment_results($item);
        }
        elseif ($this->getRequest()->query->has(self::PARAM_ASSESSMENT))
        {
            $aid = $this->getRequest()->query->get(self::PARAM_ASSESSMENT);
            $redirect_aid = $aid;
            $this->delete_assessment_results($aid);
        }

        $params = [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_RESULTS];

        if (isset($redirect_aid))
        {
            $params[self::PARAM_ASSESSMENT] = $redirect_aid;
        }

        $this->redirectWithMessage(Translation::get('ResultsDeleted'), false, $params);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    public function delete_assessment_results($aid)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID
            ), new StaticConditionVariable($aid)
        );

        $items = DataManager::retrieves(
            AssessmentAttempt::class, new StorageParameters(condition: $condition)
        );

        foreach ($items as $assessment_attempt)
        {
            $this->delete_user_assessment_results($assessment_attempt);
        }
    }

    public function delete_user_assessment_results($user_assessment)
    {
        if ($user_assessment != null)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    QuestionAttempt::class, QuestionAttempt::PROPERTY_ASSESSMENT_ATTEMPT_ID
                ), new StaticConditionVariable($user_assessment->get_id())
            );

            $items = DataManager::retrieves(
                QuestionAttempt::class, new StorageParameters(condition: $condition)
            );

            foreach ($items as $question_attempt)
            {
                $question_attempt->delete();
            }

            $user_assessment->delete();
        }
    }
}
