<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment\QuestionOptions;

/**
 * Shows the options of the assessment matrix question
 * 
 * @package application.weblcms.integration.reporting
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMatrixQuestionOptionsBlock extends AssessmentMatchingQuestionOptionsBlock
{

    /**
     * Checks if the match is correct
     * 
     * @param mixed $option
     * @param int $match
     *
     * @return bool
     */
    protected function is_correct_match($option, $match)
    {
        $correct_matches = $option->get_matches();
        if (! is_array($correct_matches))
        {
            $correct_matches = array($correct_matches);
        }
        
        return in_array($match, $correct_matches);
    }
}