<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

use Chamilo\Core\Reporting\ReportingData;

/**
 * Shows the options of the assessment open question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentHotspotQuestionOptionsBlock extends AssessmentQuestionOptionsBlock
{

    /**
     * Counts the data
     * 
     * @return ReportingData
     */
    public function count_data()
    {
        $reporting_data = new ReportingData();
        
        return $reporting_data;
    }
}