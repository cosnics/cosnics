<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assessment and the
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentUserInformationBlock extends AssessmentUsersBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        $categories = $this->get_assessment_information_headers();
        $categories = array_merge($categories, $this->get_user_reporting_info_headers());
        
        $reporting_data->set_categories($categories);
        
        $user_id = Request::get('users');
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), $user_id);
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id());
        
        $this->add_category_from_array(
            Translation::get('Details'), 
            $this->get_assessment_information($publication), 
            $reporting_data);
        
        $user_attempts = $this->calculate_user_attempt_summary_data();
        
        $reporting_info = $this->get_user_reporting_info($user, $user_attempts[$user->get_id()]);
        $this->add_category_from_array(Translation::get('Details'), $reporting_info, $reporting_data);
        
        $reporting_data->set_rows(array(Translation::get('Details')));
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    protected function get_assessment_attempts_condition()
    {
        $conditions = array();
        
        $conditions[] = parent::get_assessment_attempts_condition();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_USER_ID), 
            new StaticConditionVariable($this->get_user_id()));
        
        return new AndCondition($conditions);
    }
}
