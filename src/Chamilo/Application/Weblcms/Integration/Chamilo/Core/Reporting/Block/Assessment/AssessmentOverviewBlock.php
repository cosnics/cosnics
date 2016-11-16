<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of all assessment attempts in a
 *          course
 * @author Alexander Van Paemel
 */
class AssessmentOverviewBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('Name'),
                Translation::get('OfficialCode', null, \Chamilo\Core\User\Manager::context()),
                Translation::get('Title'),
                Translation::get('Date'),
                Translation::get('Score')));

        $course_id = $this->get_course_id();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt::class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt::PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));

        $attempts_result_set = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieves(
            AssessmentAttempt::class_name(),
            new DataClassRetrievesParameters($condition));

        $attempts = array();
        while ($attempt = $attempts_result_set->next_result())
        {
            $attempts[$attempt->get_user_id()][$attempt->get_assessment_id()][] = $attempt;
        }

        $count = 1;
        foreach ($attempts as $key => $user_attempts)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) $key);
            foreach ($user_attempts as $key => $pub_attempts)
            {
                $pub = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                    ContentObjectPublication::class_name(),
                    $key);

                if (! $pub instanceof ContentObjectPublication)
                {
                    continue;
                }

                $score = $this->get_score($pub_attempts);
                $date = DatetimeUtilities::format_locale_date(
                    Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' .
                         Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES),
                        $score['date']);
                $score = $score['score'];

                $passingPercentage = Configuration::getInstance()->get_setting(
                    array('Chamilo\Core\Admin', 'passing_percentage'));

                if ($score < $passingPercentage)
                {
                    $score = '<span style="color:red">' . $score . '</span>';
                }
                else
                {
                    $score = '<span style="color:green">' . $score . '</span>';
                }

                $reporting_data->add_category($count);

                $reporting_data->add_data_category_row($count, Translation::get('Name'), $user->get_fullname());

                $reporting_data->add_data_category_row(
                    $count,
                    Translation::get('OfficialCode', null, \Chamilo\Core\User\Manager::context()),
                    $user->get_official_code());

                $reporting_data->add_data_category_row(
                    $count,
                    Translation::get('Title', null, Utilities::COMMON_LIBRARIES),
                    $pub->get_content_object()->get_title());

                $reporting_data->add_data_category_row($count, Translation::get('Date'), $date);
                $reporting_data->add_data_category_row($count, Translation::get('Score'), $score);
                $count ++;
            }
        }

        $reporting_data->hide_categories();
        return $reporting_data;
    }

    private function get_score($attempts)
    {
        $score_type = (Request::post('sel')) ? Request::post('sel') : Request::get('sel');
        $score = array();

        switch ($score_type)
        {
            case self::SCORE_TYPE_AVG :
                foreach ($attempts as $attempt)
                {
                    $score['score'] += $attempt->get_total_score();
                }
                $score['score'] = number_format($score['score'] / count($attempts), 1);
                $score['date'] = $attempts[0]->get_start_time();
                return $score;
            case self::SCORE_TYPE_MIN :
                foreach ($attempts as $attempt)
                {
                    if (count($score) == 0 || $attempt->get_total_score() < $score['score'])
                    {
                        $score['score'] = $attempt->get_total_score();
                        $score['date'] = $attempt->get_start_time();
                    }
                }
                return $score;
            case self::SCORE_TYPE_MAX :
                foreach ($attempts as $attempt)
                {
                    if (count($score) == 0 || $attempt->get_total_score() > $score['score'])
                    {
                        $score['score'] = $attempt->get_total_score();
                        $score['date'] = $attempt->get_start_time();
                    }
                }
                return $score;
            case self::SCORE_TYPE_FIRST :
                foreach ($attempts as $attempt)
                {
                    if (count($score) == 0 || $attempt->get_start_time() < $score['date'])
                    {
                        $score['score'] = $attempt->get_total_score();
                        $score['date'] = $attempt->get_start_time();
                    }
                }
                return $score;
            case self::SCORE_TYPE_LAST :
                foreach ($attempts as $attempt)
                {
                    if (count($score) == 0 || $attempt->get_start_time() > $score['date'])
                    {
                        $score['score'] = $attempt->get_total_score();
                        $score['date'] = $attempt->get_start_time();
                    }
                }
                return $score;
            default :
                foreach ($attempts as $attempt)
                {
                    $score['score'] += $attempt->get_total_score();
                }
                $score['score'] = number_format($score['score'] / count($attempts), 1);
                $score['date'] = $attempts[0]->get_start_time();
                return $score;
        }
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
