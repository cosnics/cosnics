<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;

/**
 * Shows all the attempts for a single assessment
 * 
 * @package application\weblcms\integration\core\reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentAttemptsBlock extends AssessmentAttemptsUserBlock
{

    /**
     * Returns the rows for the reporting data
     * 
     * @return string[]
     */
    protected function get_reporting_data_rows()
    {
        return array_merge(
            array(Translation::get('Name'), Translation::get('OfficialCode')), 
            parent::get_reporting_data_rows());
    }

    /**
     * Adds additional data for the assessment attempt
     * 
     * @param AssessmentAttempt $assessment_attempt
     * @param int $counter
     * @param ReportingData $reporting_data
     */
    protected function add_additional_information_for_attempt($assessment_attempt, $counter, $reporting_data)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class_name(), 
            $assessment_attempt->get_user_id());
        
        $reporting_data->add_data_category_row($counter, Translation::get('Name'), $user->get_fullname());
        
        $reporting_data->add_data_category_row($counter, Translation::get('OfficialCode'), $user->get_official_code());
    }
}
