<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentAttemptStatus as WeblcmsPeerAssessmentAttemptStatusTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentFeedback as WeblcmsPeerAssessmentFeedbackTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentScore as WeblcmsPeerAssessmentScoreTracker;
use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * Enter description here .
 * ..
 *
 * @author Renaat De Muynck
 */
class DeleterComponent extends Manager
{

    function run()
    {
        $publication_ids = $this->getRequest()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        if (! isset($publication_ids))
        {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication',
                    array(),
                    'Chamilo\Application\Weblcms'));
        }

        if (! is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        foreach ($publication_ids as $pub_id)
        {

            // delete groups
            $groups = $this->get_groups($pub_id);
            $success = true;

            foreach ($groups as $group)
            {
                // delete enrollments
                $success &= $this->remove_user_from_group(null, $group->get_id());
                // deletegroup
                $success &= $this->delete_group($group->get_id());
            }

            // delete attempts
            $attempts = $this->get_attempts($pub_id);

            foreach ($attempts as $attempt)
            {

                $status_tracker = new WeblcmsPeerAssessmentAttemptStatusTracker();
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        WeblcmsPeerAssessmentAttemptStatusTracker::class_name(),
                        WeblcmsPeerAssessmentAttemptStatusTracker::PROPERTY_ATTEMPT_ID),
                    new StaticConditionVariable($attempt->get_id()));

                $statuses = DataManager::retrieves(
                    WeblcmsPeerAssessmentAttemptStatusTracker::class_name(),
                    new DataClassRetrievesParameters($condition))->as_array();

                // delete scores and feedaback
                foreach ($statuses as $status)
                {

                    // delete scores

                    $tracker = new WeblcmsPeerAssessmentScoreTracker();
                    $success &= $tracker->remove(
                        new AndCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(
                                    WeblcmsPeerAssessmentScoreTracker::class_name(),
                                    WeblcmsPeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
                                new StaticConditionVariable($status->get_id()))));

                    $tracker = new WeblcmsPeerAssessmentFeedbackTracker();
                    $success &= $tracker->remove(
                        new AndCondition(
                            new EqualityCondition(
                                new PropertyConditionVariable(
                                    WeblcmsPeerAssessmentFeedbackTracker::class_name(),
                                    WeblcmsPeerAssessmentFeedbackTracker::PROPERTY_ATTEMPT_STATUS_ID),
                                new StaticConditionVariable($status->get_id()))));
                }

                // delete statusses
                $success &= $this->delete_user_attempt_statuses($attempt->get_id());
                // delete attempt
                $success &= $this->delete_attempt($attempt->get_id());
            }
        }

        if ($success)
        {
            $factory = new ApplicationFactory(
                \Chamilo\Application\Weblcms\Tool\Action\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
        else
        {
            $this->redirect(Translation::get('error'), 1, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
    }
}
